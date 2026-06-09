package com.sportiptv.app.ui.navigation

sealed class Screen(val route: String) {
    object Splash : Screen("splash_screen")
    object Login : Screen("login_screen")
    object Activation : Screen("activation_screen")
    object Home : Screen("home_screen")
    object Channels : Screen("channels_screen?categoryId={categoryId}") {
        fun createRoute(categoryId: Long?) = if (categoryId != null) "channels_screen?categoryId=$categoryId" else "channels_screen"
    }
    object Favorites : Screen("favorites_screen")
    object Search : Screen("search_screen")
    object Settings : Screen("settings_screen")
    object Subscription : Screen("subscription_screen")
    object Matches : Screen("matches_screen")
    object Movies : Screen("movies_screen")
    object WorldCup : Screen("world_cup_screen")
    object MatchDetails : Screen("match_details_screen/{matchId}") {
        fun createRoute(matchId: Long) = "match_details_screen/$matchId"
    }
    object TeamDetails : Screen("team_details_screen/{competitorId}") {
        fun createRoute(competitorId: Long) = "team_details_screen/$competitorId"
    }
    
    // Player screen requires channel ID as path parameter
    object Player : Screen("player_screen/{channelId}") {
        fun createRoute(channelId: Long) = "player_screen/$channelId"
    }
}
