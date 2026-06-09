package com.sportiptv.app.ui.team

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.material3.TabRowDefaults.tabIndicatorOffset
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import coil3.compose.AsyncImage
import com.sportiptv.app.data.cache.ScoresGameCache
import com.sportiptv.app.data.remote.dto.ScoresGameDto
import com.sportiptv.app.data.remote.dto.ScoresMemberDto
import com.sportiptv.app.ui.matches.*
import com.sportiptv.app.ui.theme.*

@Composable
fun TeamDetailsScreen(
    competitorId: Long,
    onBackClick: () -> Unit,
    onMatchClick: (Long) -> Unit,
    viewModel: TeamDetailsViewModel = hiltViewModel()
) {
    val competitor by viewModel.competitor.collectAsState()
    val recentGames by viewModel.recentGames.collectAsState()
    val squad by viewModel.squad.collectAsState()
    val isLoading by viewModel.isLoading.collectAsState()
    val isArabic = java.util.Locale.getDefault().language == "ar"

    var tab by remember { mutableIntStateOf(0) }
    val tabs = if (isArabic) listOf("المباريات", "القائمة") else listOf("Matches", "Squad")

    LaunchedEffect(competitorId) { viewModel.load(competitorId) }

    Column(Modifier.fillMaxSize().background(BgPrimary)) {
        Row(
            Modifier.fillMaxWidth().padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            IconButton(onClick = onBackClick) {
                Icon(Icons.AutoMirrored.Filled.ArrowBack, null, tint = Color.White)
            }
            Text(
                text = competitor?.name ?: if (isArabic) "المنتخب" else "Team",
                color = Color.White,
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.width(48.dp))
        }

        if (isLoading && competitor == null) {
            Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = Primary)
            }
            return
        }

        Column(
            Modifier.fillMaxWidth().padding(horizontal = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            AsyncImage(
                model = teamLogoUrl(competitor?.id, competitor?.imageVersion),
                contentDescription = null,
                modifier = Modifier.size(88.dp),
                contentScale = ContentScale.Fit
            )
            Spacer(Modifier.height(8.dp))
            Text(competitor?.name ?: "---", color = Color.White, fontSize = 20.sp, fontWeight = FontWeight.Black)
            competitor?.longName?.let {
                Text(it, color = TextMuted, fontSize = 12.sp)
            }
        }

        TabRow(
            selectedTabIndex = tab,
            containerColor = BgSecondary,
            contentColor = Primary,
            indicator = { positions ->
                TabRowDefaults.SecondaryIndicator(Modifier.tabIndicatorOffset(positions[tab]), color = Primary)
            }
        ) {
            tabs.forEachIndexed { i, title ->
                Tab(selected = tab == i, onClick = { tab = i }, text = { Text(title, fontWeight = FontWeight.Bold) })
            }
        }

        when (tab) {
            0 -> TeamMatchesTab(recentGames, isArabic, onMatchClick)
            1 -> TeamSquadTab(squad, isArabic)
        }
    }
}

@Composable
private fun TeamMatchesTab(games: List<ScoresGameDto>, isArabic: Boolean, onMatchClick: (Long) -> Unit) {
    if (games.isEmpty()) {
        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text(if (isArabic) "لا توجد مباريات" else "No matches", color = TextMuted)
        }
        return
    }
    LazyColumn(Modifier.fillMaxSize().padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
        items(games, key = { it.id }) { game ->
            Card(
                modifier = Modifier.fillMaxWidth().clickable {
                    ScoresGameCache.put(game)
                    onMatchClick(game.id)
                },
                colors = CardDefaults.cardColors(containerColor = BgSecondary),
                shape = RoundedCornerShape(12.dp)
            ) {
                Row(
                    Modifier.fillMaxWidth().padding(14.dp),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Column(Modifier.weight(1f)) {
                        Text(
                            "${game.homeCompetitor?.name ?: "-"} vs ${game.awayCompetitor?.name ?: "-"}",
                            color = Color.White,
                            fontSize = 13.sp,
                            fontWeight = FontWeight.SemiBold
                        )
                        Text(
                            game.competitionDisplayName ?: "",
                            color = TextMuted,
                            fontSize = 11.sp
                        )
                    }
                    Column(horizontalAlignment = Alignment.End) {
                        Text(game.displayScoreText(), color = Primary, fontWeight = FontWeight.Bold)
                        Text(
                            game.displayStatusText(isArabic),
                            color = if (game.isLiveMatch()) DangerColor else TextMuted,
                            fontSize = 10.sp
                        )
                    }
                }
            }
        }
    }
}

@Composable
private fun TeamSquadTab(squad: List<ScoresMemberDto>, isArabic: Boolean) {
    if (squad.isEmpty()) {
        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text(if (isArabic) "القائمة غير متوفرة" else "Squad unavailable", color = TextMuted)
        }
        return
    }
    LazyColumn(Modifier.fillMaxSize().padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
        items(squad, key = { it.id }) { player ->
            Row(
                Modifier
                    .fillMaxWidth()
                    .background(BgSecondary, RoundedCornerShape(10.dp))
                    .padding(12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                AsyncImage(
                    model = playerPhotoUrl(player.id, player.athleteId, player.imageVersion),
                    contentDescription = player.name,
                    modifier = Modifier.size(48.dp).clip(CircleShape),
                    contentScale = ContentScale.Crop
                )
                Spacer(Modifier.width(12.dp))
                Text(
                    (player.jerseyNum ?: "").toString(),
                    color = Primary,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.width(28.dp),
                    textAlign = TextAlign.Center
                )
                Column(Modifier.weight(1f)) {
                    Text(player.name ?: "", color = Color.White, fontWeight = FontWeight.Bold)
                    Text(player.position?.name ?: "", color = TextMuted, fontSize = 12.sp)
                }
            }
        }
    }
}
