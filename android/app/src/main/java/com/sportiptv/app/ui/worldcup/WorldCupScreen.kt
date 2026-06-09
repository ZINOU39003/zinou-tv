package com.sportiptv.app.ui.worldcup

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.LiveTv
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.SportsSoccer
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import coil3.compose.AsyncImage
import com.sportiptv.app.data.remote.dto.ScoresStandingRowDto
import com.sportiptv.app.data.remote.dto.WorldCupNewsDto
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.ui.matches.*
import com.sportiptv.app.ui.matches.components.DateSelector
import com.sportiptv.app.ui.theme.DangerColor
import com.sportiptv.app.ui.theme.TextMuted
import java.time.LocalDate

private val WcGold = Color(0xFFE5A93C)
private val WcDeep = Color(0xFF08040F)
private val WcCard = Color(0xFF1A0D2E)
private val WcBorder = Color(0xFF3F007F)

@Composable
fun WorldCupScreen(
    onBackClick: () -> Unit,
    onStreamClick: (String) -> Unit = {},
    onChannelClick: (Long) -> Unit = {},
    onMatchDetailsClick: (Long) -> Unit = {},
    onTeamClick: (Long) -> Unit = {},
    viewModel: WorldCupViewModel = hiltViewModel()
) {
    val matches by viewModel.matches.collectAsState()
    val selectedDate by viewModel.selectedDate.collectAsState()
    val news by viewModel.news.collectAsState()
    val standings by viewModel.scoreStandings.collectAsState()
    val channels by viewModel.broadcastChannels.collectAsState()
    val isLoading by viewModel.isLoading.collectAsState()
    val isArabic = java.util.Locale.getDefault().language == "ar"

    Column(Modifier.fillMaxSize().background(WcDeep)) {
        WcHeader(isArabic, onBackClick, matches.count { it.scoresGame.isLiveMatch() })

        DateSelector(
            selectedDate = selectedDate,
            onDateSelected = { viewModel.setDate(it) },
            isArabic = isArabic
        )

        if (isLoading && matches.isEmpty()) {
            Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = WcGold)
            }
        } else {
            LazyColumn(Modifier.fillMaxSize(), contentPadding = PaddingValues(bottom = 24.dp)) {
                if (channels.isNotEmpty()) {
                    item {
                        SectionTitle(if (isArabic) "القنوات الناقلة" else "Broadcast Channels")
                        LazyRow(
                            contentPadding = PaddingValues(horizontal = 16.dp),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            items(channels, key = { it.id }) { ch ->
                                BroadcastChannelChip(ch, isArabic) { onChannelClick(ch.id) }
                            }
                        }
                        Spacer(Modifier.height(12.dp))
                    }
                }

                item {
                    ScheduleTableHeader(isArabic)
                }

                if (matches.isEmpty()) {
                    item {
                        Text(
                            if (isArabic) "لا توجد مباريات في هذا التاريخ" else "No matches on this date",
                            color = TextMuted,
                            modifier = Modifier.padding(24.dp),
                            textAlign = TextAlign.Center
                        )
                    }
                } else {
                    items(matches, key = { it.scoresGame.id }) { item ->
                        WcScheduleRow(
                            item = item,
                            isArabic = isArabic,
                            onTeamClick = onTeamClick,
                            onDetails = { viewModel.openMatchDetails(item, onMatchDetailsClick) },
                            onWatch = {
                                viewModel.watchMatch(item, onStreamClick, onChannelClick, onMatchDetailsClick)
                            }
                        )
                        Spacer(Modifier.height(8.dp))
                    }
                }

                if (standings.isNotEmpty()) {
                    item {
                        Spacer(Modifier.height(12.dp))
                        SectionTitle(if (isArabic) "جدول المجموعات" else "Group Standings")
                    }
                    items(standings.take(20), key = { it.competitor?.id ?: it.position ?: 0 }) { row ->
                        StandingRow(row, isArabic)
                    }
                }

                if (news.isNotEmpty()) {
                    item {
                        Spacer(Modifier.height(12.dp))
                        SectionTitle(if (isArabic) "تحليلات الذكاء الاصطناعي" else "AI Analysis")
                    }
                    items(news.take(5), key = { it.id }) { article ->
                        NewsCard(article)
                        Spacer(Modifier.height(8.dp))
                    }
                }
            }
        }
    }
}

