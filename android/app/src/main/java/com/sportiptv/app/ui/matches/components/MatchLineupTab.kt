package com.sportiptv.app.ui.matches.components

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyListScope
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil3.compose.AsyncImage
import androidx.compose.ui.text.style.TextAlign
import com.sportiptv.app.data.remote.dto.ScoresLineupPlayerDto
import com.sportiptv.app.ui.matches.MatchesViewModel
import com.sportiptv.app.ui.matches.awayLineupPlayers
import com.sportiptv.app.ui.matches.homeLineupPlayers
import com.sportiptv.app.ui.matches.playerPhotoUrl
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary
import com.sportiptv.app.ui.theme.TextMuted

@Composable
fun MatchLineupTab(viewModel: MatchesViewModel, isArabic: Boolean) {
    val matchDetail by viewModel.matchDetail.collectAsState()
    val matchLineup by viewModel.matchLineup.collectAsState()
    val isLoading by viewModel.matchLineupLoading.collectAsState()

    if (isLoading && matchDetail == null) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = Primary)
        }
        return
    }

    val game = matchLineup ?: matchDetail
    val homeLineups = game?.homeLineupPlayers().orEmpty()
    val awayLineups = game?.awayLineupPlayers().orEmpty()

    if (homeLineups.isEmpty() && awayLineups.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize().padding(32.dp), contentAlignment = Alignment.Center) {
            Text(
                text = if (isArabic) "التشكيلة غير متوفرة بعد" else "Lineups not available yet",
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
        if (homeLineups.isNotEmpty()) {
            item {
                Text(
                    text = game?.homeCompetitor?.name ?: (if (isArabic) "صاحب الأرض" else "Home Team"),
                    color = Primary,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(bottom = 12.dp)
                )
            }
            lineupSection(homeLineups, isArabic, isHome = true)
        }

        if (awayLineups.isNotEmpty()) {
            item {
                Spacer(modifier = Modifier.height(20.dp))
                Text(
                    text = game?.awayCompetitor?.name ?: (if (isArabic) "الضيف" else "Away Team"),
                    color = Primary,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(bottom = 12.dp)
                )
            }
            lineupSection(awayLineups, isArabic, isHome = false)
        }
    }
}

private fun LazyListScope.lineupSection(
    players: List<ScoresLineupPlayerDto>,
    isArabic: Boolean,
    isHome: Boolean
) {
    val starters = players.filter { it.status == 1 || it.status == null }
    val subs = players.filter { it.status == 2 }

    if (starters.isNotEmpty()) {
        item {
            Text(
                text = if (isArabic) "التشكيلة الأساسية" else "Starting XI",
                color = Color.White,
                fontSize = 14.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.padding(vertical = 6.dp)
            )
        }
        items(starters) { player ->
            PlayerRow(player = player)
            Spacer(modifier = Modifier.height(8.dp))
        }
    }

    if (subs.isNotEmpty()) {
        item {
            Text(
                text = if (isArabic) "الاحتياط" else "Substitutes",
                color = Color.White.copy(alpha = 0.7f),
                fontSize = 14.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.padding(vertical = 6.dp)
            )
        }
        items(subs) { player ->
            PlayerRow(player = player)
            Spacer(modifier = Modifier.height(8.dp))
        }
    }
}

@Composable
fun PlayerRow(player: ScoresLineupPlayerDto) {
    val photoUrl = playerPhotoUrl(player.id, player.athleteId, player.imageVersion)

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .background(BgSecondary, shape = RoundedCornerShape(10.dp))
            .padding(12.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        AsyncImage(
            model = photoUrl,
            contentDescription = player.name,
            modifier = Modifier
                .size(48.dp)
                .clip(CircleShape)
                .background(Color.White.copy(alpha = 0.05f)),
            contentScale = ContentScale.Crop
        )
        Spacer(modifier = Modifier.width(12.dp))
        Text(
            text = (player.jerseyNum ?: "").toString(),
            color = Primary,
            fontWeight = FontWeight.Bold,
            fontSize = 16.sp,
            modifier = Modifier.width(28.dp),
            textAlign = TextAlign.Center
        )
        Column(modifier = Modifier.weight(1f)) {
            Text(
                text = player.name ?: "",
                color = Color.White,
                fontWeight = FontWeight.Bold,
                fontSize = 15.sp
            )
            Text(
                text = player.position?.name ?: "",
                color = TextMuted,
                fontSize = 12.sp
            )
        }
    }
}
