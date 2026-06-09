package com.sportiptv.app.ui.home

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.focus.onFocusChanged
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import com.sportiptv.app.R
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.ui.components.LoadingIndicator
import com.sportiptv.app.ui.theme.*

@Composable
fun HomeScreen(
    onChannelClick: (Long) -> Unit,
    onCategoryClick: (Long?) -> Unit,
    onSearchClick: () -> Unit,
    onWorldCupClick: () -> Unit,
    onMoviesClick: () -> Unit = {},
    onSportsClick: () -> Unit = {},
    onSettingsClick: () -> Unit = {},
    onExitClick: () -> Unit = {},
    onSubscriptionClick: () -> Unit = {},
    onActivationClick: () -> Unit = {},
    viewModel: HomeViewModel = hiltViewModel()
) {
    val syncState by viewModel.syncState.collectAsState()
    val categories by viewModel.categories.collectAsState()
    val isArabic = java.util.Locale.getDefault().language == "ar"
    val context = LocalContext.current

    // Observe sync results
    LaunchedEffect(syncState) {
        if (syncState is Resource.Success) {
            android.widget.Toast.makeText(
                context,
                if (isArabic) "تم تحديث القنوات بنجاح!" else "Playlist updated successfully!",
                android.widget.Toast.LENGTH_SHORT
            ).show()
        } else if (syncState is Resource.Error) {
            android.widget.Toast.makeText(
                context,
                if (isArabic) "فشل تحديث القنوات" else "Failed to update playlist",
                android.widget.Toast.LENGTH_SHORT
            ).show()
        }
    }

    // Dynamic resolution of Series Category from channel database
    val handleSeriesClick = {
        val seriesCat = categories.find {
            val name = (it.name + " " + (it.nameAr ?: "")).lowercase()
            name.contains("series") || name.contains("مسلسلات") || name.contains("مسلسل")
        }
        if (seriesCat != null) {
            onCategoryClick(seriesCat.id)
        } else {
            onCategoryClick(null)
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(BgPrimary)
    ) {
        // Luxury wave background
        Image(
            painter = painterResource(id = R.drawable.gold_luxury_bg),
            contentDescription = null,
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop
        )

        // Dark dim overlay for better contrast
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Color.Black.copy(alpha = 0.45f))
        )

        if (syncState is Resource.Loading) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Color.Black.copy(alpha = 0.7f)),
                contentAlignment = Alignment.Center
            ) {
                LoadingIndicator(message = if (isArabic) "جاري تحديث قائمة القنوات..." else "Syncing channel playlist...")
            }
        }

        val configuration = androidx.compose.ui.platform.LocalConfiguration.current
        val isLandscape = configuration.orientation == android.content.res.Configuration.ORIENTATION_LANDSCAPE

        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = if (isLandscape) 32.dp else 16.dp, vertical = 16.dp),
            verticalArrangement = Arrangement.SpaceBetween
        ) {
            // ── Top Header Brand ──
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = if (isArabic) "زينو تي في • ZINOU TV" else "ZINOU TV • Premium Launcher",
                    color = Primary,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.ExtraBold,
                    letterSpacing = 1.sp
                )
                Text(
                    text = if (isArabic) "مرحباً بك في البث المباشر" else "Welcome to Live TV",
                    color = TextMuted,
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Medium
                )
            }

            if (isLandscape) {
                // ── LANDSCAPE TV LAUNCHER LAYOUT ──
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1f)
                        .padding(vertical = 12.dp),
                    horizontalArrangement = Arrangement.spacedBy(20.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Column 1: Live TV (Left prominent vertical card)
                    LauncherCard(
                        onClick = { onCategoryClick(null) },
                        modifier = Modifier
                            .weight(1.1f)
                            .fillMaxHeight(),
                        border = BorderStroke(1.5.dp, Primary.copy(alpha = 0.4f))
                    ) {
                        Column(
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center,
                            modifier = Modifier.padding(16.dp)
                        ) {
                            Icon(
                                imageVector = Icons.Default.Tv,
                                contentDescription = null,
                                tint = Primary,
                                modifier = Modifier.size(76.dp)
                            )
                            Spacer(modifier = Modifier.height(16.dp))
                            Text(
                                text = if (isArabic) "البث المباشر" else "Live TV",
                                color = Color.White,
                                fontSize = 20.sp,
                                fontWeight = FontWeight.Bold,
                                textAlign = TextAlign.Center
                            )
                        }
                    }

                    // Column 2: 2x2 Grid (Movies, Series, Sports, Playlist)
                    Column(
                        modifier = Modifier
                            .weight(2f)
                            .fillMaxHeight(),
                        verticalArrangement = Arrangement.spacedBy(16.dp)
                    ) {
                        // Row 1
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .weight(1f),
                            horizontalArrangement = Arrangement.spacedBy(16.dp)
                        ) {
                            LauncherCard(
                                onClick = onWorldCupClick,
                                modifier = Modifier.weight(1f)
                            ) {
                                Box(modifier = Modifier.fillMaxSize()) {
                                    Image(
                                        painter = painterResource(id = R.drawable.world_cup_launcher_btn),
                                        contentDescription = null,
                                        modifier = Modifier.fillMaxSize(),
                                        contentScale = ContentScale.Crop
                                    )
                                    Box(
                                        modifier = Modifier
                                            .fillMaxSize()
                                            .background(
                                                Brush.verticalGradient(
                                                    colors = listOf(Color.Transparent, Color.Black.copy(alpha = 0.85f))
                                                )
                                            )
                                    )
                                    Column(
                                        modifier = Modifier
                                            .fillMaxSize()
                                            .padding(10.dp),
                                        verticalArrangement = Arrangement.Bottom,
                                        horizontalAlignment = Alignment.CenterHorizontally
                                    ) {
                                        Text(
                                            text = if (isArabic) "قنوات كأس العالم 2026" else "World Cup 2026 Channels",
                                            color = Color.White,
                                            fontSize = 13.sp,
                                            fontWeight = FontWeight.Bold,
                                            textAlign = TextAlign.Center,
                                            maxLines = 1,
                                            overflow = TextOverflow.Ellipsis
                                        )
                                    }
                                }
                            }
                            LauncherCard(
                                onClick = onActivationClick,
                                modifier = Modifier.weight(1f)
                            ) {
                                GridCardContent(
                                    icon = Icons.Default.VpnKey,
                                    label = if (isArabic) "تسجيل الدخول لحساب ZINOU TV PRO" else "Login ZINOU TV PRO"
                                )
                            }
                        }

                        // Row 2
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .weight(1f),
                            horizontalArrangement = Arrangement.spacedBy(16.dp)
                        ) {
                            LauncherCard(
                                onClick = onSportsClick,
                                modifier = Modifier.weight(1f)
                            ) {
                                GridCardContent(
                                    icon = Icons.Default.SportsSoccer,
                                    label = if (isArabic) "المباريات" else "Sports"
                                )
                            }
                            LauncherCard(
                                onClick = { viewModel.syncData() },
                                modifier = Modifier.weight(1f)
                            ) {
                                GridCardContent(
                                    icon = Icons.Default.QueuePlayNext,
                                    label = if (isArabic) "تحديث القائمة" else "Playlist Sync"
                                )
                            }
                        }
                    }

                    // Column 3: Preview Box & Bottom Buttons (Right column)
                    Column(
                        modifier = Modifier
                            .weight(1.4f)
                            .fillMaxHeight(),
                        verticalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        // Preview box with glowing logo
                        // Preview box with glowing logo / Subscribe button
                        LauncherCard(
                            onClick = onSubscriptionClick,
                            modifier = Modifier
                                .fillMaxWidth()
                                .weight(1f),
                            border = BorderStroke(1.5.dp, Primary)
                        ) {
                            Box(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .background(
                                        Brush.verticalGradient(
                                            colors = listOf(Color(0xFF161025), Color(0xFF0C0718))
                                        )
                                    )
                                    .padding(12.dp),
                                contentAlignment = Alignment.Center
                            ) {
                                Column(
                                    horizontalAlignment = Alignment.CenterHorizontally,
                                    verticalArrangement = Arrangement.Center
                                ) {
                                    Image(
                                        painter = painterResource(id = R.drawable.zinou_tv_logo),
                                        contentDescription = "ZINOU TV Logo",
                                        modifier = Modifier
                                            .size(76.dp)
                                            .clip(RoundedCornerShape(8.dp)),
                                        contentScale = ContentScale.Fit
                                    )
                                    Spacer(modifier = Modifier.height(10.dp))
                                    Text(
                                        text = if (isArabic) "اشترك في ZINOU TV PRO بدون اعلان" else "Subscribe to ZINOU TV PRO (No Ads)",
                                        color = Primary,
                                        fontSize = 13.sp,
                                        fontWeight = FontWeight.ExtraBold,
                                        textAlign = TextAlign.Center,
                                        maxLines = 2,
                                        overflow = TextOverflow.Ellipsis
                                    )
                                }
                                Icon(
                                    imageVector = Icons.Default.ChevronRight,
                                    contentDescription = null,
                                    tint = Primary,
                                    modifier = Modifier
                                        .align(Alignment.CenterEnd)
                                        .size(24.dp)
                                )
                            }
                        }

                        // Bottom Actions Row (Settings, Refresh, Exit)
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(52.dp),
                            horizontalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            LauncherIconButton(
                                onClick = onSettingsClick,
                                icon = Icons.Default.Settings,
                                modifier = Modifier.weight(1f)
                            )
                            LauncherIconButton(
                                onClick = { viewModel.syncData() },
                                icon = Icons.Default.Refresh,
                                modifier = Modifier.weight(1f)
                            )
                            LauncherIconButton(
                                onClick = onExitClick,
                                icon = Icons.Default.ExitToApp,
                                modifier = Modifier.weight(1f)
                            )
                        }
                    }
                }
            } else {
                // ── PORTRAIT RESPONSIVE LAYOUT (Phones) ──
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1f)
                        .padding(vertical = 12.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Live TV large card
                    LauncherCard(
                        onClick = { onCategoryClick(null) },
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(130.dp),
                        border = BorderStroke(1.5.dp, Primary.copy(alpha = 0.4f))
                    ) {
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.Center,
                            modifier = Modifier.padding(16.dp)
                        ) {
                            Icon(
                                imageVector = Icons.Default.Tv,
                                contentDescription = null,
                                tint = Primary,
                                modifier = Modifier.size(54.dp)
                            )
                            Spacer(modifier = Modifier.width(16.dp))
                            Text(
                                text = if (isArabic) "البث المباشر" else "Live TV",
                                color = Color.White,
                                fontSize = 20.sp,
                                fontWeight = FontWeight.Bold
                            )
                        }
                    }

                    // 2x2 Grid of cards
                    Column(
                        modifier = Modifier.fillMaxWidth(),
                        verticalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            LauncherCard(
                                onClick = onWorldCupClick,
                                modifier = Modifier
                                    .weight(1f)
                                    .height(90.dp)
                            ) {
                                Box(modifier = Modifier.fillMaxSize()) {
                                    Image(
                                        painter = painterResource(id = R.drawable.world_cup_launcher_btn),
                                        contentDescription = null,
                                        modifier = Modifier.fillMaxSize(),
                                        contentScale = ContentScale.Crop
                                    )
                                    Box(
                                        modifier = Modifier
                                            .fillMaxSize()
                                            .background(
                                                Brush.verticalGradient(
                                                    colors = listOf(Color.Transparent, Color.Black.copy(alpha = 0.85f))
                                                )
                                            )
                                    )
                                    Column(
                                        modifier = Modifier
                                            .fillMaxSize()
                                            .padding(8.dp),
                                        verticalArrangement = Arrangement.Bottom,
                                        horizontalAlignment = Alignment.CenterHorizontally
                                    ) {
                                        Text(
                                            text = if (isArabic) "كأس العالم 2026" else "World Cup 2026",
                                            color = Color.White,
                                            fontSize = 12.sp,
                                            fontWeight = FontWeight.Bold,
                                            textAlign = TextAlign.Center,
                                            maxLines = 1,
                                            overflow = TextOverflow.Ellipsis
                                        )
                                    }
                                }
                            }
                            LauncherCard(
                                onClick = onActivationClick,
                                modifier = Modifier
                                    .weight(1f)
                                    .height(90.dp)
                            ) {
                                GridCardContent(
                                    icon = Icons.Default.VpnKey,
                                    label = if (isArabic) "تسجيل الدخول لحساب ZINOU TV PRO" else "Login ZINOU TV PRO"
                                )
                            }
                        }
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            LauncherCard(
                                onClick = onSportsClick,
                                modifier = Modifier
                                    .weight(1f)
                                    .height(90.dp)
                            ) {
                                GridCardContent(icon = Icons.Default.SportsSoccer, label = if (isArabic) "المباريات" else "Sports")
                            }
                            LauncherCard(
                                onClick = { viewModel.syncData() },
                                modifier = Modifier
                                    .weight(1f)
                                    .height(90.dp)
                            ) {
                                GridCardContent(icon = Icons.Default.QueuePlayNext, label = if (isArabic) "تحديث القائمة" else "Playlist Sync")
                            }
                        }
                    }

                    // Preview slide logo box
                    // Preview slide logo box / Subscribe button
                    LauncherCard(
                        onClick = onSubscriptionClick,
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(130.dp),
                        border = BorderStroke(1.5.dp, Primary)
                    ) {
                        Box(
                            modifier = Modifier
                                .fillMaxSize()
                                .background(
                                    Brush.verticalGradient(
                                        colors = listOf(Color(0xFF161025), Color(0xFF0C0718))
                                    )
                                )
                                .padding(12.dp),
                            contentAlignment = Alignment.Center
                        ) {
                            Row(
                                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.SpaceBetween
                            ) {
                                Image(
                                    painter = painterResource(id = R.drawable.zinou_tv_logo),
                                    contentDescription = "ZINOU TV Logo",
                                    modifier = Modifier.size(70.dp),
                                    contentScale = ContentScale.Fit
                                )
                                Spacer(modifier = Modifier.width(12.dp))
                                Text(
                                    text = if (isArabic) "اشترك في ZINOU TV PRO بدون اعلان" else "Subscribe to ZINOU TV PRO (No Ads)",
                                    color = Primary,
                                    fontSize = 15.sp,
                                    fontWeight = FontWeight.ExtraBold,
                                    textAlign = TextAlign.Right,
                                    modifier = Modifier.weight(1f)
                                )
                            }
                        }
                    }

                    Spacer(modifier = Modifier.weight(1f))

                    // Action buttons row (Settings, Refresh, Exit)
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(50.dp),
                        horizontalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        LauncherIconButton(
                            onClick = onSettingsClick,
                            icon = Icons.Default.Settings,
                            modifier = Modifier.weight(1f)
                        )
                        LauncherIconButton(
                            onClick = { viewModel.syncData() },
                            icon = Icons.Default.Refresh,
                            modifier = Modifier.weight(1f)
                        )
                        LauncherIconButton(
                            onClick = onExitClick,
                            icon = Icons.Default.ExitToApp,
                            modifier = Modifier.weight(1f)
                        )
                    }
                }
            }
        }
    }
}