@Composable
private fun WcHeader(isArabic: Boolean, onBack: () -> Unit, liveCount: Int) {
    Box(
        Modifier
            .fillMaxWidth()
            .background(Brush.verticalGradient(listOf(Color(0xFF1E0A3C), WcDeep)))
            .padding(16.dp)
    ) {
        IconButton(onClick = onBack, modifier = Modifier.align(Alignment.CenterStart)) {
            Icon(Icons.AutoMirrored.Filled.ArrowBack, null, tint = Color.White)
        }
        Column(Modifier.align(Alignment.Center), horizontalAlignment = Alignment.CenterHorizontally) {
            Text("FIFA 2026", color = WcGold, fontSize = 12.sp, fontWeight = FontWeight.Bold)
            Text(if (isArabic) "كأس العالم" else "World Cup", color = Color.White, fontSize = 22.sp, fontWeight = FontWeight.Black)
            if (liveCount > 0) {
                Text(if (isArabic) "$liveCount مباشر" else "$liveCount live", color = DangerColor, fontSize = 11.sp)
            }
        }
        Icon(Icons.Default.SportsSoccer, null, tint = WcGold, modifier = Modifier.align(Alignment.CenterEnd).size(32.dp))
    }
}

@Composable
private fun SectionTitle(title: String) {
    Text(title, color = Color.White, fontSize = 16.sp, fontWeight = FontWeight.Bold, modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp))
}

@Composable
private fun ScheduleTableHeader(isArabic: Boolean) {
    Row(
        Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 6.dp)
            .background(WcCard, RoundedCornerShape(8.dp))
            .padding(horizontal = 12.dp, vertical = 8.dp)
    ) {
        Text(if (isArabic) "الوقت" else "Time", color = TextMuted, fontSize = 10.sp, modifier = Modifier.width(48.dp))
        Text(if (isArabic) "المباراة" else "Match", color = TextMuted, fontSize = 10.sp, modifier = Modifier.weight(1f))
        Text(if (isArabic) "النتيجة" else "Score", color = TextMuted, fontSize = 10.sp, modifier = Modifier.width(56.dp), textAlign = TextAlign.Center)
        Text(if (isArabic) "بث" else "Live", color = TextMuted, fontSize = 10.sp, modifier = Modifier.width(52.dp), textAlign = TextAlign.Center)
    }
}

@Composable
private fun WcScheduleRow(
    item: WorldCupMatchItem,
    isArabic: Boolean,
    onTeamClick: (Long) -> Unit,
    onDetails: () -> Unit,
    onWatch: () -> Unit
) {
    val game = item.scoresGame
    val isLive = game.isLiveMatch()
    val hasBroadcast = item.broadcast?.channelId != null || !item.broadcast?.streamUrl.isNullOrBlank()

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp)
            .clickable { onDetails() },
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = WcCard),
        border = androidx.compose.foundation.BorderStroke(1.dp, if (isLive) DangerColor.copy(0.4f) else WcBorder.copy(0.3f))
    ) {
        Row(
            Modifier.fillMaxWidth().padding(12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(Modifier.width(48.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                Text(formatStartTime(game.startTime, isArabic), color = Color.White, fontSize = 12.sp, fontWeight = FontWeight.Bold)
                if (isLive) {
                    Text(game.displayStatusText(isArabic), color = DangerColor, fontSize = 9.sp)
                }
            }

            Column(Modifier.weight(1f).padding(horizontal = 8.dp)) {
                Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.clickable {
                    game.homeCompetitor?.id?.let(onTeamClick)
                }) {
                    AsyncImage(teamLogoUrl(game.homeCompetitor?.id), null, Modifier.size(20.dp), contentScale = ContentScale.Fit)
                    Spacer(Modifier.width(6.dp))
                    Text(game.homeCompetitor?.name ?: "-", color = Color.White, fontSize = 12.sp, maxLines = 1, overflow = TextOverflow.Ellipsis)
                }
                Spacer(Modifier.height(4.dp))
                Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.clickable {
                    game.awayCompetitor?.id?.let(onTeamClick)
                }) {
                    AsyncImage(teamLogoUrl(game.awayCompetitor?.id), null, Modifier.size(20.dp), contentScale = ContentScale.Fit)
                    Spacer(Modifier.width(6.dp))
                    Text(game.awayCompetitor?.name ?: "-", color = Color.White, fontSize = 12.sp, maxLines = 1, overflow = TextOverflow.Ellipsis)
                }
                val tv = game.tvNetworks?.firstOrNull()?.name ?: item.broadcast?.channelName
                if (!tv.isNullOrBlank()) {
                    Text("📺 $tv", color = TextMuted, fontSize = 9.sp, maxLines = 1)
                }
            }

            Text(
                game.displayScoreText(),
                color = WcGold,
                fontWeight = FontWeight.Black,
                fontSize = 14.sp,
                modifier = Modifier.width(56.dp),
                textAlign = TextAlign.Center
            )

            if (isLive && hasBroadcast) {
                IconButton(onClick = onWatch, modifier = Modifier.size(48.dp)) {
                    Icon(Icons.Default.PlayArrow, if (isArabic) "بث مباشر" else "Watch", tint = WcGold, modifier = Modifier.size(28.dp))
                }
            } else {
                Spacer(Modifier.width(48.dp))
            }
        }
    }
}

