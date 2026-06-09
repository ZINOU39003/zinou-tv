package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class WorldCupAiDto(
    val news: List<WorldCupNewsDto> = emptyList(),
    val predictions: List<MatchPredictionDto> = emptyList(),
    val standings: List<GroupStandingDto> = emptyList()
)

@Serializable
data class WorldCupNewsDto(
    val id: Long,
    val title: String,
    val summary: String,
    val content: String,
    val category: String,
    val image_url: String? = null,
    val read_time: String = "3 min read",
    val date: String = ""
)

@Serializable
data class MatchPredictionDto(
    val match_id: Long,
    val win_probability_team_one: Int,
    val win_probability_team_two: Int,
    val draw_probability: Int,
    val tactical_analysis: String,
    val key_players: String,
    val expected_score: String,
    val ai_verdict: String
)

@Serializable
data class GroupStandingDto(
    val group_name: String,
    val teams: List<TeamStandingDto> = emptyList()
)

@Serializable
data class TeamStandingDto(
    val name: String,
    val name_ar: String? = null,
    val flag_url: String? = null,
    val played: Int,
    val won: Int,
    val drawn: Int,
    val lost: Int,
    val goals_for: Int,
    val goals_against: Int,
    val points: Int
)
