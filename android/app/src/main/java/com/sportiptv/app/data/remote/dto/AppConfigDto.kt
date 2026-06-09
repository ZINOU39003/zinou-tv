package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class SubscriptionPackageDto(
    val id: String,
    val nameAr: String,
    val nameEn: String,
    val durationAr: String,
    val price: String,
    val features: List<String>,
    val isPopular: Boolean = false
)

@Serializable
data class AppConfigDto(
    val whatsapp_number: String,
    val packages: List<SubscriptionPackageDto>,
    val ads_enabled: Boolean,
    val admob_interstitial_ad_unit_id: String,
    val ad_video_url: String
)
