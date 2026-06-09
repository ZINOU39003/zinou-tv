package com.sportiptv.app.ui.matches

import com.sportiptv.app.data.remote.dto.ScoresEventDto
import com.sportiptv.app.data.remote.dto.ScoresGameDto
import com.sportiptv.app.data.remote.dto.ScoresLineupPlayerDto
import com.sportiptv.app.data.remote.dto.ScoresMemberDto
import java.time.OffsetDateTime
import java.time.ZoneId
import java.time.format.DateTimeFormatter

enum class MatchStatusKind { SCHEDULED, LIVE, HALF_TIME, ENDED, POSTPONED, UNKNOWN }

fun ScoresGameDto.matchStatus(): MatchStatusKind {
    val group = statusGroup ?: return MatchStatusKind.UNKNOWN
    val minute = gameMinute ?: -1
    return when (group) {
        2 -> if (minute > 0) MatchStatusKind.LIVE else MatchStatusKind.SCHEDULED
        3 -> MatchStatusKind.LIVE
        4 -> MatchStatusKind.ENDED
        9 -> MatchStatusKind.POSTPONED
        else -> MatchStatusKind.UNKNOWN
    }
}

fun ScoresGameDto.isLiveMatch(): Boolean = matchStatus() == MatchStatusKind.LIVE

fun ScoresGameDto.homeDisplayScore(): String = formatScore(homeScore ?: homeCompetitor?.score)

fun ScoresGameDto.awayDisplayScore(): String = formatScore(awayScore ?: awayCompetitor?.score)

private fun formatScore(raw: Int?): String {
    if (raw == null || raw < 0) return "-"
    return raw.toString()
}

fun ScoresGameDto.displayScoreText(): String {
    return when (matchStatus()) {
        MatchStatusKind.SCHEDULED -> "VS"
        else -> "${homeDisplayScore()} - ${awayDisplayScore()}"
    }
}

fun ScoresGameDto.displayStatusText(isArabic: Boolean): String {
    val minute = gameMinute ?: -1
    return when (matchStatus()) {
        MatchStatusKind.SCHEDULED -> formatStartTime(startTime, isArabic)
        MatchStatusKind.LIVE -> {
            val label = if (minute > 0) "${minute}'" else (shortStatusText ?: statusText ?: "")
            label.ifBlank { if (isArabic) "مباشر" else "LIVE" }
        }
        MatchStatusKind.ENDED -> shortStatusText ?: statusText ?: if (isArabic) "انتهت" else "FT"
        MatchStatusKind.HALF_TIME -> if (isArabic) "استراحة" else "HT"
        MatchStatusKind.POSTPONED -> if (isArabic) "مؤجلة" else "Postponed"
        MatchStatusKind.UNKNOWN -> formatStartTime(startTime, isArabic)
    }
}

fun formatStartTime(isoString: String?, isArabic: Boolean): String {
    if (isoString.isNullOrBlank()) return "--:--"
    return try {
        val offsetDateTime = OffsetDateTime.parse(isoString)
        val localTime = offsetDateTime.atZoneSameInstant(ZoneId.systemDefault())
        localTime.format(DateTimeFormatter.ofPattern("HH:mm"))
    } catch (_: Exception) {
        try {
            val instant = java.time.Instant.parse(isoString)
            instant.atZone(ZoneId.systemDefault())
                .format(DateTimeFormatter.ofPattern("HH:mm"))
        } catch (_: Exception) {
            if (isoString.length >= 16) isoString.substring(11, 16) else "--:--"
        }
    }
}

fun ScoresEventDto.resolvedEventTypeId(): Int = eventType?.id ?: eventTypeId ?: 0

fun ScoresEventDto.resolvedPlayerName(members: List<ScoresMemberDto>?): String {
    player?.name?.let { if (it.isNotBlank()) return it }
    val id = playerId ?: return ""
    return members?.firstOrNull { it.id == id }?.name ?: ""
}

fun ScoresEventDto.resolvedPlayer2Name(members: List<ScoresMemberDto>?): String {
    player2?.name?.let { if (it.isNotBlank()) return it }
    val id = extraPlayerIds?.firstOrNull() ?: return ""
    return members?.firstOrNull { it.id == id }?.name ?: ""
}

data class CompetitionOption(
    val id: Long?,
    val nameAr: String,
    val nameEn: String
)

const val WORLD_CUP_COMPETITION_ID = 5930L

fun playerPhotoUrl(playerId: Long, athleteId: Long? = null, imageVersion: Int? = null): String {
    val id = athleteId ?: playerId
    val version = imageVersion ?: 1
    return "https://imagecache.365scores.com/image/upload/f_png,w_120,h_120,c_limit/v${version}/Athletes/$id"
}

fun teamLogoUrl(competitorId: Long?, imageVersion: Int? = null): String {
    if (competitorId == null) return ""
    val version = imageVersion ?: 1
    return "https://imagecache.365scores.com/image/upload/f_png,w_120,h_120,c_limit/v${version}/Competitors/$competitorId"
}

fun membersToLineup(members: List<ScoresMemberDto>?, competitorId: Long?): List<ScoresLineupPlayerDto> {
    if (competitorId == null || members.isNullOrEmpty()) return emptyList()
    return members
        .filter { it.competitorId == competitorId }
        .map { m ->
            ScoresLineupPlayerDto(
                id = m.id,
                name = m.name,
                jerseyNum = m.jerseyNum,
                athleteId = m.athleteId,
                status = m.status ?: 1,
                position = m.position,
                imageVersion = m.imageVersion
            )
        }
        .sortedBy { it.jerseyNum ?: 99 }
}

fun ScoresGameDto.homeLineupPlayers(): List<ScoresLineupPlayerDto> {
    val fromLineups = homeCompetitor?.lineups.orEmpty()
    if (fromLineups.isNotEmpty()) return fromLineups
    return membersToLineup(members, homeCompetitor?.id)
}

fun ScoresGameDto.awayLineupPlayers(): List<ScoresLineupPlayerDto> {
    val fromLineups = awayCompetitor?.lineups.orEmpty()
    if (fromLineups.isNotEmpty()) return fromLineups
    return membersToLineup(members, awayCompetitor?.id)
}

val COMPETITION_OPTIONS = listOf(
    CompetitionOption(null, "الكل", "All"),
    CompetitionOption(5930L, "كأس العالم 2026", "FIFA World Cup 2026"),
    CompetitionOption(572L, "دوري أبطال أوروبا", "Champions League"),
    CompetitionOption(573L, "الدوري الأوروبي", "Europa League"),
    CompetitionOption(5L, "الدوري الإنجليزي", "Premier League"),
    CompetitionOption(12L, "الدوري الإسباني", "La Liga"),
    CompetitionOption(11L, "الدوري الألماني", "Bundesliga"),
    CompetitionOption(9L, "الدوري الإيطالي", "Serie A"),
    CompetitionOption(8L, "الدوري الفرنسي", "Ligue 1"),
    CompetitionOption(82L, "دوري روشن", "Saudi Pro League"),
    CompetitionOption(564L, "كأس أمم أفريقيا", "AFCON"),
    CompetitionOption(6196L, "كأس آسيا", "Asian Cup"),
    CompetitionOption(584L, "الدوري المصري", "Egyptian League"),
    CompetitionOption(171L, "الدوري الجزائري", "Algerian League"),
    CompetitionOption(406L, "الدوري التونسي", "Tunisian League"),
    CompetitionOption(557L, "الدوري المغربي", "Moroccan League"),
    CompetitionOption(570L, "مباريات دولية", "International Friendlies")
)
