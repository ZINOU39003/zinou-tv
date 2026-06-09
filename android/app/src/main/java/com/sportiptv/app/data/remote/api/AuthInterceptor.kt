package com.sportiptv.app.data.remote.api

import com.sportiptv.app.data.local.prefs.SecurePreferences
import com.sportiptv.app.util.Constants
import okhttp3.Interceptor
import okhttp3.Response

class AuthInterceptor(
    private val securePreferences: SecurePreferences
) : Interceptor {

    override fun intercept(chain: Interceptor.Chain): Response {
        val request = chain.request()
        val builder = request.newBuilder()

        // Attach X-API-Key (read from prefs or use default static fallback)
        val apiKey = securePreferences.getApiKey() ?: Constants.DEFAULT_API_KEY
        builder.addHeader("X-API-Key", apiKey)

        // Attach X-Device-ID (read from prefs)
        securePreferences.getDeviceId()?.let { deviceId ->
            builder.addHeader("X-Device-ID", deviceId)
        }

        // Attach JWT Auth token if present
        securePreferences.getToken()?.let { token ->
            builder.addHeader("Authorization", "Bearer $token")
        }

        // Add standard accept header
        builder.addHeader("Accept", "application/json")
        builder.addHeader("X-Timezone", java.util.TimeZone.getDefault().id)
        builder.addHeader("ngrok-skip-browser-warning", "true")
        builder.addHeader("Bypass-Tunnel-Reminder", "true")

        return chain.proceed(builder.build())
    }
}
