package com.sportiptv.app.util

import android.annotation.SuppressLint
import android.content.Context
import android.provider.Settings
import java.security.MessageDigest

object DeviceUtils {

    @SuppressLint("HardwareIds")
    fun getDeviceId(context: Context): String {
        return try {
            var androidId = Settings.Secure.getString(
                context.contentResolver,
                Settings.Secure.ANDROID_ID
            )
            
            // If ANDROID_ID is null or invalid, use a persistent fallback ID in SharedPreferences
            if (androidId.isNullOrEmpty() || androidId == "9774d56d682e549c") {
                val prefs = context.getSharedPreferences("DevicePrefs", Context.MODE_PRIVATE)
                androidId = prefs.getString("fallback_device_id", null)
                if (androidId == null) {
                    androidId = java.util.UUID.randomUUID().toString()
                    prefs.edit().putString("fallback_device_id", androidId).apply()
                }
            }
            
            // Return SHA-256 hash of Android ID for clean uniform device signature length
            val digest = MessageDigest.getInstance("SHA-256")
            val hash = digest.digest(androidId!!.toByteArray(Charsets.UTF_8))
            hash.joinToString("") { "%02x".format(it) }.take(16) // Return 16-character hex string
        } catch (e: Exception) {
            val prefs = context.getSharedPreferences("DevicePrefs", Context.MODE_PRIVATE)
            var id = prefs.getString("fallback_device_id", null)
            if (id == null) {
                id = java.util.UUID.randomUUID().toString()
                prefs.edit().putString("fallback_device_id", id).apply()
            }
            id.take(16)
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
