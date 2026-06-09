package com.sportiptv.app.ui.components

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Favorite
import androidx.compose.material.icons.filled.FavoriteBorder
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil3.compose.AsyncImage
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.ui.theme.*

fun getCountryFlagByName(name: String): String {
    return when (name) {
        "بنغلاديش" -> "🇧🇩 "
        "الهند" -> "🇮🇳 "
        "السعودية" -> "🇸🇦 "
        "مصر" -> "🇪🇬 "
        "الإمارات" -> "🇦🇪 "
        "بريطانيا" -> "🇬🇧 "
        "أمريكا" -> "🇺🇸 "
        "قطر" -> "🇶🇦 "
        "فرنسا" -> "🇫🇷 "
        "إسبانيا" -> "🇪🇸 "
        "ألمانيا" -> "🇩🇪 "
        "إيطاليا" -> "🇮🇹 "
        "البرازيل" -> "🇧🇷 "
        "الأرجنتين" -> "🇦🇷 "
        "تركيا" -> "🇹🇷 "
        "فلسطين" -> "🇵🇸 "
        "الجزائر" -> "🇩🇿 "
        "المغرب" -> "🇲🇦 "
        "تونس" -> "🇹🇳 "
        "العراق" -> "🇮🇶 "
        "سوريا" -> "🇸🇾 "
        "لبنان" -> "🇱🇧 "
        "الأردن" -> "🇯🇴 "
        "الكويت" -> "🇰🇼 "
        "البحرين" -> "🇧🇭 "
        "عمان" -> "🇴🇲 "
        "اليمن" -> "🇾🇪 "
        "ليبيا" -> "🇱🇾 "
        "السودان" -> "🇸🇩 "
        "باكستان" -> "🇵🇰 "
        "البرتغال" -> "🇵🇹 "
        else -> ""
    }
}

@Composable
fun ChannelCard(
    channel: Channel,
    onClick: () -> Unit,
    onFavoriteToggle: () -> Unit,
    modifier: Modifier = Modifier
) {
    Card(
        modifier = modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(12.dp))
            .border(
                BorderStroke(
                    1.dp,
                    Brush.linearGradient(
                        colors = listOf(
                            Color(0xFF9D00FF).copy(alpha = 0.6f),
                            Color(0xFFE5A93C).copy(alpha = 0.3f)
                        )
                    )
                ),
                shape = RoundedCornerShape(12.dp)
            )
            .clickable { onClick() },
        colors = CardDefaults.cardColors(containerColor = BgSecondary),
        elevation = CardDefaults.cardElevation(defaultElevation = 4.dp)
    ) {
        Column {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(110.dp)
                    .background(Color(0xFF0F1222))
            ) {
                // Async image for logo
                AsyncImage(
                    model = channel.logoUrl,
                    contentDescription = channel.name,
                    contentScale = ContentScale.Fit,
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(12.dp)
                )

                // Quality Badge (e.g. FHD, HD)
                Box(
                    modifier = Modifier
                        .align(Alignment.TopStart)
                        .padding(8.dp)
                        .background(
                            brush = Brush.horizontalGradient(
                                colors = listOf(Primary, AccentBlue)
                            ),
                            shape = RoundedCornerShape(4.dp)
                        )
                        .padding(horizontal = 6.dp, vertical = 2.dp)
                ) {
                    Text(
                        text = channel.quality,
                        color = Color.Black,
                        fontSize = 9.sp,
                        fontWeight = FontWeight.ExtraBold
                    )
                }

                // Favorite button Overlay
                IconButton(
                    onClick = onFavoriteToggle,
                    modifier = Modifier
                        .align(Alignment.TopEnd)
                        .padding(4.dp)
                ) {
                    Icon(
                        imageVector = if (channel.isFavorited) Icons.Default.Favorite else Icons.Default.FavoriteBorder,
                        contentDescription = "Favorite toggle",
                        tint = if (channel.isFavorited) DangerColor else Color.White,
                        modifier = Modifier.size(22.dp)
                    )
                }
            }

            // Info details
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(8.dp)
            ) {
                val isArabic = java.util.Locale.getDefault().language == "ar"
                val displayName = if (isArabic && !channel.nameAr.isNullOrEmpty()) channel.nameAr else channel.name
                val flag = channel.country?.let { getCountryFlagByName(it) } ?: ""
                val displayCategory = flag + (if (isArabic && !channel.categoryNameAr.isNullOrEmpty()) channel.categoryNameAr else (channel.categoryName ?: "General"))

                Text(
                    text = displayName,
                    color = TextMain,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis
                )
                
                Spacer(modifier = Modifier.height(2.dp))
                
                Text(
                    text = displayCategory,
                    color = TextMuted,
                    fontSize = 10.sp,
                    fontWeight = FontWeight.Medium
                )
            }
        }
    }
}
