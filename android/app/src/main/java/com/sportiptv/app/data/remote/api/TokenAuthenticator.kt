package com.sportiptv.app.data.remote.api

import com.sportiptv.app.data.local.prefs.SecurePreferences
import com.sportiptv.app.data.remote.dto.ApiResponse
import com.sportiptv.app.data.remote.dto.LoginResponse
import com.sportiptv.app.util.Constants
import kotlinx.serialization.json.Json
import okhttp3.Authenticator
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import okhttp3.Response
import okhttp3.Route

class TokenAuthenticator(
    private val securePreferences: SecurePreferences,
    private val json: Json
) : Authenticator {

    override fun authenticate(route: Route?, response: Response): Request? {
        // If we already tried refreshing once and still got 401, give up to prevent infinite loops
        if (responseCount(response) >= 3) {
            return null
        }

        synchronized(this) {
            val token = securePreferences.getToken() ?: return null
            val currentHeader = response.request.header("Authorization")
            
            // If the request token has changed since this response was received, retry with the new token
            if (currentHeader != "Bearer $token") {
                return response.request.newBuilder()
                    .header("Authorization", "Bearer $token")
                    .build()
            }

            // Sync call to refresh token
            val refreshedToken = refreshAccessToken()

            return if (refreshedToken != null) {
                response.request.newBuilder()
                    .header("Authorization", "Bearer $refreshedToken")
                    .build()
            } else {
                null
            }
        }
    }

    private fun refreshAccessToken(): String? {
        val client = OkHttpClient.Builder().build()
        val apiKey = securePreferences.getApiKey() ?: Constants.DEFAULT_API_KEY
        
        val request = Request.Builder()
            .url("${Constants.BASE_URL}refresh")
            .post("".toRequestBody("application/json".toMediaType()))
            .addHeader("X-API-Key", apiKey)
            .apply {
                securePreferences.getDeviceId()?.let { deviceId ->
                    addHeader("X-Device-ID", deviceId)
                }
                securePreferences.getToken()?.let { token ->
                    addHeader("Authorization", "Bearer $token")
                }
            }
            .build()

        return try {
            client.newCall(request).execute().use { response ->
                if (response.isSuccessful) {
                    val bodyString = response.body?.string() ?: return null
                    val apiResponse = json.decodeFromString<ApiResponse<LoginResponse>>(bodyString)
                    val newToken = apiResponse.data?.token
                    if (newToken != null) {
                        securePreferences.saveToken(newToken)
                        newToken
                    } else {
                        null
                    }
                } else {
                    // Refresh failed, clear tokens so user logs back in
                    securePreferences.clear()
                    null
                }
            }
        } catch (e: Exception) {
            null
        }
    }

    private fun responseCount(response: Response): Int {
        var result = 1
        var priorResponse = response.priorResponse
        while (priorResponse != null) {
            result++
            priorResponse = priorResponse.priorResponse
        }
        return result
    }
}
