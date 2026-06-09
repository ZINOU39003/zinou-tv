package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class PackageDto(
    val id: Long,
    val category_id: Long,
    val name: String,
    val name_ar: String? = null,
    val slug: String,
    val logo_url: String? = null,
    val sort_order: Int,
    val is_active: Boolean,
    val channels_count: Int = 0
)
