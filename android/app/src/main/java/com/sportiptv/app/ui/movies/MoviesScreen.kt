package com.sportiptv.app.ui.movies

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Movie
import androidx.compose.material.icons.filled.Star
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
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
import androidx.hilt.navigation.compose.hiltViewModel
import coil3.compose.AsyncImage
import com.sportiptv.app.data.remote.dto.MovieDto
import com.sportiptv.app.ui.theme.*

@Composable
fun MoviesScreen(
    onMovieClick: (MovieDto) -> Unit,
    viewModel: MoviesViewModel = hiltViewModel()
) {
    val movies by viewModel.movies.collectAsState()
    val isLoading by viewModel.isLoading.collectAsState()
    val errorMessage by viewModel.errorMessage.collectAsState()
    val currentFilter by viewModel.typeFilter.collectAsState()

    val isArabic = java.util.Locale.getDefault().language == "ar"

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0A0A0A)) // Oscar TV Dark
    ) {
        // Header
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Text(
                    text = if (isArabic) "الأفلام والمسلسلات" else "Movies & Series",
                    color = Color.White,
                    fontSize = 22.sp,
                    fontWeight = FontWeight.ExtraBold
                )
            }
        }

        // Filter Chips Row
        androidx.compose.foundation.lazy.LazyRow(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 8.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            item {
                MovieFilterChip(
                    label = if (isArabic) "الكل" else "All",
                    isSelected = currentFilter == null,
                    onClick = { viewModel.setTypeFilter(null) }
                )
            }
            item {
                MovieFilterChip(
                    label = if (isArabic) "أفلام" else "Movies",
                    isSelected = currentFilter == "movie",
                    onClick = { viewModel.setTypeFilter("movie") }
                )
            }
            item {
                MovieFilterChip(
                    label = if (isArabic) "مسلسلات" else "Series",
                    isSelected = currentFilter == "series",
                    onClick = { viewModel.setTypeFilter("series") }
                )
            }
        }

        // Content
        Box(modifier = Modifier.fillMaxSize().weight(1f)) {
            when {
                isLoading -> {
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        CircularProgressIndicator(color = Primary)
                    }
                }
                errorMessage != null -> {
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(
                                text = errorMessage ?: "Error",
                                color = TextMuted,
                                fontSize = 13.sp
                            )
                            Spacer(modifier = Modifier.height(16.dp))
                            Button(
                                onClick = { viewModel.refresh() },
                                colors = ButtonDefaults.buttonColors(containerColor = Primary)
                            ) {
                                Text(if (isArabic) "إعادة المحاولة" else "Retry", color = Color.White)
                            }
                        }
                    }
                }
                movies.isEmpty() -> {
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        Text(
                            text = if (isArabic) "لا توجد أفلام أو مسلسلات" else "No items available",
                            color = TextMuted
                        )
                    }
                }
                else -> {
                    LazyVerticalGrid(
                        columns = GridCells.Fixed(3),
                        modifier = Modifier.fillMaxSize(),
                        contentPadding = PaddingValues(horizontal = 12.dp, vertical = 8.dp),
                        horizontalArrangement = Arrangement.spacedBy(8.dp),
                        verticalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        items(movies) { movie ->
                            MovieCard(
                                movie = movie,
                                isArabic = isArabic,
                                onClick = { url -> onMovieClick(url) }
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun MovieFilterChip(
    label: String,
    isSelected: Boolean,
    onClick: () -> Unit
) {
    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(20.dp))
            .background(if (isSelected) Primary else Color(0xFF1E1E1E))
            .clickable { onClick() }
            .padding(horizontal = 16.dp, vertical = 8.dp)
    ) {
        Text(
            text = label,
            color = if (isSelected) Color.White else Color.Gray,
            fontSize = 14.sp,
            fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal
        )
    }
}

@Composable
private fun MovieCard(
    movie: MovieDto,
    isArabic: Boolean,
    onClick: (MovieDto) -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .aspectRatio(0.66f) // 2:3 aspect ratio for posters
            .clickable { onClick(movie) },
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0xFF1E1E1E))
    ) {
        Box(modifier = Modifier.fillMaxSize()) {
            // Poster Image
            if (!movie.poster_url.isNullOrEmpty()) {
                AsyncImage(
                    model = movie.poster_url,
                    contentDescription = movie.title,
                    modifier = Modifier.fillMaxSize(),
                    contentScale = ContentScale.Crop
                )
            } else {
                Box(modifier = Modifier.fillMaxSize().background(Color.DarkGray))
            }

            // Gradient overlay
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(
                        brush = Brush.verticalGradient(
                            colors = listOf(Color.Transparent, Color(0xDD000000)),
                            startY = 200f
                        )
                    )
            )

            // Tag Badge (top-right)
            Box(
                modifier = Modifier
                    .align(Alignment.TopEnd)
                    .padding(4.dp)
                    .background(
                        color = if (movie.type == "series") Color(0xFF6C3FC5) else Primary,
                        shape = RoundedCornerShape(4.dp)
                    )
                    .padding(horizontal = 6.dp, vertical = 2.dp)
            ) {
                Text(
                    text = if (isArabic) {
                        if (movie.type == "series") "مسلسل" else "فيلم"
                    } else {
                        if (movie.type == "series") "Series" else "Movie"
                    },
                    color = Color.White,
                    fontSize = 10.sp,
                    fontWeight = FontWeight.Bold
                )
            }

            // Title and Info
            Column(
                modifier = Modifier
                    .align(Alignment.BottomStart)
                    .padding(8.dp)
            ) {
                Text(
                    text = if (isArabic && !movie.title_ar.isNullOrEmpty()) movie.title_ar else movie.title,
                    color = Color.White,
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Bold,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis
                )
                Spacer(modifier = Modifier.height(2.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.Star, contentDescription = null, tint = Color(0xFFFACC15), modifier = Modifier.size(10.dp))
                    Spacer(modifier = Modifier.width(2.dp))
                    Text(
                        text = String.format("%.1f", movie.rating ?: 0.0),
                        color = Color(0xFFFACC15),
                        fontSize = 10.sp,
                        fontWeight = FontWeight.Bold
                    )
                    if (movie.year != null) {
                        Spacer(modifier = Modifier.width(6.dp))
                        Text(
                            text = movie.year.toString(),
                            color = Color.LightGray,
                            fontSize = 10.sp
                        )
                    }
                }
            }
        }
    }
}
