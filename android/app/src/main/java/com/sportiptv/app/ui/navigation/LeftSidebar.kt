package com.sportiptv.app.ui.navigation

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Favorite
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.LiveTv
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material.icons.filled.SportsSoccer
import androidx.compose.material3.Icon
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.sportiptv.app.ui.theme.Primary // GoldAccent
import com.sportiptv.app.ui.theme.TextMuted

@Composable
fun LeftSidebar(
    currentRoute: String?,
    onNavigate: (String) -> Unit,
    modifier: Modifier = Modifier
) {
    val isArabic = java.util.Locale.getDefault().language == "ar"
    
    // Sidebar dark background color matching dark futuristic theme
    val sidebarBgColor = Color(0xFF0A041A) // Stadium deep purple background

    Column(
        modifier = modifier
            .width(220.dp)
            .fillMaxHeight()
            .background(sidebarBgColor)
            .padding(vertical = 24.dp, horizontal = 12.dp),
        horizontalAlignment = Alignment.End // Align items to end (Right Sidebar alignment)
    ) {
        // App Logo & Brand Header
        Row(
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.End,
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 32.dp, end = 8.dp)
        ) {
            // Brand Name Text
            Text(
                text = "ZINOU TV",
                color = Color.White,
                fontSize = 20.sp,
                fontWeight = FontWeight.ExtraBold,
                letterSpacing = 1.sp
            )
            
            Spacer(modifier = Modifier.width(12.dp))
            
            // TV Icon drawn with gold gradient
            Box(
                modifier = Modifier
                    .size(36.dp)
                    .background(
                        brush = Brush.linearGradient(
                            colors = listOf(Color(0xFFE5A93C), Color(0xFF9D00FF))
                        ),
                        shape = RoundedCornerShape(8.dp)
                    )
                    .padding(2.dp),
                contentAlignment = Alignment.Center
            ) {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .background(Color(0xFF0A041A), shape = RoundedCornerShape(6.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.LiveTv,
                        contentDescription = null,
                        tint = Color(0xFFE5A93C),
                        modifier = Modifier.size(20.dp)
                    )
                }
            }
        }

        // Navigation Items
        val items = listOf(
            SidebarItemSpec(
                title = if (isArabic) "الرئيسية" else "Home",
                route = Screen.Home.route,
                icon = Icons.Default.Home
            ),
            SidebarItemSpec(
                title = if (isArabic) "القنوات" else "Channels",
                route = Screen.Channels.route,
                icon = Icons.Default.LiveTv
            ),
            SidebarItemSpec(
                title = if (isArabic) "المباريات" else "Matches",
                route = Screen.Matches.route,
                icon = Icons.Default.SportsSoccer
            ),
            SidebarItemSpec(
                title = if (isArabic) "المفضلة" else "Favorites",
                route = Screen.Favorites.route,
                icon = Icons.Default.Favorite
            ),
            SidebarItemSpec(
                title = if (isArabic) "الإعدادات" else "Settings",
                route = Screen.Settings.route,
                icon = Icons.Default.Settings
            )
        )

        Column(
            verticalArrangement = Arrangement.spacedBy(10.dp),
            modifier = Modifier.fillMaxWidth()
        ) {
            items.forEach { item ->
                // Check if route matches (allowing for Category routing inside channels)
                val isSelected = currentRoute == item.route || 
                    (item.route.startsWith("channels_screen") && currentRoute?.startsWith("channels_screen") == true)
                
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.End,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(50.dp)
                        .clip(RoundedCornerShape(12.dp))
                        .background(
                            if (isSelected) Brush.horizontalGradient(
                                colors = listOf(
                                    Color(0xFF9D00FF).copy(alpha = 0.05f),
                                    Color(0xFF9D00FF).copy(alpha = 0.25f)
                                )
                            ) else Brush.horizontalGradient(
                                colors = listOf(Color.Transparent, Color.Transparent)
                            )
                        )
                        .border(
                            width = 1.dp,
                            brush = if (isSelected) Brush.horizontalGradient(
                                colors = listOf(
                                    Color(0xFF9D00FF).copy(alpha = 0.1f),
                                    Color(0xFF9D00FF)
                                )
                            ) else Brush.horizontalGradient(
                                colors = listOf(Color.Transparent, Color.Transparent)
                            ),
                            shape = RoundedCornerShape(12.dp)
                        )
                        .clickable { onNavigate(item.route) }
                        .padding(horizontal = 16.dp)
                ) {
                    Text(
                        text = item.title,
                        color = if (isSelected) Color.White else TextMuted,
                        fontSize = 15.sp,
                        fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Medium,
                        modifier = Modifier.weight(1f),
                        textAlign = if (isArabic) androidx.compose.ui.text.style.TextAlign.Right else androidx.compose.ui.text.style.TextAlign.Left
                    )
                    
                    Spacer(modifier = Modifier.width(12.dp))
                    
                    Icon(
                        imageVector = item.icon,
                        contentDescription = item.title,
                        tint = if (isSelected) Color(0xFFE5A93C) else TextMuted,
                        modifier = Modifier.size(22.dp)
                    )
                }
            }
        }
    }
}

data class SidebarItemSpec(
    val title: String,
    val route: String,
    val icon: ImageVector
)
