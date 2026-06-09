package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

import kotlinx.serialization.json.JsonArray

@Serializable
data class TournamentDto(
    val id: Long,
    val name: String,
    val name_ar: String? = null,
    val logo_url: String? = null,
    val is_active: Boolean = true,
    val sort_order: Int = 0,
    val standings: JsonArray? = null
)
