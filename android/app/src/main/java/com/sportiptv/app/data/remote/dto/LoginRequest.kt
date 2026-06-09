package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class LoginRequest(
    val email: String,
    val password: String,
    val device_id: String,
    val device_name: String? = null,
    val device_model: String? = null,
    val android_version: String? = null,
    val app_version: String? = null
)
