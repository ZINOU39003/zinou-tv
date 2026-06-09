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
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.sportiptv.app.data.remote.dto.ScoresGameDto
import com.sportiptv.app.ui.matches.MatchesViewModel
import com.sportiptv.app.ui.matches.awayDisplayScore
import com.sportiptv.app.ui.matches.homeDisplayScore
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary
import com.sportiptv.app.ui.theme.TextMuted

@Composable
fun MatchH2HTab(viewModel: MatchesViewModel, isArabic: Boolean) {
    val h2hList by viewModel.h2h.collectAsState()
    val isLoading by viewModel.h2hLoading.collectAsState()

    if (isLoading) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = Primary)
        }
        return
    }

    if (h2hList.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize().padding(32.dp), contentAlignment = Alignment.Center) {
            Text(
                text = if (isArabic) "لا يوجد تاريخ مواجهات مسجل" else "No head-to-head records available",
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
                text = if (isArabic) "تاريخ المواجهات المباشرة" else "Head to Head History",
                color = Color.White,
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.padding(bottom = 16.dp)
            )
        }

        items(h2hList.take(10)) { game ->
            H2HRow(game = game, isArabic = isArabic)
            Spacer(modifier = Modifier.height(10.dp))
        }
    }
}

@Composable
fun H2HRow(game: ScoresGameDto, isArabic: Boolean) {
    val dateText = try {
        val isoString = game.startTime ?: ""
        if (isoString.length >= 10) {
            isoString.substring(0, 10)
        } else {
            ""
        }
    } catch (e: Exception) {
        ""
    }

    CardH2H(game = game, dateText = dateText)
}

@Composable
fun CardH2H(game: ScoresGameDto, dateText: String) {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .background(BgSecondary, shape = RoundedCornerShape(10.dp))
            .padding(14.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                text = game.homeCompetitor?.name ?: "---",
                color = Color.White,
                fontSize = 14.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.weight(1f)
            )
            
            Text(
                text = "${game.homeDisplayScore()}  -  ${game.awayDisplayScore()}",
                color = Primary,
                fontSize = 16.sp,
                fontWeight = FontWeight.Black,
                modifier = Modifier.padding(horizontal = 16.dp),
                textAlign = TextAlign.Center
            )
            
            Text(
                text = game.awayCompetitor?.name ?: "---",
                color = Color.White,
                fontSize = 14.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.End
            )
        }
        if (dateText.isNotEmpty()) {
            Spacer(modifier = Modifier.height(6.dp))
            Text(
                text = dateText,
                color = TextMuted,
                fontSize = 11.sp,
                modifier = Modifier.fillMaxWidth(),
                textAlign = TextAlign.Center
            )
        }
    }
}
