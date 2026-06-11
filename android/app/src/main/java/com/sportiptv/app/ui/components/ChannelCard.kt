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
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.sportiptv.app.util.ImageUrlResolver
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
            .aspectRatio(1f) // Square aspect ratio
            .clip(RoundedCornerShape(4.dp))
            .clickable { onClick() },
        shape = RoundedCornerShape(4.dp),
        colors = CardDefaults.cardColors(containerColor = CardBg)
    ) {
        Column(
            modifier = Modifier.fillMaxSize().padding(12.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Box(
                modifier = Modifier.weight(1f).fillMaxWidth(0.85f),
                contentAlignment = Alignment.Center
            ) {
                val logoModel = ImageUrlResolver.resolve(channel.logoUrl)
                if (!logoModel.isNullOrBlank()) {
                    AsyncImage(
                        model = logoModel,
                        contentDescription = channel.name,
                        contentScale = ContentScale.Fit,
                        modifier = Modifier.fillMaxSize().padding(8.dp)
                    )
                } else {
                    Text(
                        text = channel.name.take(2).uppercase(),
                        color = Primary,
                        fontSize = 24.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
            Spacer(modifier = Modifier.height(8.dp))
            
            val isArabic = java.util.Locale.getDefault().language == "ar"
            val displayName = if (isArabic && !channel.nameAr.isNullOrEmpty()) channel.nameAr else channel.name
            
            Text(
                text = displayName,
                color = TextMain,
                fontSize = 11.sp,
                fontWeight = FontWeight.Bold,
                maxLines = 1,
                overflow = TextOverflow.Ellipsis,
                textAlign = TextAlign.Center
            )
        }
    }
}
