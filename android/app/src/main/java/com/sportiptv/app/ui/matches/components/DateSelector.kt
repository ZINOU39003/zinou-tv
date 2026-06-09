package com.sportiptv.app.ui.matches.components

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary
import com.sportiptv.app.ui.theme.TextMuted
import java.time.LocalDate
import java.time.format.DateTimeFormatter
import java.util.*

@Composable
fun DateSelector(
    selectedDate: LocalDate,
    onDateSelected: (LocalDate) -> Unit,
    isArabic: Boolean
) {
    // Generate dates: 3 days ago to 3 days ahead
    val dates = (-3..3).map { LocalDate.now().plusDays(it.toLong()) }
    
    val displayFormatter = if (isArabic) {
        DateTimeFormatter.ofPattern("d MMM", Locale("ar"))
    } else {
        DateTimeFormatter.ofPattern("MMM d", Locale.ENGLISH)
    }

    LazyRow(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 12.dp),
        contentPadding = PaddingValues(horizontal = 20.dp),
        horizontalArrangement = Arrangement.spacedBy(8.dp)
    ) {
        items(dates) { date ->
            val isSelected = date == selectedDate
            val isToday = date == LocalDate.now()
            
            val label = when {
                isToday -> if (isArabic) "اليوم" else "Today"
                else -> date.format(displayFormatter)
            }

            Box(
                modifier = Modifier
                    .background(
                        color = if (isSelected) BgSecondary else Color.Transparent,
                        shape = RoundedCornerShape(20.dp)
                    )
                    .border(
                        width = 1.dp,
                        color = if (isSelected) Primary else Color(0x33FFFFFF),
                        shape = RoundedCornerShape(20.dp)
                    )
                    .clickable { onDateSelected(date) }
                    .padding(horizontal = 16.dp, vertical = 8.dp),
                contentAlignment = Alignment.Center
            ) {
                Text(
                    text = label,
                    color = if (isSelected) Primary else TextMuted,
                    fontSize = 13.sp,
                    fontWeight = if (isSelected) FontWeight.Bold else FontWeight.SemiBold
                )
            }
        }
    }
}