@Composable
private fun BroadcastChannelChip(channel: Channel, isArabic: Boolean, onClick: () -> Unit) {
    Card(
        modifier = Modifier.width(100.dp).clickable { onClick() },
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = WcCard),
        border = androidx.compose.foundation.BorderStroke(1.dp, WcBorder)
    ) {
        Column(Modifier.padding(10.dp), horizontalAlignment = Alignment.CenterHorizontally) {
            if (!channel.logoUrl.isNullOrBlank()) {
                AsyncImage(channel.logoUrl, null, Modifier.size(36.dp).clip(CircleShape), contentScale = ContentScale.Fit)
            } else {
                Icon(Icons.Default.LiveTv, null, tint = WcGold, modifier = Modifier.size(36.dp))
            }
            Spacer(Modifier.height(6.dp))
            Text(
                if (isArabic) channel.nameAr ?: channel.name else channel.name,
                color = Color.White, fontSize = 10.sp, maxLines = 2, overflow = TextOverflow.Ellipsis, textAlign = TextAlign.Center
            )
        }
    }
}

@Composable
private fun StandingRow(row: ScoresStandingRowDto, isArabic: Boolean) {
    Row(
        Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 3.dp)
            .background(WcCard, RoundedCornerShape(8.dp)).padding(12.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text((row.position ?: 0).toString(), color = WcGold, modifier = Modifier.width(24.dp), fontWeight = FontWeight.Bold)
        AsyncImage(teamLogoUrl(row.competitor?.id), null, Modifier.size(24.dp), contentScale = ContentScale.Fit)
        Spacer(Modifier.width(8.dp))
        Text(row.competitor?.name ?: "---", color = Color.White, modifier = Modifier.weight(1f), fontSize = 13.sp)
        Text((row.points ?: 0).toString(), color = WcGold, fontWeight = FontWeight.Bold)
    }
}

@Composable
private fun NewsCard(article: WorldCupNewsDto) {
    Card(
        Modifier.fillMaxWidth().padding(horizontal = 16.dp),
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = WcCard)
    ) {
        Row(Modifier.padding(12.dp)) {
            AsyncImage(article.image_url, null, Modifier.size(64.dp).clip(RoundedCornerShape(8.dp)), contentScale = ContentScale.Crop)
            Spacer(Modifier.width(12.dp))
            Column {
                Text(article.title, color = Color.White, fontSize = 13.sp, fontWeight = FontWeight.Bold, maxLines = 2, overflow = TextOverflow.Ellipsis)
                Text(article.summary, color = TextMuted, fontSize = 11.sp, maxLines = 2, overflow = TextOverflow.Ellipsis)
            }
        }
    }
}
