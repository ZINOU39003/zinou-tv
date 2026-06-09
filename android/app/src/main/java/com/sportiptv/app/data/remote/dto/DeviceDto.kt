package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class DeviceDto(
    @SerialName("id") val id: String = "",
    @SerialName("device_name") val deviceName: String = "",
    @SerialName("device_model") val deviceModel: String = "",
    @SerialName("os_version") val osVersion: String = "",
    @SerialName("last_active") val lastActive: String = "",
    @SerialName("is_current") val isCurrent: Boolean = false
)
