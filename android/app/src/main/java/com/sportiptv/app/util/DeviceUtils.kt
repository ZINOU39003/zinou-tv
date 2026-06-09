package com.sportiptv.app.util

import android.annotation.SuppressLint
import android.content.Context
import android.provider.Settings
import java.security.MessageDigest

object DeviceUtils {

    @SuppressLint("HardwareIds")
    fun getDeviceId(context: Context): String {
        return try {
            val androidId = Settings.Secure.getString(
                context.contentResolver,
                Settings.Secure.ANDROID_ID
            ) ?: "fallback_iptv_id_${System.currentTimeMillis()}"
            
            // Return SHA-256 hash of Android ID for clean uniform device signature length
            val digest = MessageDigest.getInstance("SHA-256")
            val hash = digest.digest(androidId.toByteArray(Charsets.UTF_8))
            hash.joinToString("") { "%02x".format(it) }.take(16) // Return 16-character hex string
        } catch (e: Exception) {
            "fallback_device_16_chars"
        }
    }

    fun getDeviceName(): String {
        val manufacturer = android.os.Build.MANUFACTURER
        val model = android.os.Build.MODEL
        return if (model.startsWith(manufacturer)) {
            model.replaceFirstChar { it.uppercase() }
        } else {
            "${manufacturer.replaceFirstChar { it.uppercase() }} $model"
        }
    }

    fun getDeviceModel(): String = android.os.Build.MODEL

    fun getAndroidVersion(): String = android.os.Build.VERSION.RELEASE
}
