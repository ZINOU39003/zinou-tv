package com.sportiptv.app.data.remote.dto

import com.sportiptv.app.util.Constants
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
    val whatsapp_number: String = "",
    val packages: List<SubscriptionPackageDto> = emptyList(),
    val ads_enabled: Boolean = false,
    val admob_app_id: String = Constants.ADMOB_APP_ID,
    val admob_banner_ad_unit_id: String = Constants.ADMOB_BANNER_AD_UNIT_ID,
    val admob_interstitial_ad_unit_id: String = Constants.ADMOB_INTERSTITIAL_AD_UNIT_ID,
    val ad_video_url: String = "",
    val min_app_version: String = "1.0.0",
    val force_update: Boolean = false,
    val update_message: String = "",
    val latest_apk_url: String = "",
    val stream_ticker_text: String = "",
)
