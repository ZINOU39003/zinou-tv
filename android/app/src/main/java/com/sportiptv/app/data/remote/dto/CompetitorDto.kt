package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class CompetitorDetailResponse(
    val competitors: List<ScoresCompetitorDto>? = null,
    val competitions: List<ScoresCompetitionDto>? = null
)

@Serializable
data class CompetitorGamesResponse(
    val games: List<ScoresGameDto>? = null,
    val competitions: List<ScoresCompetitionDto>? = null
)

@Serializable
data class CompetitorSquadResponse(
    val competitorId: Long? = null,
    val sourceGameId: Long? = null,
    val members: List<ScoresMemberDto>? = null,
    val competitor: ScoresCompetitorDto? = null
)
