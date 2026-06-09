package com.sportiptv.app.ui.components

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.DragHandle
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.MoreVert
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil3.compose.AsyncImage
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary // GoldAccent
import com.sportiptv.app.ui.theme.TextMuted

@Composable
fun PlaylistsPanel(
    onBackClick: (() -> Unit)? = null,
    modifier: Modifier = Modifier
) {
    val isArabic = java.util.Locale.getDefault().language == "ar"
    
    // Background color of the right-side playlists panel
    val playlistsBgColor = Color(0xFF0C0914)

    var selectedFilter by remember { mutableStateOf(0) }
    val filters = if (isArabic) {
        listOf("قوائمي", "الكل", "رياضة", "أفلام", "مسلسلات")
    } else {
        listOf("My Playlists", "All", "Sports", "Movies", "Series")
    }

    val playlists = listOf(
        PlaylistItemSpec(
            title = if (isArabic) "كأس العالم 2026" else "World Cup 2026",
            count = 125,
            image = "https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/bein1.png"
        ),
        PlaylistItemSpec(
            title = if (isArabic) "دوري أبطال أوروبا" else "UEFA Champions League",
            count = 89,
            image = "https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/mbc_action.png"
        ),
        PlaylistItemSpec(
            title = if (isArabic) "الدوري الإسباني" else "La Liga Santander",
            count = 112,
            image = "https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/aljazeera.png"
        ),
        PlaylistItemSpec(
            title = if (isArabic) "أفلام الأكشن" else "Action Movies",
            count = 64,
            image = "https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/mbc_action.png"
        ),
        PlaylistItemSpec(
            title = if (isArabic) "مسلسلات أجنبية" else "Foreign Series",
            count = 53,
            image = "https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/bein2.png"
        ),
        PlaylistItemSpec(
            title = if (isArabic) "كرتون للأطفال" else "Kids Cartoons",
            count = 78,
            image = "https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/aljazeera.png"
        )
    )

    Column(
        modifier = modifier
            .fillMaxHeight()
            .background(playlistsBgColor)
            .padding(16.dp)
    ) {
        // Playlists Top Header Bar
        Row(
            modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                if (onBackClick != null) {
                    IconButton(onClick = onBackClick) {
                        Icon(
                            imageVector = Icons.AutoMirrored.Filled.ArrowBack,
                            contentDescription = "Back",
                            tint = Color.White
                        )
                    }
                    Spacer(modifier = Modifier.width(8.dp))
                }
                
                Text(
                    text = if (isArabic) "قوائم التشغيل" else "Playlists",
                    color = Color.White,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Bold
                )
            }

            Row {
                IconButton(onClick = {}) {
                    Icon(imageVector = Icons.Default.Search, contentDescription = "Search", tint = Color.White)
                }
                IconButton(onClick = {}) {
                    Icon(imageVector = Icons.Default.MoreVert, contentDescription = "More", tint = Color.White)
                }
            }
        }

        // Horizontal filter chips
        LazyRow(
            horizontalArrangement = Arrangement.spacedBy(8.dp),
            modifier = Modifier.fillMaxWidth().padding(bottom = 16.dp)
        ) {
            items(filters.size) { index ->
                val isSelected = selectedFilter == index
                Box(
                    modifier = Modifier
                        .clip(RoundedCornerShape(8.dp))
                        .background(if (isSelected) Primary else BgSecondary)
                        .clickable { selectedFilter = index }
                        .padding(horizontal = 14.dp, vertical = 6.dp)
                ) {
                    Text(
                        text = filters[index],
                        color = if (isSelected) Color.Black else Color.White,
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }

        // Playlists lists vertical scroll Column
        LazyColumn(
            verticalArrangement = Arrangement.spacedBy(10.dp),
            modifier = Modifier.weight(1f).fillMaxWidth()
        ) {
            items(playlists) { playlist ->
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = BgSecondary),
                    shape = RoundedCornerShape(10.dp)
                ) {
                    Row(
                        modifier = Modifier.padding(10.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        // Drag Handle
                        Icon(
                            imageVector = Icons.Default.DragHandle,
                            contentDescription = "Drag reorder",
                            tint = TextMuted,
                            modifier = Modifier.size(20.dp)
                        )
                        
                        Spacer(modifier = Modifier.width(10.dp))
                        
                        // Playlist Thumbnail
                        AsyncImage(
                            model = playlist.image,
                            contentDescription = playlist.title,
                            contentScale = ContentScale.Crop,
                            modifier = Modifier
                                .size(50.dp)
                                .clip(RoundedCornerShape(6.dp))
                                .background(Color(0xFF0F1222))
                        )
                        
                        Spacer(modifier = Modifier.width(12.dp))
                        
                        // Title + Count Info
                        Column(modifier = Modifier.weight(1f)) {
                            Text(
                                text = playlist.title,
                                color = Color.White,
                                fontSize = 13.sp,
                                fontWeight = FontWeight.Bold
                            )
                            
                            Spacer(modifier = Modifier.height(2.dp))
                            
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Text(
                                    text = if (isArabic) "${playlist.count} فيديو" else "${playlist.count} videos",
                                    color = TextMuted,
                                    fontSize = 10.sp
                                )
                                Spacer(modifier = Modifier.width(8.dp))
                                Icon(
                                    imageVector = Icons.Default.Lock,
                                    contentDescription = "Private",
                                    tint = Primary,
                                    modifier = Modifier.size(10.dp)
                                )
                                Spacer(modifier = Modifier.width(3.dp))
                                Text(
                                    text = if (isArabic) "خاص" else "Private",
                                    color = Primary,
                                    fontSize = 10.sp,
                                    fontWeight = FontWeight.SemiBold
                                )
                            }
                        }

                        // More Action vert dots
                        IconButton(onClick = {}) {
                            Icon(
                                imageVector = Icons.Default.MoreVert,
                                contentDescription = "More options",
                                tint = TextMuted
                            )
                        }
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(12.dp))

        // Create Playlist button
        Button(
            onClick = {},
            modifier = Modifier.fillMaxWidth().height(48.dp),
            colors = ButtonDefaults.buttonColors(containerColor = Primary),
            shape = RoundedCornerShape(12.dp)
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(
                    imageVector = Icons.Default.Add,
                    contentDescription = null,
                    tint = Color.Black,
                    modifier = Modifier.size(18.dp)
                )
                Spacer(modifier = Modifier.width(6.dp))
                Text(
                    text = if (isArabic) "إنشاء قائمة تشغيل جديدة" else "Create New Playlist",
                    color = Color.Black,
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp
                )
            }
        }
    }
}

data class PlaylistItemSpec(
    val title: String,
    val count: Int,
    val image: String
)
