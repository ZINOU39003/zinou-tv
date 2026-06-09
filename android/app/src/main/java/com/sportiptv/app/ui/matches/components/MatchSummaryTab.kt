package com.sportiptv.app.ui.matches.components

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.text.style.TextAlign
import com.sportiptv.app.data.remote.dto.ScoresEventDto
import com.sportiptv.app.data.remote.dto.ScoresMemberDto
import com.sportiptv.app.ui.matches.MatchesViewModel
import com.sportiptv.app.ui.matches.resolvedEventTypeId
import com.sportiptv.app.ui.matches.resolvedPlayer2Name
import com.sportiptv.app.ui.matches.resolvedPlayerName
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary
import com.sportiptv.app.ui.theme.TextMuted

@Composable
fun MatchSummaryTab(viewModel: MatchesViewModel, isArabic: Boolean) {
    val matchDetail by viewModel.matchDetail.collectAsState()
    val isLoading by viewModel.matchDetailLoading.collectAsState()

    if (isLoading) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = Primary)
        }
        return
    }

    val match = matchDetail
    val members = match?.members
    val events = match?.events?.sortedBy { it.gameMinute } ?: emptyList()

    if (events.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize().padding(32.dp), contentAlignment = Alignment.Center) {
            Text(
                text = if (isArabic) "لا توجد أحداث مسجلة لهذه المباراة" else "No events recorded for this match yet",
                color = TextMuted,
                fontSize = 16.sp
            )
        }
        return
    }

    LazyColumn(
        modifier = Modifier.fillMaxSize().padding(16.dp),
        contentPadding = PaddingValues(bottom = 32.dp)
    ) {
        item {
            Text(
                text = if (isArabic) "أحداث المباراة" else "Match Events",
                color = Color.White,
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.padding(bottom = 16.dp)
            )
        }

        items(events) { event ->
            val isHome = event.competitorId == match?.homeCompetitor?.id
            EventTimelineRow(event = event, isHome = isHome, members = members)
            Spacer(modifier = Modifier.height(10.dp))
        }
    }
}

@Composable
fun EventTimelineRow(
    event: ScoresEventDto,
    isHome: Boolean,
    members: List<ScoresMemberDto>? = null
) {
    val eventTypeId = event.resolvedEventTypeId()
    val icon = when (eventTypeId) {
        1 -> "⚽"
        2 -> "⚽ (OG)"
        3 -> "🟨"
        4 -> "🟥"
        5 -> "🔄"
        6 -> "⚽ (PEN)"
        7 -> "❌"
        8 -> "🟨🟥"
        else -> event.eventType?.name ?: "📌"
    }

    val playerName = event.resolvedPlayerName(members)
    val player2Name = event.resolvedPlayer2Name(members)

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .background(BgSecondary, shape = RoundedCornerShape(10.dp))
            .padding(12.dp),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = if (isHome) Arrangement.Start else Arrangement.End
    ) {
        if (isHome) {
            Text(
                text = "${event.gameMinute}'",
                color = Primary,
                fontWeight = FontWeight.Bold,
                fontSize = 15.sp,
                modifier = Modifier.width(36.dp)
            )
            Text(text = icon, fontSize = 18.sp, modifier = Modifier.padding(horizontal = 8.dp))
            Column {
                if (playerName.isNotBlank()) {
                    Text(text = playerName, color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                }
                if (player2Name.isNotBlank()) {
                    Text(text = "↪ $player2Name", color = TextMuted, fontSize = 12.sp)
                }
            }
        } else {
            Column(horizontalAlignment = Alignment.End) {
                if (playerName.isNotBlank()) {
                    Text(text = playerName, color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp, textAlign = TextAlign.End)
                }
                if (player2Name.isNotBlank()) {
                    Text(text = "↪ $player2Name", color = TextMuted, fontSize = 12.sp, textAlign = TextAlign.End)
                }
            }
            Text(text = icon, fontSize = 18.sp, modifier = Modifier.padding(horizontal = 8.dp))
            Text(
                text = "${event.gameMinute}'",
                color = Primary,
                fontWeight = FontWeight.Bold,
                fontSize = 15.sp,
                modifier = Modifier.width(36.dp),
                textAlign = TextAlign.End
            )
        }
    }
}
