package com.sportiptv.app.util

object Constants {
    // ngrok/duckdns tunnel for stable access
    const val BASE_URL = "http://zinoutv.duckdns.org:8000/api/"
    
    // Fallback static API key (must match X_API_KEY in Laravel .env)
    const val DEFAULT_API_KEY = "SportIptvDefaultApiKeySecret2026"

    // WhatsApp configuration for account purchase
    const val WHATSAPP_NUMBER = "213770000000"

    // Advertisement video URL played before channel streams
    const val AD_VIDEO_URL = "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4"

    // AdMob Interstitial Ad Unit ID (Google Test ID by default)
    const val ADMOB_INTERSTITIAL_AD_UNIT_ID = "ca-app-pub-3940256099942544/1033173712"
}
