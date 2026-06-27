package com.sportiptv.app.ui.channels

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.foundation.basicMarquee
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.foundation.Image
import androidx.hilt.navigation.compose.hiltViewModel
import com.sportiptv.app.R
import com.sportiptv.app.util.Constants
import coil3.compose.AsyncImage
import com.sportiptv.app.util.ImageUrlResolver
import com.sportiptv.app.domain.model.Category
import com.sportiptv.app.domain.model.Package
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.ui.components.ChannelCard
import com.sportiptv.app.ui.theme.*
import java.util.Locale

private sealed class GridEntry {
    data class CategoryItem(val category: Category) : GridEntry()
    data class PackageItem(val pkg: Package) : GridEntry()
    data object AdSlot : GridEntry()
}

private fun <T> buildGridWithAd(items: List<T>, mapItem: (T) -> GridEntry): List<GridEntry> {
    if (items.size <= 2) return items.map(mapItem)
    val result = mutableListOf<GridEntry>()
    items.forEachIndexed { index, item ->
        if (index == 2) result.add(GridEntry.AdSlot)
        result.add(mapItem(item))
    }
    return result
}

@Composable
fun ChannelsScreen(
    initialCategoryId: Long? = null,
    onChannelClick: (Long) -> Unit,
    onNavigateHome: () -> Unit = {},
    viewModel: ChannelsViewModel = hiltViewModel()
) {
    val configuration = androidx.compose.ui.platform.LocalConfiguration.current
    val isLandscape = configuration.orientation == android.content.res.Configuration.ORIENTATION_LANDSCAPE
    val columnsCount = if (isLandscape) 8 else 4

    val categories by viewModel.categories.collectAsState()
    val packages by viewModel.packages.collectAsState()
    val channels by viewModel.channels.collectAsState()
    val selectedCategoryId by viewModel.selectedCategoryId.collectAsState()
    val selectedPackageId by viewModel.selectedPackageId.collectAsState()
    val syncState by viewModel.syncState.collectAsState()
    val configState by viewModel.appConfigState.collectAsState()

    val adsEnabled = remember(configState) {
        (configState as? Resource.Success)?.data?.ads_enabled == true
    }

    val bannerAdUnitId = remember(configState) {
        (configState as? Resource.Success)?.data?.admob_banner_ad_unit_id
            ?: Constants.ADMOB_BANNER_AD_UNIT_ID
    }

    LaunchedEffect(initialCategoryId) {
        if (initialCategoryId != null) {
            viewModel.selectCategory(initialCategoryId)
        }
    }

    val isArabic = Locale.getDefault().language == "ar"
    val emptyMsg = if (isArabic) "لا توجد قنوات أو باقات متوفرة" else "No items available"
    val syncingMsg = if (isArabic) "جاري تحميل البيانات..." else "Loading data..."

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0A0A0A))
    ) {
        // Top Bar
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(start = 16.dp, end = 16.dp, top = 16.dp, bottom = 8.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
            IconButton(onClick = {
                if (selectedPackageId != null) {
                    viewModel.selectPackage(null)
                } else if (selectedCategoryId != null && initialCategoryId == null) {
                    viewModel.selectCategory(null)
                } else {
                    onNavigateHome()
                }
            }) {
                Icon(Icons.Default.ArrowBack, contentDescription = "Back", tint = Color.White)
            }
            Text(
                text = when {
                    selectedPackageId != null -> packages.find { it.id == selectedPackageId }?.let { if (isArabic && !it.nameAr.isNullOrEmpty()) it.nameAr else it.name } ?: "القنوات"
                    selectedCategoryId != null -> categories.find { it.id == selectedCategoryId }?.let { if (isArabic && !it.nameAr.isNullOrEmpty()) it.nameAr else it.name } ?: "الباقات"
                    else -> "الشبكات"
                },
                color = Color.White,
                fontSize = 20.sp,
                fontWeight = FontWeight.Bold
            )
            IconButton(onClick = { /* TODO: Search */ }) {
                Icon(Icons.Default.Search, contentDescription = "Search", tint = Color.White)
            }
        }

        // Content Area
        Box(modifier = Modifier.fillMaxSize().weight(1f)) {
            when {
                syncState is Resource.Loading && categories.isEmpty() -> {
                    LoadingState(syncingMsg)
                }
                selectedCategoryId == null -> {
                    // Show Categories Grid
                    if (categories.isEmpty()) {
                        EmptyState(emptyMsg, isArabic, showRetry = true) { viewModel.retrySync() }
                    } else {
                        val entries = buildGridWithAd(categories) { GridEntry.CategoryItem(it) }
                        NetworkPackageGrid(
                            entries = entries,
                            columnsCount = columnsCount,
                            isArabic = isArabic,
                            bannerAdUnitId = bannerAdUnitId,
                            onCategoryClick = { viewModel.selectCategory(it) },
                            onPackageClick = {}
                        )
                    }
                }
                selectedPackageId == null && packages.isNotEmpty() -> {
                    // Show Packages Grid
                    val entries = buildGridWithAd(packages) { GridEntry.PackageItem(it) }
                    NetworkPackageGrid(
                        entries = entries,
                        columnsCount = columnsCount,
                        isArabic = isArabic,
                        bannerAdUnitId = bannerAdUnitId,
                        onCategoryClick = {},
                        onPackageClick = { viewModel.selectPackage(it) }
                    )
                }
                else -> {
                    // Show Channels Grid
                    if (channels.isEmpty()) {
                        EmptyState(emptyMsg, isArabic, showRetry = true) { viewModel.retrySync() }
                    } else {
                        ChannelsGrid(channels, isLandscape, onChannelClick, viewModel)
                    }
                }
            }
        }

        if (adsEnabled) {
            Box(modifier = Modifier.fillMaxWidth().background(Color(0xFF0A0A0A))) {
                AdBanner(adUnitId = bannerAdUnitId)
            }
        }
    }
}

