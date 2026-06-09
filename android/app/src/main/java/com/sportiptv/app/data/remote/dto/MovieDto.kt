package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class MovieDto(
    val id: Long,
    val title: String,
    val title_ar: String? = null,
    val poster_url: String? = null,
    val type: String = "movie",
    val stream_url: String? = null,
    val description: String? = null,
    val description_ar: String? = null,
    val year: Int? = null,
    val rating: Double? = null,
    val is_latest: Boolean = true,
    val is_active: Boolean = true,
    val sort_order: Int = 0
)
