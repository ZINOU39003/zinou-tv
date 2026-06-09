package com.sportiptv.app.ui.matches.components

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
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
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.sportiptv.app.ui.matches.MatchesViewModel
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary
import com.sportiptv.app.ui.theme.TextMuted

@Composable
fun MatchStatsTab(viewModel: MatchesViewModel, isArabic: Boolean) {
    val stats by viewModel.matchStats.collectAsState()
    val isLoading by viewModel.matchStatsLoading.collectAsState()

    if (isLoading) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = Primary)
        }
        return
    }

    if (stats.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize().padding(32.dp), contentAlignment = Alignment.Center) {
            Text(
                text = if (isArabic) "الإحصائيات غير متوفرة بعد" else "Stats not available yet",
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
                text = if (isArabic) "إحصائيات المباراة" else "Match Stats",
                color = Color.White,
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.padding(bottom = 16.dp)
            )
        }

        items(stats) { stat ->
            val homeVal = stat.home?.toFloat() ?: 0f
            val awayVal = stat.away?.toFloat() ?: 0f
            val total = homeVal + awayVal
            val homePercentage = if (total > 0f) homeVal / total else 0.5f

            val homeValStr = stat.home?.toString() ?: "0"
            val awayValStr = stat.away?.toString() ?: "0"
            val statName = stat.name ?: ""

            if (statName.equals("possession", ignoreCase = true) || statName.equals("possessionPct", ignoreCase = true) || statName.contains("الاستحواذ", ignoreCase = true)) {
                val homeFormatted = if (homeValStr.contains("%")) homeValStr else "$homeValStr%"
                val awayFormatted = if (awayValStr.contains("%")) awayValStr else "$awayValStr%"
                StatPossessionBar(
                    title = if (isArabic) "الاستحواذ على الكرة" else "Possession",
                    homeValue = homeFormatted,
                    awayValue = awayFormatted,
                    homePercentage = homePercentage
                )
            } else {
                StatRow(
                    title = translateStat(statName, isArabic),
                    homeValue = homeValStr,
                    awayValue = awayValStr,
                    homeVal = homeVal,
                    awayVal = awayVal
                )
            }
            Spacer(modifier = Modifier.height(16.dp))
        }
    }
}

@Composable
fun StatPossessionBar(title: String, homeValue: String, awayValue: String, homePercentage: Float) {
    Column(modifier = Modifier.fillMaxWidth().padding(vertical = 8.dp)) {
        Text(
            text = title,
            color = Color.White,
            fontSize = 14.sp,
            fontWeight = FontWeight.Bold,
            modifier = Modifier.align(Alignment.CenterHorizontally)
        )
        Spacer(modifier = Modifier.height(8.dp))
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .height(24.dp)
                .clip(RoundedCornerShape(4.dp)),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .weight(if (homePercentage > 0f) homePercentage else 0.01f)
                    .fillMaxHeight()
                    .background(Primary),
                contentAlignment = Alignment.CenterStart
            ) {
                Text(
                    text = homeValue,
                    color = Color.Black,
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(start = 8.dp)
                )
            }
            Box(
                modifier = Modifier
                    .weight(if ((1f - homePercentage) > 0f) (1f - homePercentage) else 0.01f)
                    .fillMaxHeight()
                    .background(Color(0xFF7C3AED)),
                contentAlignment = Alignment.CenterEnd
            ) {
                Text(
                    text = awayValue,
                    color = Color.White,
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(end = 8.dp)
                )
            }
        }
    }
}

@Composable
fun StatRow(title: String, homeValue: String, awayValue: String, homeVal: Float, awayVal: Float) {
    Row(
        modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        // Home
        Box(
            modifier = Modifier
                .size(32.dp)
                .background(if (homeVal > awayVal) Primary else BgSecondary, shape = CircleShape),
            contentAlignment = Alignment.Center
        ) {
            Text(
                text = homeValue,
                color = if (homeVal > awayVal) Color.Black else Color.White,
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold
            )
        }

        // Title
        Text(
            text = title,
            color = Color.White,
            fontSize = 14.sp,
            fontWeight = FontWeight.SemiBold
        )

        // Away
        Box(
            modifier = Modifier
                .size(32.dp)
                .background(if (awayVal > homeVal) Color(0xFF7C3AED) else BgSecondary, shape = CircleShape),
            contentAlignment = Alignment.Center
        ) {
            Text(
                text = awayValue,
                color = Color.White,
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold
            )
        }
    }
}

fun translateStat(statName: String, isArabic: Boolean): String {
    if (!isArabic) return statName
    return when (statName.lowercase()) {
        "fouls", "الأخطاء" -> "الأخطاء"
        "corner kicks", "corners", "الضربات الركنية" -> "الضربات الركنية"
        "offsides", "حالات التسلل" -> "حالات التسلل"
        "possession", "الاستحواذ" -> "الاستحواذ"
        "yellow cards", "بطاقات صفراء" -> "بطاقات صفراء"
        "red cards", "بطاقات حمراء" -> "بطاقات حمراء"
        "saves", "تصدي" -> "التصديات"
        "passes", "التمريرات" -> "التمريرات"
        "shots", "التسديدات" -> "التسديدات"
        "shots on target", "التسديدات على المرمى" -> "تسديدات على المرمى"
        "tackles" -> "الاعتراضات"
        "fouled" -> "أخطاء مكتسبة"
        else -> statName
    }
}
