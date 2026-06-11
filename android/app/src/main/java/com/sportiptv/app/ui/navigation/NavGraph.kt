package com.sportiptv.app.ui.navigation

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.remember
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument
import androidx.compose.runtime.collectAsState
import androidx.compose.ui.platform.LocalContext
import androidx.hilt.navigation.compose.hiltViewModel
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.ui.components.ForceUpdateScreen
import com.sportiptv.app.ui.config.AppConfigViewModel
import com.sportiptv.app.util.VersionUtils
import com.sportiptv.app.ui.auth.ActivationScreen
import com.sportiptv.app.ui.auth.LoginScreen
import com.sportiptv.app.ui.channels.ChannelsScreen
import com.sportiptv.app.ui.components.BottomNavBar
import com.sportiptv.app.ui.components.PlaylistsPanel
import com.sportiptv.app.ui.favorites.FavoritesScreen
import com.sportiptv.app.ui.home.HomeScreen
import com.sportiptv.app.ui.matches.MatchesScreen
import com.sportiptv.app.ui.movies.MoviesScreen
import com.sportiptv.app.ui.player.PlayerScreen
import com.sportiptv.app.ui.search.SearchScreen
import com.sportiptv.app.ui.settings.SettingsScreen
import com.sportiptv.app.ui.splash.SplashScreen
import com.sportiptv.app.ui.subscription.SubscriptionScreen
import com.sportiptv.app.ui.worldcup.WorldCupScreen
import com.sportiptv.app.ui.admin.AdminPanelScreen

