package com.sportiptv.app.domain.model

data class License(
    val licenseKey: String = "",
    val status: String = "",
    val plan: String = "",
    val expiresAt: String = "",
    val activatedAt: String = "",
    val maxDevices: Int = 1,
    val activeDevices: Int = 0
)
