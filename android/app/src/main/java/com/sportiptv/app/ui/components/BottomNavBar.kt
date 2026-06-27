package com.sportiptv.app.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Favorite
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.List
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material.icons.filled.SportsSoccer
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.unit.dp
import androidx.compose.material.icons.filled.Face
import androidx.compose.material.icons.filled.Movie
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Sports
import androidx.compose.material.icons.filled.Tv
import androidx.compose.ui.unit.sp
import com.sportiptv.app.ui.navigation.Screen
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary

@Composable
fun BottomNavBar(
    currentRoute: String?,
    onNavigate: (String) -> Unit
) {
    val items = listOf(
        NavigationItem("الرئيسية", Screen.Home.route, Icons.Default.Home),
        NavigationItem("المسلسلات", Screen.Series.route, Icons.Default.PlayArrow),
        NavigationItem("الأفلام", Screen.Movies.route, Icons.Default.Movie),
        NavigationItem("القنوات", Screen.Channels.route, Icons.Default.Tv),
        NavigationItem("المباريات", Screen.Matches.route, Icons.Default.SportsSoccer)
    )

    NavigationBar(
        modifier = Modifier
            .fillMaxWidth()
            .background(Color(0xFF0F0F0F)),
        containerColor = Color(0xFF0F0F0F),
        tonalElevation = 0.dp
    ) {
        items.forEach { item ->
            val selected = currentRoute == item.route || 
                (item.route.startsWith("channels_screen") && currentRoute?.startsWith("channels_screen") == true)
            NavigationBarItem(
                selected = selected,
                onClick = { onNavigate(item.route) },
                icon = {
                    Icon(
                        imageVector = item.icon,
                        contentDescription = item.title,
                        modifier = Modifier.size(24.dp)
                    )
                },
                label = { 
                    Text(
                        text = item.title, 
                        fontSize = 10.sp,
                        maxLines = 1,
                        color = if (selected) Primary else Color.Gray
                    ) 
                },
                colors = NavigationBarItemDefaults.colors(
                    selectedIconColor = Primary,
                    selectedTextColor = Primary,
                    indicatorColor = Color.Transparent, // No background highlight
                    unselectedIconColor = Color.Gray,
                    unselectedTextColor = Color.Gray
                ),
                alwaysShowLabel = true
            )
        }
    }
}

private data class NavigationItem(
    val title: String,
    val route: String,
    val icon: ImageVector
)
