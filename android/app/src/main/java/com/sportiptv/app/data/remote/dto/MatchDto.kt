package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class MatchDto(
    val id: Long,
    val tournament_id: Long,
    val tournament: TournamentDto? = null,
    val team_one_name: String,
    val team_one_name_ar: String? = null,
    val team_one_flag: String? = null,
    val team_two_name: String,
    val team_two_name_ar: String? = null,
    val team_two_flag: String? = null,
    val team_one_score: Int = 0,
    val team_two_score: Int = 0,
    val match_time: String = "",
    val match_date: String? = null,
    val is_live: Boolean = false,
    val is_world_cup: Boolean = false,
    val stream_url: String? = null,
    val scores_game_id: Long? = null,
    val channel_id: Long? = null,
    val is_active: Boolean = true,
    val sort_order: Int = 0,
    val match_details: kotlinx.serialization.json.JsonObject? = null
)
