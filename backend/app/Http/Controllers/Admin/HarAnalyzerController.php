<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HarAnalyzerController extends Controller
{
    public function index()
    {
        $channels = Channel::where('is_active', true)->orderBy('name')->get();
        return view('admin.har.index', compact('channels'));
    }

    public function analyze(Request $request)
    {
        // Check for PHP file upload size limits or error codes
        if (isset($_FILES['har_file']) && $_FILES['har_file']['error'] !== UPLOAD_ERR_OK) {
            $errorCode = $_FILES['har_file']['error'];
            if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
                return back()->with('error', 'حجم الملف المرفوع كبير جداً ويتجاوز الحد المسموح به في إعدادات السيرفر. يرجى لصق محتويات الملف أو روابط البث مباشرة في مربع النص أدناه.');
            }
        }

        $content = '';

        if ($request->hasFile('har_file')) {
            $file = $request->file('har_file');
            if ($file->isValid()) {
                $content = file_get_contents($file->getRealPath());
            } else {
                return back()->with('error', 'المستند المرفوع غير صالح أو تالف. يرجى تجربة لصق النص مباشرة.');
            }
        } elseif ($request->filled('raw_text')) {
            $content = $request->input('raw_text');
        } else {
            return back()->with('error', 'الرجاء رفع ملف (HAR / Session) أو لصق نص يحتوي على روابط البث.');
        }

        if (empty(trim($content))) {
            return back()->with('error', 'المحتوى المرفوع أو الملصق فارغ.');
        }

        $extractedUrls = $this->extractUrlsFromContent($content);

        if (empty($extractedUrls)) {
            return back()->with('error', 'لم يتم العثور على أي روابط بث مباشر مطابقة (.m3u8, .mpd, .ts) في الملف أو النص المدخل.');
        }

        // Fetch all active channels from the database and decrypt their URLs to build a lookup map
        $encryptionService = resolve(EncryptionService::class);
        $dbChannels = Channel::all();
        $channelMap = [];
        foreach ($dbChannels as $ch) {
            $decryptedUrl = '';
            if (!empty($ch->stream_url)) {
                $decryptedUrl = $encryptionService->decrypt($ch->stream_url);
            }
            
            $channelMap[$ch->id] = [
                'id' => $ch->id,
                'name' => $ch->name,
                'name_ar' => $ch->name_ar,
                'url' => $decryptedUrl,
                'signature' => !empty($decryptedUrl) ? $this->getUrlSignature($decryptedUrl) : ''
            ];
        }

        $streams = [];
        foreach ($extractedUrls as $url) {
            $sig = $this->getUrlSignature($url);
            
            $matchType = 'new'; // 'exact', 'sig', 'new'
            $matchedChannelId = null;
            $matchedChannelName = '';
            
            // 1. Try exact URL match
            foreach ($channelMap as $chId => $chData) {
                if (!empty($chData['url']) && $chData['url'] === $url) {
                    $matchType = 'exact';
                    $matchedChannelId = $chId;
                    $matchedChannelName = $chData['name'] . ($chData['name_ar'] ? ' (' . $chData['name_ar'] . ')' : '');
                    break;
                }
            }
            
            // 2. Try signature/ID match if not exact
            if ($matchType === 'new' && !empty($sig)) {
                foreach ($channelMap as $chId => $chData) {
                    if (!empty($chData['signature']) && $chData['signature'] === $sig) {
                        $matchType = 'sig';
                        $matchedChannelId = $chId;
                        $matchedChannelName = $chData['name'] . ($chData['name_ar'] ? ' (' . $chData['name_ar'] . ')' : '');
                        break;
                    }
                }
            }
            
            // Set guessed name
            $guessedName = $this->guessChannelName($url);
            if (!empty($matchedChannelName)) {
                $guessedName = $matchedChannelName;
            }
            
            $streams[] = [
                'url' => $url,
                'guessed_name' => $guessedName,
                'type' => $this->getStreamType($url),
                'match_type' => $matchType,
                'matched_channel_id' => $matchedChannelId,
                'matched_channel_name' => $matchedChannelName
            ];
        }

        $channels = Channel::where('is_active', true)->orderBy('name')->get();
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.har.results', compact('streams', 'channels', 'categories'));
    }

    public function checkLink(Request $request)
    {
        $url = $request->query('url');
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'status' => 'inactive',
                'http_code' => 0,
                'metadata_name' => null
            ]);
        }

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 2.0, // 2 seconds timeout
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36\r\n" .
                            "Referer: https://x.com\r\n"
            ]
        ]);

        try {
            $handle = @fopen($url, 'r', false, $ctx);
            if ($handle) {
                $metaData = stream_get_meta_data($handle);
                $wrapperData = $metaData['wrapper_data'] ?? [];
                
                $httpCode = 0;
                foreach ($wrapperData as $headerLine) {
                    if (preg_match('/HTTP\/\d\.\d\s+(\d+)/i', $headerLine, $matches)) {
                        $httpCode = (int)$matches[1];
                        break;
                    }
                }
                
                $content = @fread($handle, 8192);
                @fclose($handle);
                
                $isWorking = ($httpCode >= 200 && $httpCode < 400);
                
                $metadataName = null;
                if ($isWorking && !empty($content)) {
                    if (preg_match('/NAME="([^"]+)"/i', $content, $matches)) {
                        $metadataName = trim($matches[1]);
                    } elseif (preg_match('/#EXTINF:[^,\n]*,([^\n\r]+)/i', $content, $matches)) {
                        $name = trim($matches[1]);
                        if (!empty($name) && !str_starts_with($name, '#')) {
                            $metadataName = $name;
                        }
                    }
                }
                
                return response()->json([
                    'status' => $isWorking ? 'active' : 'inactive',
                    'http_code' => $httpCode,
                    'metadata_name' => $metadataName
                ]);
            }
        } catch (\Exception $e) {
            // Ignore & fallback
        }
        
        // Fallback using cURL
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_RANGE, '0-8192');
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_REFERER, 'https://x.com');
            
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $isWorking = ($httpCode >= 200 && $httpCode < 400);
            $metadataName = null;
            
            if ($isWorking && !empty($content)) {
                if (preg_match('/NAME="([^"]+)"/i', $content, $matches)) {
                    $metadataName = trim($matches[1]);
                } elseif (preg_match('/#EXTINF:[^,\n]*,([^\n\r]+)/i', $content, $matches)) {
                    $name = trim($matches[1]);
                    if (!empty($name) && !str_starts_with($name, '#')) {
                        $metadataName = $name;
                    }
                }
            }
            
            return response()->json([
                'status' => $isWorking ? 'active' : 'inactive',
                'http_code' => $httpCode,
                'metadata_name' => $metadataName
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'inactive',
                'http_code' => 0,
                'metadata_name' => null
            ]);
        }
    }

    public function distribute(Request $request)
    {
        $distributions = $request->input('distributions', []);
        $encryptionService = resolve(EncryptionService::class);
        $updatedCount = 0;

        foreach ($distributions as $channelId => $url) {
            if (empty($url) || empty($channelId)) continue;

            $channel = Channel::find($channelId);
            if ($channel) {
                // Encrypt the URL before saving it
                $channel->stream_url = $encryptionService->encrypt($url);
                $channel->save();
                $updatedCount++;
            }
        }

        return redirect()->route('admin.har.index')->with('success', "تم تحديث روابط $updatedCount قنوات بنجاح!");
    }

    public function player(Request $request)
    {
        $url = $request->query('url');
        $name = $request->query('name', 'بث غير معروف');
        $logo = $request->query('logo');
        
        if (empty($url)) {
            abort(400, 'رابط البث غير موجود.');
        }

        $type = $this->getStreamType($url);
        
        return view('admin.har.player', compact('url', 'name', 'type', 'logo'));
    }

    public function quickDistribute(Request $request)
    {
        $channelId = $request->input('channel_id');
        $url = $request->input('url');
        $mode = $request->input('mode'); // 'primary', 'backup', 'server'
        $serverName = $request->input('server_name', 'سيرفر جديد');

        if (empty($channelId) || empty($url) || empty($mode)) {
            return response()->json(['success' => false, 'message' => 'الرجاء توفير جميع الحقول المطلوبة.'], 400);
        }

        $channel = Channel::find($channelId);
        if (!$channel) {
            return response()->json(['success' => false, 'message' => 'القناة غير موجودة.'], 404);
        }

        $encryptionService = resolve(EncryptionService::class);
        $encryptedUrl = $encryptionService->encrypt($url);

        try {
            if ($mode === 'primary') {
                $channel->stream_url = $encryptedUrl;
                $channel->save();
                return response()->json([
                    'success' => true,
                    'message' => "تم تعيين البث الرئيسي لقناة {$channel->name} بنجاح!",
                    'channel_id' => $channel->id,
                    'channel_name' => $channel->name
                ]);
            } elseif ($mode === 'backup') {
                $channel->backup_url = $encryptedUrl;
                $channel->save();
                return response()->json([
                    'success' => true,
                    'message' => "تم تعيين البث الاحتياطي لقناة {$channel->name} بنجاح!",
                    'channel_id' => $channel->id,
                    'channel_name' => $channel->name
                ]);
            } elseif ($mode === 'server') {
                // Insert new server in channel_servers
                \Illuminate\Support\Facades\DB::table('channel_servers')->insert([
                    'channel_id' => $channel->id,
                    'name' => $serverName,
                    'stream_url' => $encryptedUrl,
                    'stream_type' => $this->getStreamType($url) === 'DASH / MPD' ? 'mpd' : 'm3u8',
                    'quality' => $channel->quality ?? 'HD',
                    'is_active' => 1,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "تمت إضافة السيرفر الجديد ({$serverName}) لقناة {$channel->name} بنجاح!",
                    'channel_id' => $channel->id,
                    'channel_name' => $channel->name
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'خطأ أثناء الحفظ: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => false, 'message' => 'نوع التوزيع غير مدعوم.'], 400);
    }

    public function createChannelAjax(Request $request)
    {
        $name = $request->input('name');
        $nameAr = $request->input('name_ar');
        $categoryId = $request->input('category_id');
        $logoUrl = $request->input('logo_url');
        $quality = $request->input('quality', 'HD');
        $url = $request->input('url');

        if (empty($name) || empty($categoryId) || empty($url)) {
            return response()->json(['success' => false, 'message' => 'الرجاء ملء الحقول الإجبارية (الاسم، التصنيف، رابط البث).'], 400);
        }

        $encryptionService = resolve(EncryptionService::class);
        $encryptedUrl = $encryptionService->encrypt($url);

        try {
            $channel = new Channel();
            $channel->name = $name;
            $channel->name_ar = $nameAr;
            $channel->category_id = $categoryId;
            $channel->logo_url = $logoUrl;
            $channel->quality = $quality;
            $channel->stream_url = $encryptedUrl;
            $channel->stream_type = $this->getStreamType($url) === 'DASH / MPD' ? 'mpd' : 'm3u8';
            $channel->is_active = 1;
            $channel->sort_order = 1;
            $channel->save();

            return response()->json([
                'success' => true,
                'channel' => [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'name_ar' => $channel->name_ar,
                    'logo_url' => $channel->logo_url
                ],
                'message' => "تم إنشاء القناة الجديدة ({$channel->name}) وتعيين البث لها بنجاح!"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'خطأ أثناء إنشاء القناة: ' . $e->getMessage()], 500);
        }
    }

    private function extractUrlsFromContent(string $content): array
    {
        $urls = [];

        // 1. Try parsing as JSON (works for .har, .chlsj, etc.)
        $jsonData = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
            // Check if it is a Charles JSON Session (an array where entries have 'host' and 'path')
            $isCharlesSession = false;
            if (!empty($jsonData)) {
                $firstItem = reset($jsonData);
                if (is_array($firstItem) && isset($firstItem['host']) && isset($firstItem['path'])) {
                    $isCharlesSession = true;
                }
            }

            if ($isCharlesSession) {
                foreach ($jsonData as $entry) {
                    if (is_array($entry) && isset($entry['host']) && isset($entry['path'])) {
                        $scheme = strtolower($entry['scheme'] ?? $entry['protocol'] ?? 'http');
                        if (str_starts_with($scheme, 'http')) {
                            $parts = explode('/', $scheme);
                            $scheme = $parts[0];
                        } else {
                            $scheme = 'http';
                        }
                        $url = $scheme . '://' . $entry['host'] . $entry['path'];
                        if (!empty($entry['query'])) {
                            $url .= '?' . $entry['query'];
                        }
                        $urls[] = $url;
                    }
                }
            } else {
                $this->findUrlsInJson($jsonData, $urls);
            }
        }

        // 2. If no URLs found through JSON, or if JSON parsing failed, run regex extraction
        if (empty($urls)) {
            // Match URLs allowing backslashes (JSON escaped) and convert later
            preg_match_all('#https?://[^\s"\'{}[\]\^<>`\(\)]+#i', $content, $matches);
            if (!empty($matches[0])) {
                $urls = $matches[0];
            }
        }

        // 3. Clean and filter URLs
        $streamingUrls = [];
        $patterns = [
            '/\.m3u8/i',
            '/\.mpd/i',
            '/\.ts/i',
            '/manifest/i',
            '/hdnts=/i',
            '/token=/i',
        ];

        foreach ($urls as $url) {
            // Replace JSON escaped slashes
            $url = str_replace('\/', '/', $url);
            // Trim trailing punctuation common in JSON/text formatting
            $url = rtrim(trim($url), '",;)]}>\\');

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $isStream = false;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url)) {
                    $isStream = true;
                    break;
                }
            }

            if ($isStream) {
                $streamingUrls[] = $url;
            }
        }

        return array_values(array_unique($streamingUrls));
    }

    private function findUrlsInJson($data, &$urls)
    {
        if (is_string($data)) {
            if (str_starts_with($data, 'http://') || str_starts_with($data, 'https://') || 
                str_starts_with($data, 'http:\\/') || str_starts_with($data, 'https:\\/')) {
                $urls[] = $data;
            }
        } elseif (is_array($data)) {
            foreach ($data as $value) {
                $this->findUrlsInJson($value, $urls);
            }
        }
    }

    private function getUrlSignature(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $path = trim(strtolower($path), '/');
        
        // Extract any numeric segment of at least 4 digits
        preg_match_all('/\b\d{4,}\b/', $path, $matches);
        if (!empty($matches[0])) {
            $longest = '';
            foreach ($matches[0] as $match) {
                if (strlen($match) > strlen($longest)) {
                    $longest = $match;
                }
            }
            if (!empty($longest)) {
                return $longest;
            }
        }
        
        return $path;
    }

    private function guessChannelName(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $segments = explode('/', trim($path, '/'));
        
        // Find segments that look like channel names
        foreach (array_reverse($segments) as $segment) {
            if (preg_match('/(bein|sports|ssc|mbc|rotana|aljazeera|alkass|news|live|tv)/i', $segment)) {
                $clean = str_replace(['-', '_', '.', '1080p', '720p', 'hd', 'sd'], ' ', $segment);
                return ucwords(trim($clean));
            }
        }

        // Check query string
        $query = parse_url($url, PHP_URL_QUERY) ?? '';
        parse_str($query, $queryParams);
        foreach ($queryParams as $key => $value) {
            if (is_string($value) && preg_match('/(channel|stream|id|name)/i', $key)) {
                return ucwords(str_replace(['-', '_'], ' ', $value));
            }
        }

        return 'بث مكتشف (Guessed Stream)';
    }

    public function streamProxy(Request $request)
    {
        $url = $request->query('url');
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response('Invalid URL', 400);
        }

        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? 'http';
        $host = $parsed['host'] ?? '';
        $path = $parsed['path'] ?? '';
        
        $lastSlash = strrpos($path, '/');
        $folderPath = ($lastSlash !== false) ? substr($path, 0, $lastSlash + 1) : '/';
        $baseDir = $scheme . '://' . $host . $folderPath;
        
        $isManifest = str_contains(strtolower($path), '.m3u8') || str_contains(strtolower($path), '.mpd');

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
            
            // Replicate authentic streaming request headers
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',
                'Referer: https://x.com',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
                if ($isManifest) {
                    if (str_contains(strtolower($path), '.m3u8')) {
                        // HLS manifest rewrite: rewrite all links to go through this stream proxy
                        $lines = explode("\n", $response);
                        $newLines = [];
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) {
                                $newLines[] = '';
                                continue;
                            }
                            
                            if (str_starts_with($line, '#')) {
                                // Resolve and rewrite key or media playlist URIs
                                if (preg_match('/URI="([^"]+)"/', $line, $matches)) {
                                    $relativeUri = $matches[1];
                                    $absoluteUri = $this->resolveAbsoluteUrl($relativeUri, $baseDir, $scheme, $host);
                                    $proxiedUri = route('admin.har.stream-proxy') . '?url=' . urlencode($absoluteUri);
                                    $line = str_replace($relativeUri, $proxiedUri, $line);
                                }
                                $newLines[] = $line;
                            } else {
                                $absoluteUri = $this->resolveAbsoluteUrl($line, $baseDir, $scheme, $host);
                                $proxiedUri = route('admin.har.stream-proxy') . '?url=' . urlencode($absoluteUri);
                                $newLines[] = $proxiedUri;
                            }
                        }
                        $response = implode("\n", $newLines);
                    }
                    
                    return response($response, 200, [
                        'Content-Type' => $contentType ?: 'application/vnd.apple.mpegurl',
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                        'Access-Control-Allow-Headers' => '*',
                    ]);
                } else {
                    // Direct binary segments (.ts, .m4s, etc.)
                    return response($response, 200, [
                        'Content-Type' => $contentType ?: 'video/mp2t',
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                        'Access-Control-Allow-Headers' => '*',
                    ]);
                }
            } else {
                return response("Failed to fetch stream source. HTTP status: $httpCode", $httpCode ?: 502);
            }
        } catch (\Exception $e) {
            return response("Proxy error: " . $e->getMessage(), 500);
        }
    }

    private function resolveAbsoluteUrl($uri, $baseDir, $scheme, $host)
    {
        if (preg_match('/^https?:\/\//i', $uri)) {
            return $uri;
        }
        
        if (str_starts_with($uri, '//')) {
            return $scheme . ':' . $uri;
        }
        
        if (str_starts_with($uri, '/')) {
            return $scheme . '://' . $host . $uri;
        }
        
        return $baseDir . $uri;
    }

    private function getStreamType(string $url): string
    {
        if (str_contains(strtolower($url), '.m3u8')) return 'HLS / M3U8';
        if (str_contains(strtolower($url), '.mpd')) return 'DASH / MPD';
        if (str_contains(strtolower($url), '.ts')) return 'MPEG-TS';
        return 'أخرى (Other)';
    }
}