@Composable
fun NavGraph(
    startDestination: String = Screen.Splash.route
) {
    val navController = rememberNavController()
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    val configViewModel: AppConfigViewModel = hiltViewModel()
    val configState by configViewModel.configState.collectAsState()
    val context = LocalContext.current
    val versionName = remember {
        context.packageManager.getPackageInfo(context.packageName, 0).versionName ?: "1.0.0"
    }
    val configData = (configState as? Resource.Success)?.data
    val needsForceUpdate = configData?.force_update == true &&
        configData.min_app_version.isNotBlank() &&
        VersionUtils.isLowerThan(versionName, configData.min_app_version)

    if (needsForceUpdate && currentRoute != Screen.Splash.route && currentRoute != Screen.Login.route) {
        ForceUpdateScreen(
            message = configData?.update_message ?: "",
            downloadUrl = configData?.latest_apk_url ?: ""
        )
        return
    }

    // Toggle Bottom Bar / Sidebar. Hidden on Splash, Login, Activation, and Player screens
    val showBottomBar = currentRoute in listOf(
        Screen.Home.route,
        Screen.Channels.route,
        Screen.Favorites.route,
        Screen.Settings.route,
        "matches_screen",
        Screen.WorldCup.route
    )

    val configuration = androidx.compose.ui.platform.LocalConfiguration.current
    val isLandscape = configuration.orientation == android.content.res.Configuration.ORIENTATION_LANDSCAPE
    val isArabic = java.util.Locale.getDefault().language == "ar"

    Row(modifier = Modifier.fillMaxSize()) {
        Scaffold(
            bottomBar = {
                // Render Bottom Navigation Bar in Portrait Mode
                if (showBottomBar && !isLandscape) {
                    BottomNavBar(
                        currentRoute = currentRoute,
                        onNavigate = { route ->
                            navController.navigate(route) {
                                popUpTo(Screen.Home.route) { saveState = true }
                                launchSingleTop = true
                                restoreState = true
                            }
                        }
                    )
                }
            },
            modifier = Modifier.weight(1f)
        ) { innerPadding ->
            NavHost(
                navController = navController,
                startDestination = startDestination,
                modifier = Modifier.padding(innerPadding)
            ) {
                // Splash Route
                composable(Screen.Splash.route) {
                    SplashScreen(
                        onNavigateToLogin = {
                            navController.navigate(Screen.Login.route) {
                                popUpTo(Screen.Splash.route) { inclusive = true }
                            }
                        },
                        onNavigateToActivation = {
                            navController.navigate(Screen.Activation.route) {
                                popUpTo(Screen.Splash.route) { inclusive = true }
                            }
                        },
                        onNavigateToHome = {
                            navController.navigate(Screen.Home.route) {
                                popUpTo(Screen.Splash.route) { inclusive = true }
                            }
                        }
                    )
                }

                // Login Route
                composable(Screen.Login.route) {
                    LoginScreen(
                        onLoginSuccess = {
                            navController.navigate(Screen.Home.route) {
                                popUpTo(Screen.Login.route) { inclusive = true }
                            }
                        },
                        onNavigateToActivation = {
                            navController.navigate(Screen.Activation.route)
                        }
                    )
                }

                // Activation Route
                composable(Screen.Activation.route) {
                    ActivationScreen(
                        onActivationSuccess = {
                            navController.navigate(Screen.Home.route) {
                                popUpTo(Screen.Activation.route) { inclusive = true }
                            }
                        },
                        onNavigateToLogin = {
                            navController.navigate(Screen.Login.route) {
                                popUpTo(Screen.Activation.route) { inclusive = true }
                            }
                        }
                    )
                }
                // Home Route
                composable(Screen.Home.route) {
                    val context = androidx.compose.ui.platform.LocalContext.current
                    Row(modifier = Modifier.fillMaxSize()) {
                        HomeScreen(
                            onChannelClick = { channelId ->
                                navController.navigate(Screen.Player.createRoute(channelId))
                            },
                            onCategoryClick = { categoryId ->
                                navController.navigate(Screen.Channels.createRoute(categoryId))
                            },
                            onSearchClick = {
                                navController.navigate(Screen.Search.route)
                            },
                            onWorldCupClick = {
                                navController.navigate(Screen.WorldCup.route)
                            },
                            onMoviesClick = {
                                navController.navigate(Screen.Movies.route)
                            },
                            onSportsClick = {
                                navController.navigate(Screen.Matches.route)
                            },
                            onSettingsClick = {
                                navController.navigate(Screen.Settings.route)
                            },
                            onExitClick = {
                                val activity = context as? android.app.Activity
                                activity?.finish()
                            },
                            onSubscriptionClick = {
                                navController.navigate(Screen.Subscription.route)
                            },
                            onActivationClick = {
                                navController.navigate(Screen.Activation.route)
                            }
                        )
                    }
                }

                // Favorites Route
                composable(Screen.Favorites.route) {
                    FavoritesScreen(
                        onChannelClick = { channelId ->
                            navController.navigate(Screen.Player.createRoute(channelId))
                        }
                    )
                }


                // Channels List Route
                composable(
                    route = Screen.Channels.route,
                    arguments = listOf(navArgument("categoryId") {
                        type = NavType.StringType
                        nullable = true
                        defaultValue = null
                    })
                ) { backStackEntry ->
                    val categoryIdStr = backStackEntry.arguments?.getString("categoryId")
                    val categoryId = categoryIdStr?.toLongOrNull()
                    ChannelsScreen(
                        initialCategoryId = categoryId,
                        onChannelClick = { channelId ->
                            navController.navigate(Screen.Player.createRoute(channelId))
                        },
                        onNavigateHome = {
                            navController.navigate(Screen.Home.route) {
                                popUpTo(Screen.Home.route) { inclusive = true }
                                launchSingleTop = true
                            }
                        }
                    )
                }


                // Real-time Search Route
                composable(Screen.Search.route) {
                    SearchScreen(
                        onChannelClick = { channelId ->
                            navController.navigate(Screen.Player.createRoute(channelId))
                        },
                        onBackClick = {
                            navController.popBackStack()
                        }
                    )
                }

                // Settings Route
                composable(Screen.Settings.route) {
                    SettingsScreen(
                        onNavigateToSubscription = {
                            navController.navigate(Screen.Subscription.route)
                        },
                        onNavigateToAdminPanel = {
                            navController.navigate(Screen.AdminPanel.route)
                        },
                        onLogout = {
                            navController.navigate(Screen.Login.route) {
                                popUpTo(Screen.Home.route) { inclusive = true }
                            }
                        }
                    )
                }

                // Subscription Details Route
                composable(Screen.Subscription.route) {
                    SubscriptionScreen(
                        onBackClick = {
                            navController.popBackStack()
                        }
                    )
                }

                // Admin Panel WebView Route
                composable(Screen.AdminPanel.route) {
                    AdminPanelScreen(
                        onBackClick = { navController.popBackStack() }
                    )
                }


                // Matches Screen
                composable(Screen.Matches.route) {
                    MatchesScreen(
                        onMatchClick = { matchId ->
                            navController.navigate(Screen.MatchDetails.createRoute(matchId))
                        }
                    )
                }

                // Match Details Screen
                composable(
                    route = Screen.MatchDetails.route,
                    arguments = listOf(navArgument("matchId") { type = NavType.LongType })
                ) { backStackEntry ->
                    val matchId = backStackEntry.arguments?.getLong("matchId") ?: 0L
                    com.sportiptv.app.ui.matches.MatchDetailsScreen(
                        matchId = matchId,
                        onBackClick = { navController.popBackStack() }
                    )
                }


                // World Cup Screen
                composable(Screen.WorldCup.route) {
                    WorldCupScreen(
                        onBackClick = { navController.popBackStack() },
                        onStreamClick = { streamUrl ->
                            val encoded = java.net.URLEncoder.encode(streamUrl, "UTF-8")
                            navController.navigate("player_direct/$encoded")
                        },
                        onChannelClick = { channelId ->
                            navController.navigate(Screen.Player.createRoute(channelId))
                        },
                        onMatchDetailsClick = { matchId ->
                            navController.navigate(Screen.MatchDetails.createRoute(matchId))
                        },
                        onTeamClick = { competitorId ->
                            navController.navigate(Screen.TeamDetails.createRoute(competitorId))
                        }
                    )
                }

                composable(
                    route = Screen.TeamDetails.route,
                    arguments = listOf(navArgument("competitorId") { type = NavType.LongType })
                ) { backStackEntry ->
                    val competitorId = backStackEntry.arguments?.getLong("competitorId") ?: 0L
                    com.sportiptv.app.ui.team.TeamDetailsScreen(
                        competitorId = competitorId,
                        onBackClick = { navController.popBackStack() },
                        onMatchClick = { matchId ->
                            navController.navigate(Screen.MatchDetails.createRoute(matchId))
                        }
                    )
                }

                // Direct stream player (from World Cup match stream URL)
                composable(
                    route = "player_direct/{streamUrl}",
                    arguments = listOf(navArgument("streamUrl") { type = NavType.StringType })
                ) { backStackEntry ->
                    val encodedUrl = backStackEntry.arguments?.getString("streamUrl") ?: ""
                    val streamUrl = java.net.URLDecoder.decode(encodedUrl, "UTF-8")
                    PlayerScreen(
                        channelId = 0L,
                        directStreamUrl = streamUrl,
                        onBackClick = { navController.popBackStack() }
                    )
                }

                // ExoPlayer Screen Route (Protected stream playback)
                composable(
                    route = Screen.Player.route,
                    arguments = listOf(navArgument("channelId") { type = NavType.LongType })
                ) { backStackEntry ->
                    val channelId = backStackEntry.arguments?.getLong("channelId") ?: 0L
                    PlayerScreen(
                        channelId = channelId,
                        onBackClick = {
                            navController.popBackStack()
                        }
                    )
                }
            }
        }

        // Render Sidebar Navigation on the Right side in Landscape Mode
        if (showBottomBar && isLandscape && currentRoute != Screen.Home.route) {
            LeftSidebar(
                currentRoute = currentRoute,
                onNavigate = { route ->
                    navController.navigate(route) {
                        popUpTo(Screen.Home.route) { saveState = true }
                        launchSingleTop = true
                        restoreState = true
                    }
                }
            )
        }
    }
}
