package com.sportiptv.app.ui.matches.components

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil3.compose.AsyncImage
import com.sportiptv.app.data.remote.dto.ScoresStandingRowDto
import com.sportiptv.app.ui.matches.MatchesViewModel
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary
import com.sportiptv.app.ui.theme.TextMuted

@Composable
fun StandingsTab(viewModel: MatchesViewModel, isArabic: Boolean) {
    val standings by viewModel.standings.collectAsState()
    val isLoading by viewModel.standingsLoading.collectAsState()

    if (isLoading) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = Primary)
        }
        return
    }

    if (standings.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize().padding(32.dp), contentAlignment = Alignment.Center) {
            Text(
                text = if (isArabic) "ترتيب البطولة غير متوفر حالياً" else "Standings not available currently",
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
                text = if (isArabic) "جدول الترتيب" else "League Table",
                color = Color.White,
                fontSize = 16.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.padding(vertical = 12.dp)
            )
        }

        item {
            StandingHeader(isArabic = isArabic)
        }

        items(standings) { row ->
            StandingRow(row = row)
        }
    }
}

@Composable
fun StandingHeader(isArabic: Boolean) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .background(BgSecondary)
            .padding(vertical = 8.dp, horizontal = 4.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(text = "#", color = TextMuted, fontSize = 12.sp, modifier = Modifier.width(24.dp), textAlign = TextAlign.Center)
        Text(text = if (isArabic) "الفريق" else "Team", color = TextMuted, fontSize = 12.sp, modifier = Modifier.weight(1f))
        Text(text = if (isArabic) "ل" else "P", color = TextMuted, fontSize = 12.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
        Text(text = if (isArabic) "ف" else "W", color = TextMuted, fontSize = 12.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
        Text(text = if (isArabic) "ت" else "D", color = TextMuted, fontSize = 12.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
        Text(text = if (isArabic) "خ" else "L", color = TextMuted, fontSize = 12.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
        Text(text = if (isArabic) "ن" else "Pts", color = TextMuted, fontSize = 12.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
    }
}

@Composable
fun StandingRow(row: ScoresStandingRowDto) {
    val teamLogoUrl = "https://imagecache.365scores.com/image/upload/f_png,w_80,h_80,c_limit/v5/Competitors/${row.competitor?.id}"

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 8.dp, horizontal = 4.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = (row.position ?: 1).toString(),
            color = Color.White,
            fontSize = 14.sp,
            fontWeight = FontWeight.Bold,
            modifier = Modifier.width(24.dp),
            textAlign = TextAlign.Center
        )
        Row(
            modifier = Modifier.weight(1f),
            verticalAlignment = Alignment.CenterVertically
        ) {
            AsyncImage(
                model = teamLogoUrl,
                contentDescription = null,
                modifier = Modifier.size(20.dp),
                contentScale = ContentScale.Fit
            )
            Spacer(modifier = Modifier.width(8.dp))
            Text(
                text = row.competitor?.name ?: "---",
                color = Color.White,
                fontSize = 14.sp,
                fontWeight = FontWeight.SemiBold,
                maxLines = 1,
                overflow = TextOverflow.Ellipsis
            )
        }
        Text(text = (row.played ?: 0).toString(), color = Color.White, fontSize = 14.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
        Text(text = (row.wins ?: 0).toString(), color = Color.White, fontSize = 14.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
        Text(text = (row.draws ?: 0).toString(), color = Color.White, fontSize = 14.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
        Text(text = (row.losses ?: 0).toString(), color = Color.White, fontSize = 14.sp, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
        Text(text = (row.points ?: 0).toString(), color = Primary, fontSize = 14.sp, fontWeight = FontWeight.Bold, modifier = Modifier.width(30.dp), textAlign = TextAlign.Center)
    }
}
