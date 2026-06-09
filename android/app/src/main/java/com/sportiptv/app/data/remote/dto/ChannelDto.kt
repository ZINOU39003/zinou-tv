package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class ChannelDto(
    val id: Long,
    val name: String,
    val name_ar: String? = null,
    val category_id: Long,
    val category_name: String? = null,
    val category_name_ar: String? = null,
    val package_id: Long? = null,
    val package_name: String? = null,
    val package_name_ar: String? = null,
    val logo_url: String? = null,
    val stream_url: String,
    val stream_type: String,
    val quality: String,
    val backup_url: String? = null,
    val country: String? = null,
    val language: String? = null,
    val continent: String? = null,
    val sort_order: Int,
    val is_active: Boolean,
    val drm_license_url: String? = null,
    val drm_headers: String? = null,
    val servers: List<ChannelServerDto> = emptyList()
)