@Composable
private fun ChannelsGrid(
    channels: List<com.sportiptv.app.domain.model.Channel>,
    isLandscape: Boolean,
    onChannelClick: (Long) -> Unit,
    viewModel: ChannelsViewModel
) {
    LazyVerticalGrid(
        columns = GridCells.Fixed(if (isLandscape) 6 else 3),
        modifier = Modifier.fillMaxSize(),
        contentPadding = PaddingValues(start = 12.dp, top = 0.dp, end = 12.dp, bottom = 16.dp),
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        verticalArrangement = Arrangement.spacedBy(8.dp)
    ) {
        items(channels, key = { it.id }) { channel ->
            ChannelCard(
                channel = channel,
                onClick = { onChannelClick(channel.id) },
                onFavoriteToggle = { viewModel.toggleFavorite(channel.id, !channel.isFavorited) }
            )
        }
    }
}

@Composable
private fun NetworkPackageGrid(
    entries: List<GridEntry>,
    columnsCount: Int,
    isArabic: Boolean,
    bannerAdUnitId: String,
    onCategoryClick: (Long) -> Unit,
    onPackageClick: (Long) -> Unit
) {
    LazyVerticalGrid(
        columns = GridCells.Fixed(columnsCount),
        modifier = Modifier.fillMaxSize(),
        contentPadding = PaddingValues(start = 12.dp, top = 8.dp, end = 12.dp, bottom = 20.dp),
        horizontalArrangement = Arrangement.spacedBy(12.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        items(entries, key = {
            when (it) {
                is GridEntry.PackageItem -> "pkg_${it.pkg.id}"
                is GridEntry.CategoryItem -> "cat_${it.category.id}"
                GridEntry.AdSlot -> "ad"
            }
        }) { entry ->
            when (entry) {
                GridEntry.AdSlot -> GridAdBanner(adUnitId = bannerAdUnitId)
                is GridEntry.PackageItem -> NetworkOrPackageCard(
                    id = entry.pkg.id.toString(),
                    name = if (isArabic) entry.pkg.nameAr ?: entry.pkg.name else entry.pkg.name,
                    logoUrl = entry.pkg.logoUrl ?: "",
                    onClick = { onPackageClick(entry.pkg.id) }
                )
                is GridEntry.CategoryItem -> NetworkOrPackageCard(
                    id = entry.category.id.toString(),
                    name = if (isArabic) entry.category.nameAr ?: entry.category.name else entry.category.name,
                    logoUrl = entry.category.icon ?: "",
                    onClick = { onCategoryClick(entry.category.id) }
                )
            }
        }
    }
}

@Composable
fun NetworkOrPackageCard(id: String, name: String, logoUrl: String, onClick: () -> Unit) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .aspectRatio(0.9f) // Slightly taller to fit marquee text nicely
            .clickable { onClick() },
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0xFF111111))
    ) {
        Column(
            modifier = Modifier.fillMaxSize().padding(12.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Box(
                modifier = Modifier
                    .weight(1f)
                    .fillMaxWidth()
                    .padding(horizontal = 8.dp, vertical = 4.dp)
                    .background(Color.Black, shape = RoundedCornerShape(16.dp))
                    .border(1.dp, Color(0x33FFFFFF), RoundedCornerShape(16.dp)),
                contentAlignment = Alignment.Center
            ) {
                val resolvedUrl = ImageUrlResolver.resolve(logoUrl)
                if (!resolvedUrl.isNullOrBlank()) {
                    AsyncImage(
                        model = resolvedUrl,
                        contentDescription = null,
                        modifier = Modifier.fillMaxSize().padding(12.dp),
                        contentScale = ContentScale.Fit
                    )
                } else {
                    // Mapping logic based on network name
                    val lowerName = name.lowercase(Locale.getDefault())
                    val iconRes = when {
                        lowerName.contains("sport") || lowerName.contains("رياضة") || lowerName.contains("bein") -> R.drawable.ic_sports // You'll need to add this or use ImageVector
                        lowerName.contains("news") || lowerName.contains("اخبار") -> R.drawable.ic_news
                        lowerName.contains("kid") || lowerName.contains("اطفال") -> R.drawable.ic_kids
                        lowerName.contains("movie") || lowerName.contains("افلام") -> R.drawable.ic_movies
                        lowerName.contains("vip") -> R.drawable.ic_vip
                        lowerName.contains("ar") || lowerName.contains("عرب") -> R.drawable.ic_arab
                        lowerName.contains("fr") || lowerName.contains("france") -> R.drawable.ic_france
                        lowerName.contains("uk") || lowerName.contains("england") -> R.drawable.ic_uk
                        else -> null
                    }
                    
                    if (iconRes != null) {
                        Image(
                            painter = androidx.compose.ui.res.painterResource(id = iconRes),
                            contentDescription = name,
                            modifier = Modifier.fillMaxSize().padding(8.dp),
                            contentScale = ContentScale.Fit
                        )
                    } else {
                        // Fallback to text if no mapped icon
                        Text(name.take(2).uppercase(), color = Primary, fontSize = 24.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                text = name,
                color = Color.LightGray,
                fontSize = 13.sp,
                fontWeight = FontWeight.Bold,
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth().basicMarquee(),
                maxLines = 1
            )
        }
    }
}

@Composable
fun LoadingState(message: String) {
    var showLoading by remember { mutableStateOf(false) }
    LaunchedEffect(Unit) {
        kotlinx.coroutines.delay(250L)
        showLoading = true
    }
    if (showLoading) {
        Box(
            modifier = Modifier.fillMaxWidth().fillMaxHeight(0.8f),
            contentAlignment = Alignment.Center
        ) {
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                CircularProgressIndicator(color = Primary)
                Spacer(modifier = Modifier.height(12.dp))
                Text(text = message, color = TextMuted, fontSize = 14.sp)
            }
        }
    }
}

@Composable
fun EmptyState(
    message: String,
    isArabic: Boolean,
    showRetry: Boolean = false,
    onRetry: () -> Unit = {}
) {
    Box(
        modifier = Modifier.fillMaxWidth().fillMaxHeight(0.8f).padding(40.dp),
        contentAlignment = Alignment.Center
    ) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            Text(text = message, color = TextMuted, fontSize = 14.sp, textAlign = TextAlign.Center)
            if (showRetry) {
                Spacer(modifier = Modifier.height(16.dp))
                Button(
                    onClick = onRetry,
                    colors = ButtonDefaults.buttonColors(containerColor = Primary),
                    shape = RoundedCornerShape(8.dp)
                ) {
                    Text(
                        text = if (isArabic) "إعادة المحاولة" else "Retry",
                        color = Color.Black,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }
    }
}

@Composable
fun GridAdBanner(adUnitId: String) {
    BoxWithConstraints(
        modifier = Modifier
            .fillMaxWidth()
            .aspectRatio(1.1f)
            .clip(RoundedCornerShape(12.dp))
            .border(BorderStroke(1.dp, GlassBorder), RoundedCornerShape(12.dp))
            .background(BgSecondary)
    ) {
        val widthPx = maxWidth.value.toInt().coerceAtLeast(50)
        val heightPx = (maxWidth.value / 1.1f).toInt().coerceAtLeast(50)
        androidx.compose.ui.viewinterop.AndroidView(
            modifier = Modifier.fillMaxSize(),
            factory = { context ->
                com.google.android.gms.ads.AdView(context).apply {
                    setAdSize(com.google.android.gms.ads.AdSize(widthPx, heightPx))
                    this.adUnitId = adUnitId
                    loadAd(com.google.android.gms.ads.AdRequest.Builder().build())
                }
            }
        )
    }
}

@Composable
fun AdBanner(adUnitId: String, modifier: Modifier = Modifier) {
    androidx.compose.ui.viewinterop.AndroidView(
        modifier = modifier.fillMaxWidth(),
        factory = { context ->
            com.google.android.gms.ads.AdView(context).apply {
                setAdSize(com.google.android.gms.ads.AdSize.BANNER)
                this.adUnitId = adUnitId
                loadAd(com.google.android.gms.ads.AdRequest.Builder().build())
            }
        }
    )
}