@Composable
fun LauncherCard(
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    border: BorderStroke = BorderStroke(1.dp, Color(0x13FFFFFF)),
    content: @Composable BoxScope.() -> Unit
) {
    var isFocused by remember { mutableStateOf(false) }
    Card(
        modifier = modifier
            .onFocusChanged { isFocused = it.isFocused }
            .border(
                border = if (isFocused) BorderStroke(2.dp, Primary) else border,
                shape = RoundedCornerShape(16.dp)
            )
            .clip(RoundedCornerShape(16.dp))
            .clickable { onClick() },
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0x880E081B))
    ) {
        Box(
            modifier = Modifier.fillMaxSize(),
            contentAlignment = Alignment.Center,
            content = content
        )
    }
}

@Composable
fun GridCardContent(
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    label: String
) {
    Column(
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center,
        modifier = Modifier.padding(10.dp)
    ) {
        Icon(
            imageVector = icon,
            contentDescription = null,
            tint = Primary,
            modifier = Modifier.size(32.dp)
        )
        Spacer(modifier = Modifier.height(8.dp))
        Text(
            text = label,
            color = Color.White,
            fontSize = 13.sp,
            fontWeight = FontWeight.Bold,
            textAlign = TextAlign.Center
        )
    }
}

@Composable
fun LauncherIconButton(
    onClick: () -> Unit,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    modifier: Modifier = Modifier
) {
    var isFocused by remember { mutableStateOf(false) }
    Box(
        modifier = modifier
            .fillMaxHeight()
            .clip(RoundedCornerShape(12.dp))
            .background(Color(0x880E081B))
            .onFocusChanged { isFocused = it.isFocused }
            .border(
                border = if (isFocused) BorderStroke(2.dp, Primary) else BorderStroke(1.dp, Primary.copy(alpha = 0.3f)),
                shape = RoundedCornerShape(12.dp)
            )
            .clickable { onClick() },
        contentAlignment = Alignment.Center
    ) {
        Icon(
            imageVector = icon,
            contentDescription = null,
            tint = Primary,
            modifier = Modifier.size(24.dp)
        )
    }
}
