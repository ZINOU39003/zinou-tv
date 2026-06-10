<?php

namespace App\Support;

class MediaUrl
{
    public static function resolve(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);

        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        if (str_starts_with($url, '/')) {
            return url($url);
        }

        if (preg_match('#^https?://(127\.0\.0\.1|localhost)(:\d+)?#i', $url)) {
            $path = parse_url($url, PHP_URL_PATH) ?: '';
            $query = parse_url($url, PHP_URL_QUERY);
            $resolved = url($path);

            return $query ? $resolved.'?'.$query : $resolved;
        }

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            return url('/'.ltrim($url, '/'));
        }

        return $url;
    }
}
