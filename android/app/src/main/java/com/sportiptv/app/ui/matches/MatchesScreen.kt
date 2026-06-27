package com.sportiptv.app.ui.matches

import androidx.compose.animation.core.*
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.SportsSoccer
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.alpha
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
import com.sportiptv.app.data.cache.ScoresGameCache
import com.sportiptv.app.data.remote.dto.ScoresGameDto
import com.sportiptv.app.ui.matches.components.DateSelector
import com.sportiptv.app.ui.theme.*
import java.time.LocalDate

@Composable
fun MatchesScreen(
    viewModel: MatchesViewModel = hiltViewModel(),
    onMatchClick: (Long) -> Unit = {}
) {
    val scoresResponse by viewModel.scoresResponse.collectAsState()
    val isLoading by viewModel.isLoading.collectAsState()
    val errorMessage by viewModel.errorMessage.collectAsState()
    val currentFilter by viewModel.filter.collectAsState()
    val selectedCompetitionId by viewModel.selectedCompetitionId.collectAsState()

    val isArabic = java.util.Locale.getDefault().language == "ar"

    val competitionsMap = remember(scoresResponse) {
        scoresResponse?.competitions?.associateBy { it.id } ?: emptyMap()
    }

    val groupedGames = remember(scoresResponse, currentFilter, selectedCompetitionId) {
        var games = scoresResponse?.games ?: emptyList()
        if (selectedCompetitionId != null) {
            games = games.filter { it.competitionId == selectedCompetitionId }
        }
        val filtered = when (currentFilter) {
            "live" -> games.filter { it.isLiveMatch() }
            else -> games
        }
        filtered.groupBy { it.competitionId }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0A0A0A))
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp, vertical = 16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(Icons.Default.SportsSoccer, contentDescription = null, tint = Primary, modifier = Modifier.size(28.dp))
                Spacer(modifier = Modifier.width(10.dp))
                Text(
                    text = if (isArabic) "مركز المباريات" else "Match Center",
                    color = Color.White,
                    fontSize = 22.sp,
                    fontWeight = FontWeight.ExtraBold
                )
            }
            val liveCount = scoresResponse?.games?.count { it.isLiveMatch() } ?: 0
            if (liveCount > 0) {
                Box(
                    modifier = Modifier
                        .background(DangerColor, shape = RoundedCornerShape(6.dp))
                        .padding(horizontal = 10.dp, vertical = 4.dp)
                ) {
                    Text(
                        text = if (isArabic) "$liveCount مباشر" else "$liveCount LIVE",
                        color = Color.White,
                        fontSize = 11.sp,
                        fontWeight = FontWeight.ExtraBold
                    )
                }
            }
        }

        CompetitionSelector(
            selectedId = selectedCompetitionId,
            isArabic = isArabic,
            onSelected = { viewModel.setCompetitionFilter(it) }
        )

        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp, vertical = 4.dp),
            horizontalArrangement = Arrangement.spacedBy(10.dp)
        ) {
            FilterChipItem(
                label = if (isArabic) "الكل" else "All",
                isSelected = currentFilter == null,
                onClick = { viewModel.setFilter(null) }
            )
            FilterChipItem(
                label = if (isArabic) "مباشر" else "Live",
                isSelected = currentFilter == "live",
                onClick = { viewModel.setFilter("live") }
            )
        }

        var selectedDate by remember { mutableStateOf(LocalDate.now()) }
        DateSelector(
            selectedDate = selectedDate,
            onDateSelected = {
                selectedDate = it
                viewModel.setDateFilter(it.toString())
            },
            isArabic = isArabic
        )

        when {
            isLoading -> {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = Primary)
                }
            }
            errorMessage != null -> {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(
                            text = if (isArabic) "حدث خطأ" else "An error occurred",
                            color = DangerColor,
                            fontSize = 16.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(text = errorMessage ?: "", color = TextMuted, fontSize = 13.sp)
                        Spacer(modifier = Modifier.height(16.dp))
                        Button(
                            onClick = { viewModel.refresh() },
                            colors = ButtonDefaults.buttonColors(containerColor = Primary),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Text(
                                text = if (isArabic) "إعادة المحاولة" else "Retry",
                                color = Color.Black,
                                fontWeight = FontWeight.Bold
                            )
                        }
                    }
                }
            }
            groupedGames.isEmpty() -> {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Icon(Icons.Default.SportsSoccer, contentDescription = null, tint = TextMuted, modifier = Modifier.size(64.dp))
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = if (isArabic) "لا توجد مباريات حالياً" else "No matches available",
                            color = TextMuted,
                            fontSize = 16.sp,
                            fontWeight = FontWeight.Medium
                        )
                    }
                }
            }
            else -> {
                LazyColumn(
                    modifier = Modifier.fillMaxSize(),
                    contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp)
                ) {
                    groupedGames.forEach { (compId, compGames) ->
                        val comp = competitionsMap[compId]
                        val compName = comp?.name ?: compGames.firstOrNull()?.competitionDisplayName
                        item(key = "header_$compId") {
                            TournamentHeader(
                                name = compName ?: (if (isArabic) "بطولة" else "Tournament"),
                                compId = compId
                            )
                        }
                        items(compGames, key = { it.id }) { game ->
                            ProfessionalMatchCard(
                                game = game,
                                isArabic = isArabic,
                                onClick = {
                                    ScoresGameCache.put(game)
                                    onMatchClick(game.id)
                                }
                            )
                            Spacer(modifier = Modifier.height(10.dp))
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun CompetitionSelector(
    selectedId: Long?,
    isArabic: Boolean,
    onSelected: (Long?) -> Unit
) {
    LazyRow(
        modifier = Modifier.fillMaxWidth().padding(vertical = 8.dp),
        contentPadding = PaddingValues(horizontal = 16.dp),
        horizontalArrangement = Arrangement.spacedBy(8.dp)
    ) {
        items(COMPETITION_OPTIONS) { option ->
            val isSelected = option.id == selectedId
            FilterChip(
                selected = isSelected,
                onClick = { onSelected(option.id) },
                label = {
                    Text(
                        text = if (isArabic) option.nameAr else option.nameEn,
                        fontSize = 12.sp,
                        fontWeight = FontWeight.SemiBold,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis
                    )
                },
                colors = FilterChipDefaults.filterChipColors(
                    selectedContainerColor = Primary,
                    selectedLabelColor = Color.Black,
                    containerColor = BgSecondary,
                    labelColor = TextMuted
                ),
                border = FilterChipDefaults.filterChipBorder(
                    borderColor = Color(0x33FFFFFF),
                    selectedBorderColor = Primary,
                    enabled = true,
                    selected = isSelected
                ),
                shape = RoundedCornerShape(20.dp)
            )
        }
    }
}

@Composable
private fun TournamentHeader(name: String, compId: Long) {
    val compLogoUrl = "https://imagecache.365scores.com/image/upload/f_png,w_48,h_48,c_limit/v5/Competitions/$compId"
    Row(
        modifier = Modifier.fillMaxWidth().padding(top = 12.dp, bottom = 8.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        AsyncImage(
            model = compLogoUrl,
            contentDescription = null,
            modifier = Modifier.size(22.dp),
            contentScale = ContentScale.Fit
        )
        Spacer(modifier = Modifier.width(8.dp))
        Text(text = name, color = Color.White, fontSize = 14.sp, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.width(8.dp))
        Box(modifier = Modifier.weight(1f).height(1.dp).background(Color(0x22FFFFFF)))
    }
}

@Composable
private fun FilterChipItem(label: String, isSelected: Boolean, onClick: () -> Unit) {
    FilterChip(
        selected = isSelected,
        onClick = onClick,
        label = { Text(text = label, fontSize = 13.sp, fontWeight = FontWeight.SemiBold) },
        colors = FilterChipDefaults.filterChipColors(
            selectedContainerColor = Primary,
            selectedLabelColor = Color.Black,
            containerColor = BgSecondary,
            labelColor = TextMuted
        ),
        border = FilterChipDefaults.filterChipBorder(
            borderColor = Color(0x33FFFFFF),
            selectedBorderColor = Primary,
            enabled = true,
            selected = isSelected
        ),
        shape = RoundedCornerShape(20.dp)
    )
}

@Composable
private fun ProfessionalMatchCard(
    game: ScoresGameDto,
    isArabic: Boolean,
    onClick: () -> Unit
) {
    val infiniteTransition = rememberInfiniteTransition(label = "livePulse")
    val liveAlpha by infiniteTransition.animateFloat(
        initialValue = 0.4f,
        targetValue = 1f,
        animationSpec = infiniteRepeatable(tween(900), RepeatMode.Reverse),
        label = "alpha"
    )

    val isLive = game.isLiveMatch()
    val status = game.matchStatus()
    val homeLogo = "https://imagecache.365scores.com/image/upload/f_png,w_80,h_80,c_limit/v5/Competitors/${game.homeCompetitor?.id}"
    val awayLogo = "https://imagecache.365scores.com/image/upload/f_png,w_80,h_80,c_limit/v5/Competitors/${game.awayCompetitor?.id}"

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        shape = RoundedCornerShape(16.dp),
        border = BorderStroke(
            width = if (isLive) 1.5.dp else 1.dp,
            color = if (isLive) Primary.copy(alpha = 0.5f) else Color(0x18FFFFFF)
        ),
        colors = CardDefaults.cardColors(containerColor = Color(0xFF1E1E1E))
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .background(
                    Brush.verticalGradient(
                        listOf(
                            if (isLive) Color(0x22FF4444) else Color(0x12FFFFFF),
                            Color(0xFF1E1E1E)
                        )
                    )
                )
                .padding(horizontal = 16.dp, vertical = 14.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = formatStartTime(game.startTime, isArabic),
                    color = TextMuted,
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Medium
                )
                StatusBadge(status = status, isArabic = isArabic, liveAlpha = liveAlpha, game = game)
            }

            Spacer(modifier = Modifier.height(12.dp))

            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                TeamColumn(
                    name = game.homeCompetitor?.name ?: "---",
                    logoUrl = homeLogo,
                    modifier = Modifier.weight(1f),
                    alignment = Alignment.Start
                )

                Column(
                    horizontalAlignment = Alignment.CenterHorizontally,
                    modifier = Modifier.padding(horizontal = 12.dp)
                ) {
                    Text(
                        text = game.displayScoreText(),
                        color = if (status == MatchStatusKind.SCHEDULED) TextMuted else Primary,
                        fontSize = if (status == MatchStatusKind.SCHEDULED) 18.sp else 26.sp,
                        fontWeight = FontWeight.Black,
                        letterSpacing = 1.sp
                    )
                    if (status != MatchStatusKind.SCHEDULED) {
                        Text(
                            text = game.displayStatusText(isArabic),
                            color = if (isLive) DangerColor else TextMuted,
                            fontSize = 11.sp,
                            fontWeight = FontWeight.SemiBold,
                            modifier = Modifier.padding(top = 2.dp)
                        )
                    }
                }

                TeamColumn(
                    name = game.awayCompetitor?.name ?: "---",
                    logoUrl = awayLogo,
                    modifier = Modifier.weight(1f),
                    alignment = Alignment.End
                )
            }
        }
    }
}

