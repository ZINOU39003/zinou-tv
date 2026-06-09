package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class SubscriptionDto(
    val id: Long? = null,
    val code: String,
    val duration: String? = null,
    val status: String,
    val activated_at: String? = null,
    val expires_at: String? = null,
    val days_remaining: Int,
    val device: SubscriptionDeviceDto? = null
)

@Serializable
data class SubscriptionDeviceDto(
    val device_id: String,
    val device_name: String? = null,
    val device_model: String? = null,
    val last_active_at: String? = null
)

