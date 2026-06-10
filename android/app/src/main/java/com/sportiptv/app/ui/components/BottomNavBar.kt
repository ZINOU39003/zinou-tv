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
import com.sportiptv.app.ui.navigation.Screen
import com.sportiptv.app.ui.theme.BgSecondary
import com.sportiptv.app.ui.theme.Primary

@Composable
fun BottomNavBar(
    currentRoute: String?,
    onNavigate: (String) -> Unit
) {
    val isArabic = java.util.Locale.getDefault().language == "ar"
    val items = listOf(
        NavigationItem(if (isArabic) "الرئيسية" else "Home", Screen.Home.route, Icons.Default.Home),
        NavigationItem(if (isArabic) "القنوات" else "Channels", Screen.Channels.route, Icons.Default.List),
        NavigationItem(if (isArabic) "المباريات" else "Matches", Screen.Matches.route, Icons.Default.SportsSoccer),
        NavigationItem(if (isArabic) "المفضلة" else "Favorites", Screen.Favorites.route, Icons.Default.Favorite),
        NavigationItem(if (isArabic) "الإعدادات" else "Settings", Screen.Settings.route, Icons.Default.Settings)
    )

    NavigationBar(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(topStart = 16.dp, topEnd = 16.dp))
            .background(BgSecondary),
        containerColor = BgSecondary,
        tonalElevation = 8.dp
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
                        contentDescription = item.title
                    )
                },
                label = { Text(text = item.title) },
                colors = NavigationBarItemDefaults.colors(
                    selectedIconColor = Color.White,
                    selectedTextColor = Primary,
                    indicatorColor = Color(0xFF9D00FF), // Neon Purple indicator
                    unselectedIconColor = Color.Gray,
                    unselectedTextColor = Color.Gray
                )
            )
        }
    }
}

private data class NavigationItem(
    val title: String,
    val route: String,
    val icon: ImageVector
)
