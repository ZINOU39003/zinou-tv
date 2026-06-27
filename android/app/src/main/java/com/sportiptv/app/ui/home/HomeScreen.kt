package com.sportiptv.app.ui.home

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.SportsSoccer
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.focus.onFocusChanged
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalConfiguration
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import coil3.compose.AsyncImage
import com.sportiptv.app.R
import com.sportiptv.app.data.remote.dto.MovieDto
import com.sportiptv.app.ui.components.LoadingIndicator

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
    onMovieItemClick: (String) -> Unit = {},
    viewModel: HomeViewModel = hiltViewModel()
) {
    val movies by viewModel.movies.collectAsState()
    val series by viewModel.series.collectAsState()
    val liveMatches by viewModel.liveMatches.collectAsState()
    val featuredChannels by viewModel.featuredChannels.collectAsState()
    val configuration = LocalConfiguration.current
    val isLandscape = configuration.orientation == android.content.res.Configuration.ORIENTATION_LANDSCAPE

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0A0A0A)) // Oscar TV deep background
    ) {
        androidx.compose.foundation.lazy.LazyColumn(
            modifier = Modifier.fillMaxSize(),
            contentPadding = PaddingValues(bottom = 80.dp) // padding for bottom nav
        ) {
            // World Cup Banner
            item {
                WorldCupBanner(onClick = onWorldCupClick)
            }

            // Matches Section
            if (liveMatches.isNotEmpty()) {
                item {
                    SectionTitle(title = "مباريات اليوم", icon = Icons.Default.SportsSoccer)
                    LazyRow(
                        horizontalArrangement = Arrangement.spacedBy(12.dp),
                        contentPadding = PaddingValues(horizontal = 16.dp)
                    ) {
                        items(liveMatches) { match ->
                            MatchCardMini(match = match, onClick = onSportsClick)
                        }
                    }
                    Spacer(modifier = Modifier.height(16.dp))
                }
            }

            // Top Leagues Section
            item {
                SectionTitle(title = "أقوى الدوريات العالمية وترتيبها", icon = Icons.Default.EmojiEvents)
                TopLeaguesRow()
                Spacer(modifier = Modifier.height(16.dp))
            }
        }
    }
}

@Composable
fun HeroSection(heroItem: MovieDto, onClick: (String) -> Unit) {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(400.dp)
            .clickable { heroItem.stream_url?.let { onClick(it) } }
    ) {
        AsyncImage(
            model = heroItem.poster_url,
            contentDescription = heroItem.title,
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop
        )
        // Dark gradient from bottom
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(
                        colors = listOf(Color.Transparent, Color(0xFF0A0A0A)),
                        startY = 300f
                    )
                )
        )
        // Content
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(16.dp),
            verticalArrangement = Arrangement.Bottom,
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(
                text = heroItem.title,
                color = Color.White,
                fontSize = 28.sp,
                fontWeight = FontWeight.Bold,
                textAlign = TextAlign.Center
            )
            Spacer(modifier = Modifier.height(8.dp))
            Row(
                horizontalArrangement = Arrangement.Center,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text("حركة ومغامرة • دراما", color = Color.Gray, fontSize = 12.sp)
            }
            Spacer(modifier = Modifier.height(12.dp))
            Row(
                horizontalArrangement = Arrangement.Center,
                verticalAlignment = Alignment.CenterVertically,
                modifier = Modifier.fillMaxWidth()
            ) {
                // Play Button
                Button(
                    onClick = { heroItem.stream_url?.let { onClick(it) } },
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFE50914)),
                    shape = RoundedCornerShape(8.dp),
                    modifier = Modifier.height(36.dp)
                ) {
                    Text("جاري العرض", color = Color.White)
                }
                Spacer(modifier = Modifier.width(16.dp))
                Text("2026", color = Color.LightGray, fontSize = 14.sp)
                Spacer(modifier = Modifier.width(16.dp))
                Text("8.3 ⭐", color = Color(0xFFFACC15), fontSize = 14.sp)
            }
        }
    }
}

@Composable
fun WorldCupBanner(onClick: () -> Unit) {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .padding(16.dp)
            .height(200.dp)
            .clip(RoundedCornerShape(16.dp))
            .clickable { onClick() }
    ) {
        AsyncImage(
            model = "https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExNTFkN2QzZjUzYWY2M2IzZTNhNGE2MjZhNTkyZThhNmQzNGZhOWUzNSZlcD12MV9pbnRlcm5hbF9naWZzX2dpZklkJmN0PWc/3o7aD0s6WnN0TfVb3C/giphy.gif",
            contentDescription = "World Cup Animation",
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop
        )
        Box(modifier = Modifier.fillMaxSize().background(Brush.horizontalGradient(listOf(Color.Black.copy(alpha=0.7f), Color.Transparent))))
        Column(
            modifier = Modifier.fillMaxSize().padding(16.dp),
            verticalArrangement = Arrangement.Center
        ) {
            Text("كأس العالم", color = Color.White, fontSize = 28.sp, fontWeight = FontWeight.Bold)
            Spacer(modifier = Modifier.height(8.dp))
            Text("النتائج الحصرية والمباريات المباشرة", color = Color(0xFFFFD700), fontSize = 16.sp, fontWeight = FontWeight.Bold)
        }
    }
}