@Composable
private fun TeamColumn(
    name: String,
    logoUrl: String,
    modifier: Modifier = Modifier,
    alignment: Alignment.Horizontal
) {
    Column(
        modifier = modifier,
        horizontalAlignment = alignment
    ) {
        AsyncImage(
            model = logoUrl,
            contentDescription = name,
            modifier = Modifier
                .size(44.dp)
                .clip(RoundedCornerShape(10.dp))
                .background(Color(0x15FFFFFF)),
            contentScale = ContentScale.Fit
        )
        Spacer(modifier = Modifier.height(8.dp))
        Text(
            text = name,
            color = Color.White,
            fontSize = 12.sp,
            fontWeight = FontWeight.SemiBold,
            maxLines = 2,
            overflow = TextOverflow.Ellipsis,
            textAlign = if (alignment == Alignment.End) TextAlign.End else TextAlign.Start,
            lineHeight = 14.sp
        )
    }
}

@Composable
private fun StatusBadge(
    status: MatchStatusKind,
    isArabic: Boolean,
    liveAlpha: Float,
    game: ScoresGameDto
) {
    val (bg, text, color) = when (status) {
        MatchStatusKind.LIVE -> Triple(DangerColor.copy(alpha = 0.2f * liveAlpha), if (isArabic) "مباشر" else "LIVE", DangerColor)
        MatchStatusKind.ENDED -> Triple(Color(0x22FFFFFF), game.shortStatusText ?: if (isArabic) "انتهت" else "FT", TextMuted)
        MatchStatusKind.SCHEDULED -> Triple(Primary.copy(alpha = 0.15f), if (isArabic) "قادمة" else "Upcoming", Primary)
        MatchStatusKind.POSTPONED -> Triple(Color(0x33FFA000), if (isArabic) "مؤجلة" else "PPD", Color(0xFFFFA000))
        else -> Triple(Color(0x15FFFFFF), game.shortStatusText ?: "--", TextMuted)
    }

    Row(
        modifier = Modifier
            .background(bg, RoundedCornerShape(6.dp))
            .padding(horizontal = 8.dp, vertical = 3.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        if (status == MatchStatusKind.LIVE) {
            Box(
                modifier = Modifier
                    .size(6.dp)
                    .alpha(liveAlpha)
                    .background(DangerColor, CircleShape)
            )
            Spacer(modifier = Modifier.width(4.dp))
        }
        Text(text = text, color = color, fontSize = 10.sp, fontWeight = FontWeight.Bold)
    }
}
