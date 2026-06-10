package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class HeartbeatRequest(
    val device_id: String,
    val channel_id: Long,
    val device_name: String? = null,
    val app_version: String? = null,
)
