package com.sportiptv.app.ui.matches

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Share
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
import com.sportiptv.app.ui.matches.displayScoreText
import com.sportiptv.app.ui.matches.displayStatusText
import com.sportiptv.app.ui.matches.isLiveMatch
import com.sportiptv.app.ui.matches.teamLogoUrl
import com.sportiptv.app.ui.theme.*

@Composable
fun MatchDetailsScreen(
    matchId: Long,
    onBackClick: () -> Unit,
    viewModel: MatchesViewModel = hiltViewModel()
) {
    val matchDetail by viewModel.matchDetail.collectAsState()
    val matchDetailLoading by viewModel.matchDetailLoading.collectAsState()
    val isArabic = java.util.Locale.getDefault().language == "ar"

    var selectedTabIndex by remember { mutableStateOf(0) }
    val tabs = if (isArabic) {
        listOf("الملخص", "التشكيلة", "إحصائيات", "التاريخ")
    } else {
        listOf("Summary", "Lineups", "Stats", "H2H")
    }

    LaunchedEffect(matchId) {
        viewModel.fetchMatchDetail(matchId)
        viewModel.fetchMatchLineup(matchId)
        viewModel.fetchMatchStats(matchId)
        viewModel.fetchH2H(matchId)
    }

    val match = matchDetail
    if (matchDetailLoading && match == null) {
        Box(modifier = Modifier.fillMaxSize().background(BgPrimary), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = Primary)
        }
        return
    }

    if (match == null) {
        Box(modifier = Modifier.fillMaxSize().background(BgPrimary), contentAlignment = Alignment.Center) {
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                Text(
                    text = if (isArabic) "تفاصيل المباراة غير متوفرة" else "Match details not available",
                    color = TextMuted,
                    fontSize = 16.sp
                )
                Spacer(modifier = Modifier.height(16.dp))
                Button(onClick = { viewModel.fetchMatchDetail(matchId) }, colors = ButtonDefaults.buttonColors(containerColor = Primary)) {
                    Text(text = if (isArabic) "إعادة المحاولة" else "Retry", color = Color.Black)
                }
                Spacer(modifier = Modifier.height(8.dp))
                Button(onClick = onBackClick, colors = ButtonDefaults.buttonColors(containerColor = BgSecondary)) {
                    Text(text = if (isArabic) "رجوع" else "Back", color = Color.White)
                }
            }
        }
        return
    }

    val homeLogo = teamLogoUrl(match.homeCompetitor?.id, match.homeCompetitor?.imageVersion)
    val awayLogo = teamLogoUrl(match.awayCompetitor?.id, match.awayCompetitor?.imageVersion)
    val tournamentName = match.competitionDisplayName ?: if (isArabic) "تفاصيل المباراة" else "Match Details"
    val isLive = match.isLiveMatch()

    Column(modifier = Modifier.fillMaxSize().background(BgPrimary)) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 16.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
            IconButton(onClick = onBackClick) {
                Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back", tint = Color.White)
            }
            Text(text = tournamentName, color = Primary, fontSize = 14.sp, fontWeight = FontWeight.Bold, maxLines = 1)
            IconButton(onClick = { }) {
                Icon(Icons.Default.Share, contentDescription = "Share", tint = Color.White)
            }
        }

        Row(
            modifier = Modifier.fillMaxWidth().padding(horizontal = 24.dp, vertical = 8.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.weight(1f)) {
                AsyncImage(model = homeLogo, contentDescription = null, modifier = Modifier.size(64.dp), contentScale = ContentScale.Fit)
                Spacer(modifier = Modifier.height(8.dp))
                Text(
                    text = match.homeCompetitor?.name ?: "---",
                    color = Color.White,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    textAlign = TextAlign.Center
                )
            }

            Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.weight(1f)) {
                Text(
                    text = match.displayScoreText(),
                    color = Color.White,
                    fontSize = 34.sp,
                    fontWeight = FontWeight.ExtraBold
                )
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = match.displayStatusText(isArabic),
                    color = if (isLive) DangerColor else TextMuted,
                    fontSize = 12.sp,
                    fontWeight = FontWeight.SemiBold
                )
                match.venue?.name?.let { venue ->
                    Text(text = venue, color = TextMuted, fontSize = 10.sp, modifier = Modifier.padding(top = 4.dp))
                }
            }

            Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.weight(1f)) {
                AsyncImage(model = awayLogo, contentDescription = null, modifier = Modifier.size(64.dp), contentScale = ContentScale.Fit)
                Spacer(modifier = Modifier.height(8.dp))
                Text(
                    text = match.awayCompetitor?.name ?: "---",
                    color = Color.White,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    textAlign = TextAlign.Center
                )
            }
        }

        match.tvNetworks?.takeIf { it.isNotEmpty() }?.let { networks ->
            Text(
                text = (if (isArabic) "القنوات الناقلة: " else "Broadcast: ") +
                    networks.joinToString(" • ") { it.name.orEmpty() },
                color = Primary,
                fontSize = 11.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth().padding(horizontal = 24.dp, vertical = 8.dp)
            )
        }

        ScrollableTabRow(
            selectedTabIndex = selectedTabIndex,
            containerColor = BgSecondary,
            contentColor = Primary,
            edgePadding = 0.dp,
            indicator = { tabPositions ->
                TabRowDefaults.SecondaryIndicator(
                    modifier = Modifier.tabIndicatorOffset(tabPositions[selectedTabIndex]),
                    color = Primary
                )
            },
            divider = { HorizontalDivider(color = Color(0x13FFFFFF)) }
        ) {
            tabs.forEachIndexed { index, title ->
                Tab(
                    selected = selectedTabIndex == index,
                    onClick = { selectedTabIndex = index },
                    text = { Text(text = title, fontWeight = FontWeight.Bold, fontSize = 14.sp) },
                    selectedContentColor = Primary,
                    unselectedContentColor = TextMuted
                )
            }
        }

        Box(modifier = Modifier.fillMaxSize()) {
            when (selectedTabIndex) {
                0 -> com.sportiptv.app.ui.matches.components.MatchSummaryTab(viewModel = viewModel, isArabic = isArabic)
                1 -> com.sportiptv.app.ui.matches.components.MatchLineupTab(viewModel = viewModel, isArabic = isArabic)
                2 -> com.sportiptv.app.ui.matches.components.MatchStatsTab(viewModel = viewModel, isArabic = isArabic)
                3 -> com.sportiptv.app.ui.matches.components.MatchH2HTab(viewModel = viewModel, isArabic = isArabic)
            }
        }
    }
}
