package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class ScoresResponse(
    val games: List<ScoresGameDto>? = null,
    val competitions: List<ScoresCompetitionDto>? = null
)

@Serializable
data class ScoresGameDto(
    val id: Long,
    val competitionId: Long,
    val homeCompetitor: ScoresCompetitorDto? = null,
    val awayCompetitor: ScoresCompetitorDto? = null,
    val homeScore: Int? = null,
    val awayScore: Int? = null,
    @SerialName("statusGroup") val statusGroup: Int? = null,
    val statusId: Int? = null,
    @SerialName("gameTime") val gameMinute: Int? = null,
    val startTime: String? = null,
    val statusText: String? = null,
    val shortStatusText: String? = null,
    val competitionDisplayName: String? = null,
    val events: List<ScoresEventDto>? = null,
    val members: List<ScoresMemberDto>? = null,
    val venue: ScoresVenueDto? = null,
    val tvNetworks: List<ScoresTvNetworkDto>? = null
)

@Serializable
data class ScoresTvNetworkDto(
    val id: Long? = null,
    val name: String? = null
)

@Serializable
data class ScoresCompetitorDto(
    val id: Long,
    val name: String? = null,
    val longName: String? = null,
    val score: Int? = null,
    val color: String? = null,
    val imageVersion: Int? = null,
    val lineups: List<ScoresLineupPlayerDto>? = null
)

@Serializable
data class ScoresCompetitionDto(
    val id: Long,
    val name: String? = null,
    val popularityRank: Int? = null
)

@Serializable
data class ScoresEventDto(
    @SerialName("gameTime") val gameMinute: Int = 0,
    val eventTypeId: Int? = null,
    val eventType: ScoresEventTypeDto? = null,
    val competitorId: Long = 0,
    val playerId: Long? = null,
    val player: ScoresEventPlayerDto? = null,
    val player2: ScoresEventPlayerDto? = null,
    @SerialName("extraPlayers") val extraPlayerIds: List<Long>? = null
)

@Serializable
data class ScoresEventTypeDto(
    val id: Int,
    val name: String? = null
)

@Serializable
data class ScoresMemberDto(
    val id: Long,
    val name: String? = null,
    val shortName: String? = null,
    val competitorId: Long? = null,
    val athleteId: Long? = null,
    @SerialName("jerseyNumber") val jerseyNum: Int? = null,
    val status: Int? = null,
    val position: ScoresPlayerPositionDto? = null,
    val imageVersion: Int? = null
)

@Serializable
data class ScoresEventPlayerDto(
    val id: Long? = null,
    val name: String? = null
)

@Serializable
data class ScoresVenueDto(
    val name: String? = null
)

@Serializable
data class ScoresLineupPlayerDto(
    val id: Long,
    val name: String? = null,
    @SerialName("jerseyNumber") val jerseyNum: Int? = null,
    val athleteId: Long? = null,
    val status: Int? = null, // 1 = starter, 2 = sub
    val position: ScoresPlayerPositionDto? = null,
    val imageVersion: Int? = null
)

@Serializable
data class ScoresPlayerPositionDto(
    val name: String? = null
)

@Serializable
data class ScoresStatsResponse(
    val stats: List<ScoresStatItemDto>? = null
)

@Serializable
data class ScoresStatItemDto(
    val name: String? = null,
    val home: Int? = null,
    val away: Int? = null
)

@Serializable
data class ScoresStandingsResponse(
    val standing: ScoresStandingDto? = null,
    val standings: List<ScoresStandingDto>? = null
)

@Serializable
data class ScoresStandingDto(
    val rows: List<ScoresStandingRowDto>? = null
)

@Serializable
data class ScoresStandingRowDto(
    val position: Int? = null,
    val competitor: ScoresCompetitorDto? = null,
    val played: Int? = null,
    val wins: Int? = null,
    val draws: Int? = null,
    val losses: Int? = null,
    val `for`: Int? = null,
    val against: Int? = null,
    val points: Int? = null
)

@Serializable
data class ScoresH2HResponse(
    val headToHead: ScoresH2HDto? = null
)

@Serializable
data class ScoresH2HDto(
    val games: List<ScoresGameDto>? = null
)

@Serializable
data class ScoresGameDetailResponse(
    val game: ScoresGameDto? = null
)

@Serializable
data class ScoresLineupResponse(
    val game: ScoresGameDto? = null
)
