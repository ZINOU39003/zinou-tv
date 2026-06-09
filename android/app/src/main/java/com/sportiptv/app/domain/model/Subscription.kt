package com.sportiptv.app.domain.model

data class Subscription(
    val code: String,
    val duration: String?,
    val status: String,
    val activatedAt: String?,
    val expiresAt: String?,
    val daysRemaining: Int,
    val boundDeviceId: String?,
    val boundDeviceName: String?
)