@Composable
fun TopLeaguesRow() {
    val leagues = listOf(
        "كأس العالم" to "https://upload.wikimedia.org/wikipedia/en/thumb/e/e3/2022_FIFA_World_Cup.svg/1200px-2022_FIFA_World_Cup.svg.png",
        "الدوري الإنجليزي" to "https://upload.wikimedia.org/wikipedia/en/thumb/f/f2/Premier_League_Logo.svg/1200px-Premier_League_Logo.svg.png",
        "الدوري الإسباني" to "https://upload.wikimedia.org/wikipedia/commons/thumb/0/0f/LaLiga_logo_2023.svg/1200px-LaLiga_logo_2023.svg.png",
        "الدوري الإيطالي" to "https://upload.wikimedia.org/wikipedia/en/thumb/e/e1/Serie_A_logo_%282021%29.svg/1200px-Serie_A_logo_%282021%29.svg.png",
        "الدوري الفرنسي" to "https://upload.wikimedia.org/wikipedia/en/thumb/c/ca/Ligue_1_logo.svg/1200px-Ligue_1_logo.svg.png"
    )
    LazyRow(
        horizontalArrangement = Arrangement.spacedBy(16.dp),
        contentPadding = PaddingValues(horizontal = 16.dp)
    ) {
        items(leagues) { (name, logoUrl) ->
            Card(
                modifier = Modifier.width(110.dp).height(110.dp),
                shape = RoundedCornerShape(16.dp),
                colors = CardDefaults.cardColors(containerColor = Color(0xFF161616))
            ) {
                Column(
                    modifier = Modifier.fillMaxSize().padding(12.dp),
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.Center
                ) {
                    AsyncImage(
                        model = logoUrl,
                        contentDescription = name,
                        modifier = Modifier.size(50.dp),
                        contentScale = ContentScale.Fit
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    Text(name, color = Color.White, fontSize = 11.sp, textAlign = TextAlign.Center, fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}


@Composable
fun SectionTitle(title: String, subtitle: String? = null, icon: ImageVector? = null, onSubtitleClick: () -> Unit = {}) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 8.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            if (icon != null) {
                Icon(icon, contentDescription = null, tint = Color.White, modifier = Modifier.size(20.dp))
                Spacer(modifier = Modifier.width(8.dp))
            }
            Text(
                text = title,
                color = Color.White,
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold
            )
        }
        if (subtitle != null) {
            Text(
                text = subtitle,
                color = Color(0xFFE50914),
                fontSize = 14.sp,
                modifier = Modifier.clickable { onSubtitleClick() }
            )
        }
    }
}

@Composable
fun MatchCardMini(match: com.sportiptv.app.data.remote.dto.MatchDto, onClick: () -> Unit) {
    Card(
        modifier = Modifier
            .width(260.dp)
            .height(100.dp)
            .clickable { onClick() },
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0xFF1E1E1E))
    ) {
        Column(
            modifier = Modifier.fillMaxSize().padding(8.dp),
            verticalArrangement = Arrangement.Center
        ) {
            val isArabic = java.util.Locale.getDefault().language == "ar"
            val teamOne = if (isArabic && !match.team_one_name_ar.isNullOrEmpty()) match.team_one_name_ar else match.team_one_name
            val teamTwo = if (isArabic && !match.team_two_name_ar.isNullOrEmpty()) match.team_two_name_ar else match.team_two_name
            val tournamentName = match.tournament?.let { if (isArabic && !it.name_ar.isNullOrEmpty()) it.name_ar else it.name } ?: "مباراة"

            Text(tournamentName, color = Color.Gray, fontSize = 10.sp)
            Spacer(modifier = Modifier.height(8.dp))
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.weight(1f)) {
                    AsyncImage(model = match.team_one_flag, contentDescription = null, modifier = Modifier.size(24.dp))
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(teamOne, color = Color.White, fontSize = 12.sp, maxLines = 1, overflow = TextOverflow.Ellipsis)
                }
                Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.padding(horizontal = 8.dp)) {
                    Text(match.match_time, color = Color.LightGray, fontSize = 14.sp, fontWeight = FontWeight.Bold)
                    Text(if (match.is_live) "مباشر" else "قريباً", color = if (match.is_live) Color(0xFFE50914) else Color.Gray, fontSize = 10.sp)
                }
                Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.weight(1f), horizontalArrangement = Arrangement.End) {
                    Text(teamTwo, color = Color.White, fontSize = 12.sp, maxLines = 1, overflow = TextOverflow.Ellipsis)
                    Spacer(modifier = Modifier.width(8.dp))
                    AsyncImage(model = match.team_two_flag, contentDescription = null, modifier = Modifier.size(24.dp))
                }
            }
        }
    }
}

@Composable
fun MoviePosterCard(movie: MovieDto, onClick: (String) -> Unit) {
    var isFocused by remember { mutableStateOf(false) }
    Card(
        modifier = Modifier
            .width(120.dp)
            .height(180.dp)
            .onFocusChanged { isFocused = it.isFocused }
            .border(
                width = if (isFocused) 3.dp else 1.dp,
                color = if (isFocused) Color(0xFFFF9800) else Color(0x33FFFFFF),
                shape = RoundedCornerShape(8.dp)
            )
            .clip(RoundedCornerShape(8.dp))
            .clickable { movie.stream_url?.let { onClick(it) } },
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0xFF1E222D))
    ) {
        Box(modifier = Modifier.fillMaxSize()) {
            AsyncImage(
                model = movie.poster_url,
                contentDescription = movie.title,
                modifier = Modifier.fillMaxSize(),
                contentScale = ContentScale.Crop
            )
            // Gradient Overlay
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(
                        Brush.verticalGradient(
                            colors = listOf(Color.Transparent, Color.Black.copy(alpha = 0.9f)),
                            startY = 100f
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
                    text = movie.title,
                    color = Color.White,
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Bold,
                    textAlign = TextAlign.Center,
                    maxLines = 2,
                    overflow = TextOverflow.Ellipsis
                )
            }
        }
    }
}


