package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class CategoryDto(
    val id: Long,
    val name: String,
    val name_ar: String,
    val slug: String,
    val icon: String? = null,
    val type: String = "content_type",
    val sort_order: Int,
    val is_active: Boolean,
    val channels_count: Int = 0,
    val packages_count: Int = 0
)
