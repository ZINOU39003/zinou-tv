<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StreamProxyController extends Controller
{
    /**
     * Proxy stream manifest and segments to bypass IP locks and account session limits.
     */
    public function proxy(Request $request, $channel_id, $any = '')
    {
        // 1. Find the channel
        $channel = Channel::find($channel_id);
        if (!$channel) {
            $channel = Channel::where('name', $channel_id)->first();
        }

        if (!$channel) {
            return response('Channel not found', 404);
        }

        // 2. Decrypt the original stream URL
        $encryptionService = resolve(EncryptionService::class);
        $originalUrl = $encryptionService->decrypt($channel->stream_url);

        if (empty($originalUrl)) {
            return response('Stream URL is empty or invalid', 400);
        }

        // 3. Parse original URL to extract base directory and query parameters
        $parsed = parse_url($originalUrl);
        $path = $parsed['path'] ?? '';
        
        $lastSlash = strrpos($path, '/');
        if ($lastSlash !== false) {
            $folderPath = substr($path, 0, $lastSlash + 1);
        } else {
            $folderPath = '/';
        }

        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $baseDirectory = $scheme . '://' . $host . $folderPath;
        $originalQuery = $parsed['query'] ?? '';

        // 4. Construct target URL
        if (empty($any)) {
            $any = substr($path, $lastSlash + 1);
        }

        $isManifest = str_ends_with(strtolower($any), '.mpd') || 
                      str_ends_with(strtolower($any), '.m3u8') || 
                      str_contains(strtolower($any), 'manifest');

        if ($isManifest) {
            // Manifest uses the original full path
            $targetUrl = $baseDirectory . $any;
        } else {
            // Segments use a different base path than the manifest!
            // Manifest path: /variant/v1/dai-preroll-prod/Content/Channel/.../DASH/playlist_ha.mpd
            // Segment path:  /Content/Channel/.../DASH/stream_01/segment.m4v
            // We need to extract the /Content/Channel/.../DASH/ part and build from there
            if (preg_match('#(Content/Channel/[^/]+/DASH/)#', $path, $matches)) {
                $segmentBase = $scheme . '://' . $host . '/' . $matches[1];
                $targetUrl = $segmentBase . $any;
            } else {
                // Fallback to original base directory
                $targetUrl = $baseDirectory . $any;
            }
        }

        // Merge original query parameters (containing the token)
        $requestQuery = $request->getQueryString();
        if ($originalQuery) {
            $targetUrl .= '?' . $originalQuery;
            if ($requestQuery) {
                $targetUrl .= '&' . $requestQuery;
            }
        } elseif ($requestQuery) {
            $targetUrl .= '?' . $requestQuery;
        }

        // If it is a segment request, redirect directly using 302 Found response!
        // This avoids server bandwidth saturation and lets client stream directly from CDN.
        if (!$isManifest) {
            // Check if client requested to relay the stream through our server instead of redirecting
            if ($request->query('relay')) {
                return response()->stream(function() use ($targetUrl) {
                    $ctx = stream_context_create([
                        'http' => [
                            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
                            'timeout' => 15
                        ]
                    ]);
                    $handle = @fopen($targetUrl, 'rb', false, $ctx);
                    if ($handle) {
                        while (!feof($handle)) {
                            echo fread($handle, 16384); // Pipe in 16KB blocks
                            flush();
                        }
                        fclose($handle);
                    }
                }, 200, [
                    'Content-Type' => 'video/mp2t',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                    'Access-Control-Allow-Headers' => '*',
                ]);
            }

            // Check if the token is old and Chrome is likely in the middle of a reload
            $tokenTimestamp = Cache::get('channel_token_time_' . $channel_id, 0);
            $tokenAge = time() - $tokenTimestamp;
            
            // If the token is older than 70 seconds (assuming Tampermonkey refreshes every 80 seconds),
            // wait up to 8 seconds for Chrome to complete the reload and update the DB with a new token
            if ($tokenAge > 70) {
                Log::info("Segment request detected old token (age: {$tokenAge}s) on channel {$channel_id}. Waiting for reload...");
                $retryCount = 0;
                $maxWait = 8;
                while ($retryCount < $maxWait) {
                    sleep(1);
                    $channel = Channel::find($channel_id);
                    if ($channel) {
                        $originalUrl = $encryptionService->decrypt($channel->stream_url);
                        $latestTimestamp = Cache::get('channel_token_time_' . $channel->id, 0);
                        if ($latestTimestamp > $tokenTimestamp) {
                            Log::info("Fresh token detected during segment wait! Resuming redirect with new token.");
                            
                            // Reconstruct the CDN URL with the fresh token parameters
                            $parsed = parse_url($originalUrl);
                            $originalQuery = $parsed['query'] ?? '';
                            $path = $parsed['path'] ?? '';
                            
                            $lastSlash = strrpos($path, '/');
                            if ($lastSlash !== false) {
                                $folderPath = substr($path, 0, $lastSlash + 1);
                            } else {
                                $folderPath = '/';
                            }
                            $scheme = $parsed['scheme'] ?? 'https';
                            $host = $parsed['host'] ?? '';
                            $baseDirectory = $scheme . '://' . $host . $folderPath;

                            if (preg_match('#(Content/Channel/[^/]+/DASH/)#', $path, $matches)) {
                                $segmentBase = $scheme . '://' . $host . '/' . $matches[1];
                                $targetUrl = $segmentBase . $any;
                            } else {
                                $targetUrl = $baseDirectory . $any;
                            }

                            if ($originalQuery) {
                                $targetUrl .= '?' . $originalQuery;
                                if ($requestQuery) {
                                    $targetUrl .= '&' . $requestQuery;
                                }
                            } elseif ($requestQuery) {
                                $targetUrl .= '?' . $requestQuery;
                            }
                            break;
                        }
                    }
                    $retryCount++;
                }
            }

            return redirect()->away($targetUrl, 302)->withHeaders([
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => '*',
            ]);
        }

        // 5. Cache Strategy for manifest file only
        $cacheDuration = 2; // Manifests cached for 2 seconds

        $cleanPath = parse_url($targetUrl, PHP_URL_PATH);
        $cacheKey = 'stream_proxy_' . $channel_id . '_' . md5($cleanPath);

        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            $content = base64_decode($cachedData['content']);
            $headers = $cachedData['headers'];
        } else {
            $retryCount = 0;
            $maxRetries = 6;
            $currentUrlToFetch = $targetUrl;
            $success = false;
            $statusCode = 200;

            while ($retryCount < $maxRetries) {
                try {
                    $response = Http::withoutVerifying()->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Origin' => 'https://www.tod.tv',
                        'Referer' => 'https://www.tod.tv/',
                    ])->timeout(8)->get($currentUrlToFetch);

                    $statusCode = $response->status();

                    if ($response->successful()) {
                        $content = $response->body();
                        
                        // Extract content type and headers from the successful response
                        $contentType = $response->header('Content-Type') ?: 'application/dash+xml';
                        $headers = [
                            'Content-Type' => $contentType,
                            'Access-Control-Allow-Origin' => '*',
                            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                            'Access-Control-Allow-Headers' => '*',
                        ];

                        $success = true;
                        break;
                    }

                    if ($statusCode === 403) {
                        Log::warning("Proxy got 403 for manifest on channel {$channel_id}. Waiting for scraper token update... (Retry {$retryCount}/{$maxRetries})");
                        sleep(1);

                        // Reload channel from DB to check for updated token
                        $channel = Channel::find($channel_id);
                        if ($channel) {
                            $newOriginalUrl = $encryptionService->decrypt($channel->stream_url);
                            if (!empty($newOriginalUrl)) {
                                $newParsed = parse_url($newOriginalUrl);
                                $newQuery = $newParsed['query'] ?? '';

                                // Reconstruct URL with the new query/token
                                $parsedCurrent = parse_url($currentUrlToFetch);
                                $currentPath = $parsedCurrent['path'] ?? '';
                                $currentScheme = $parsedCurrent['scheme'] ?? 'https';
                                $currentHost = $parsedCurrent['host'] ?? '';

                                $currentUrlToFetch = $currentScheme . '://' . $currentHost . $currentPath;
                                if (!empty($newQuery)) {
                                    $currentUrlToFetch .= '?' . $newQuery;
                                }
                            }
                        }

                        $retryCount++;
                        continue;
                    }

                    // For any other non-successful status (e.g. 500, 404), do not retry
                    Log::warning("Proxy manifest fetch failed with status {$statusCode} for channel {$channel_id}", [
                        'targetUrl' => $currentUrlToFetch
                    ]);
                    return response('Failed to fetch manifest from CDN: ' . $statusCode, $statusCode);

                } catch (\Exception $e) {
                    Log::warning("Proxy manifest exception during fetch (Retry {$retryCount}): " . $e->getMessage());
                    if ($retryCount < $maxRetries - 1) {
                        sleep(1);
                        $retryCount++;
                        continue;
                    }
                    Log::error("Proxy manifest final exception for channel {$channel_id}: " . $e->getMessage());
                    return response('Error fetching manifest: ' . $e->getMessage(), 500);
                }
            }

            if (!$success) {
                Log::error("Proxy manifest final failure (403) after {$maxRetries} retries for channel {$channel_id}");
                return response('Stream token expired. Refresh the TOD TV tab.', 403);
            }

            // 6. CRITICAL: Rewrite MPD manifest to ensure all segment URLs go through our proxy
            $content = $this->rewriteManifest($content, $channel_id, $scheme, $host, $folderPath);

            Cache::put($cacheKey, [
                'content' => base64_encode($content),
                'headers' => $headers
            ], $cacheDuration);
        }

        return response($content, 200, $headers);
    }

    /**
     * Rewrite the DASH MPD manifest to ensure all segment URLs go through our proxy.
     * 
     * This is CRITICAL because:
     * - Akamai manifests often contain <BaseURL> elements pointing to the CDN directly
     * - ExoPlayer resolves segment paths against <BaseURL> if present
     * - Without rewriting, segments bypass our proxy and hit the local server at /Content/... (404)
     * - OR they hit Akamai directly, which fails due to IP/session restrictions
     */
    private function rewriteManifest(string $content, $channelId, string $scheme, string $host, string $folderPath): string
    {
        // Remove all <BaseURL> elements that point to the CDN host
        // This forces ExoPlayer to resolve segment URLs relative to the proxy manifest URL
        $content = preg_replace(
            '/<BaseURL>[^<]*' . preg_quote($host, '/') . '[^<]*<\/BaseURL>/i',
            '',
            $content
        );
        
        // Also remove any <BaseURL> with absolute paths starting with /
        // e.g. <BaseURL>/variant/v1/dai-preroll-prod/Content/...</BaseURL>
        $content = preg_replace(
            '/<BaseURL>\/[^<]+<\/BaseURL>/i',
            '',
            $content
        );

        // Also remove any <BaseURL> with full https URLs
        $content = preg_replace(
            '/<BaseURL>https?:\/\/[^<]+<\/BaseURL>/i',
            '',
            $content
        );

        // Handle SegmentTemplate media/initialization attributes that use absolute paths
        // Convert absolute paths like "/Content/Channel/.../stream_01/$Number$.m4s" to relative "stream_01/$Number$.m4s"
        $dashFolder = basename(rtrim($folderPath, '/'));
        
        // Replace absolute paths in media="..." attributes
        // e.g. media="/variant/v1/.../DASH/stream_01/$Number$.m4s" -> media="stream_01/$Number$.m4s"
        $escapedFolder = preg_quote($folderPath, '/');
        $content = preg_replace(
            '/media="' . $escapedFolder . '([^"]+)"/i',
            'media="$1"',
            $content
        );
        
        // Same for initialization="..." attributes
        $content = preg_replace(
            '/initialization="' . $escapedFolder . '([^"]+)"/i',
            'initialization="$1"',
            $content
        );

        // CRITICAL: Rewrite relative paths with ../ traversal that escape the proxy directory
        // e.g. media="../../../../../../../Content/Channel/.../DASH/$RepresentationID$/Segment-$Time$.m4v"
        // -> media="$RepresentationID$/Segment-$Time$.m4v"
        // The ../ traversal goes up from the manifest URL path and resolves to /Content/Channel/...
        // which doesn't match our proxy route. We extract just the part after /DASH/ which is relative.
        $content = preg_replace(
            '/media="(?:\.\.\/)+[^"]*\/DASH\/([^"]+)"/i',
            'media="$1"',
            $content
        );
        
        $content = preg_replace(
            '/initialization="(?:\.\.\/)+[^"]*\/DASH\/([^"]+)"/i',
            'initialization="$1"',
            $content
        );

        // CRITICAL: Remove Widevine and PlayReady ContentProtection elements
        // This forces ExoPlayer to use our ClearKey DRM session manager instead of
        // trying to contact Widevine/PlayReady license servers (which require paid licenses)
        
        // Remove Widevine ContentProtection (UUID: edef8ba9-79d6-4ace-a3c8-27dcd51d21ed)
        $content = preg_replace(
            '/<ContentProtection\s+schemeIdUri="urn:uuid:edef8ba9[^"]*"[^>]*>.*?<\/ContentProtection>\s*/si',
            '',
            $content
        );
        
        // Remove PlayReady ContentProtection (UUID: 9a04f079-9840-4286-ab92-e65be0885f95)
        $content = preg_replace(
            '/<ContentProtection\s+schemeIdUri="urn:uuid:9a04f079[^"]*"[^>]*>.*?<\/ContentProtection>\s*/si',
            '',
            $content
        );

        // Keep the basic CENC mp4protection element (urn:mpeg:dash:mp4protection:2011)
        // This tells ExoPlayer the content is encrypted and provides the KID
        // Our ClearKey session manager will handle the decryption

        return $content;
    }

    /**
     * Catch-all proxy for segment requests that come with absolute CDN paths.
     * Routes like /Content/Channel/svc-spo-hd-38-dt/DASH/stream_07/segment.m4s
     * get proxied to the Akamai CDN with proper authentication token.
     */
    public function proxyCdnPath(Request $request, $any)
    {
        // Extract channel identifier from the path (e.g. "svc-spo-hd-38-dt")
        // Path format: Content/Channel/{channel-slug}/DASH/{rest}
        $parts = explode('/', $any);
        
        if (count($parts) < 4) {
            return response('Invalid CDN path', 400);
        }

        $channelSlug = $parts[1] ?? ''; // e.g. "svc-spo-hd-38-dt"
        
        // Find the channel model matching this slug to check reload gap delay
        $encryptionService = resolve(EncryptionService::class);
        $channel = null;
        $channels = Channel::where('is_active', true)->get();
        foreach ($channels as $c) {
            if (empty($c->stream_url)) continue;
            $decrypted = $encryptionService->decrypt($c->stream_url);
            if (empty($decrypted)) continue;
            if (str_contains($decrypted, $channelSlug)) {
                $channel = $c;
                break;
            }
        }

        // The CDN host for TOD TV
        $cdnHost = 'todtv-live-ent-prod.akamaized.net';
        $targetUrl = 'https://' . $cdnHost . '/variant/v1/dai-preroll-prod/Content/' . $any;

        if ($channel) {
            $tokenTimestamp = Cache::get('channel_token_time_' . $channel->id, 0);
            $tokenAge = time() - $tokenTimestamp;

            // If the token is older than 70 seconds, wait for reload gap
            if ($tokenAge > 70) {
                Log::info("CDN path segment request detected old token (age: {$tokenAge}s) for slug {$channelSlug}. Waiting for reload...");
                $retryCount = 0;
                $maxWait = 8;
                while ($retryCount < $maxWait) {
                    sleep(1);
                    $channel = Channel::find($channel->id);
                    $latestTimestamp = Cache::get('channel_token_time_' . $channel->id, 0);
                    if ($latestTimestamp > $tokenTimestamp) {
                        Log::info("Fresh token detected during CDN path segment wait! Resuming redirect.");
                        break;
                    }
                    $retryCount++;
                }
            }

            $decrypted = $encryptionService->decrypt($channel->stream_url);
            $parsed = parse_url($decrypted);
            $token = $parsed['query'] ?? null;
        } else {
            $token = null;
        }

        if ($token) {
            $targetUrl .= '?' . $token;
        }

        // Add any incoming query parameters
        $requestQuery = $request->getQueryString();
        if ($requestQuery) {
            $targetUrl .= ($token ? '&' : '?') . $requestQuery;
        }

        // Redirect directly to the CDN segment URL via 302 Found response
        return redirect()->away($targetUrl, 302)->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ]);
    }

    /**
     * Find the authentication token for a given CDN channel path.
     */
    private function findTokenForPath(string $channelSlug): ?string
    {
        // Search for channels that have this slug in their stream URL
        $encryptionService = resolve(EncryptionService::class);
        
        $channels = Channel::where('is_active', true)->get();
        foreach ($channels as $channel) {
            if (empty($channel->stream_url)) continue;
            
            $decrypted = $encryptionService->decrypt($channel->stream_url);
            if (empty($decrypted)) continue;
            
            if (str_contains($decrypted, $channelSlug)) {
                $parsed = parse_url($decrypted);
                return $parsed['query'] ?? null;
            }
        }
        
        return null;
    }
}
