package com.sportiptv.app.ui.channels

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Home
import androidx.compose.material3.*
import androidx.compose.runtime.*
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
    val networksTitleText = if (isArabic) "الشبكات" else "Networks"
    val emptyMsg = if (isArabic) "لا توجد قنوات أو باقات متوفرة" else "No items available"
    val syncingMsg = if (isArabic) "جاري تحميل القنوات..." else "Loading channels..."

    val showChannels = selectedPackageId != null ||
        (selectedCategoryId != null && packages.isEmpty())

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BgPrimary)
    ) {
        if (selectedCategoryId == null) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(start = 20.dp, end = 20.dp, top = 20.dp, bottom = 8.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Text(
                    text = networksTitleText,
                    color = Color.White,
                    fontSize = 22.sp,
                    fontWeight = FontWeight.Bold
                )
                IconButton(onClick = onNavigateHome) {
                    Icon(Icons.Default.Home, contentDescription = "Home", tint = Primary)
                }
            }
        } else {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp, vertical = 8.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                val category = categories.find { it.id == selectedCategoryId }
                val catName = category?.let { if (isArabic && !it.nameAr.isNullOrEmpty()) it.nameAr else it.name } ?: ""
                val pkg = packages.find { it.id == selectedPackageId }
                val pkgName = pkg?.let { if (isArabic && !it.nameAr.isNullOrEmpty()) it.nameAr else it.name } ?: ""

                Button(
                    onClick = {
                        if (selectedPackageId != null) {
                            viewModel.selectPackage(null)
                        } else {
                            viewModel.selectCategory(null)
                        }
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0x22FFFFFF)),
                    contentPadding = PaddingValues(horizontal = 12.dp, vertical = 6.dp),
                    shape = RoundedCornerShape(8.dp)
                ) {
                    Icon(Icons.Default.ArrowBack, contentDescription = "Back", tint = Color.White, modifier = Modifier.size(16.dp))
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(text = networksTitleText, color = Color.White, fontSize = 12.sp)
                }

                Spacer(modifier = Modifier.width(16.dp))

                Text(
                    text = if (selectedPackageId != null) "$catName - $pkgName" else catName,
                    color = Primary,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.weight(1f),
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis
                )

                IconButton(onClick = onNavigateHome) {
                    Icon(Icons.Default.Home, contentDescription = "Home", tint = Primary, modifier = Modifier.size(22.dp))
                }
            }
        }

        when {
            syncState is Resource.Loading && categories.isEmpty() -> LoadingState(syncingMsg)
            showChannels -> {
                if (channels.isEmpty()) {
                    if (syncState is Resource.Loading) LoadingState(syncingMsg)
                    else EmptyState(emptyMsg, isArabic, showRetry = true) { viewModel.retrySync() }
                } else {
                    ChannelsGrid(channels, isLandscape, onChannelClick, viewModel)
                }
            }
            selectedCategoryId == null -> {
                if (categories.isEmpty()) {
                    EmptyState(emptyMsg, isArabic, syncState is Resource.Error) { viewModel.retrySync() }
                } else {
                    val gridEntries = remember(categories, adsEnabled) {
                        if (adsEnabled) buildGridWithAd(categories) { GridEntry.CategoryItem(it) }
                        else categories.map { GridEntry.CategoryItem(it) }
                    }
                    NetworkPackageGrid(
                        entries = gridEntries,
                        columnsCount = columnsCount,
                        isArabic = isArabic,
                        bannerAdUnitId = bannerAdUnitId,
                        onCategoryClick = { viewModel.selectCategory(it) },
                        onPackageClick = { viewModel.selectPackage(it) }
                    )
                }
            }
            packages.isEmpty() -> {
                EmptyState(emptyMsg, isArabic, showRetry = true) { viewModel.retrySync() }
            }
            else -> {
                val gridEntries = remember(packages, adsEnabled) {
                    if (adsEnabled) buildGridWithAd(packages) { GridEntry.PackageItem(it) }
                    else packages.map { GridEntry.PackageItem(it) }
                }
                NetworkPackageGrid(
                    entries = gridEntries,
                    columnsCount = columnsCount,
                    isArabic = isArabic,
                    bannerAdUnitId = bannerAdUnitId,
                    onCategoryClick = { viewModel.selectCategory(it) },
                    onPackageClick = { viewModel.selectPackage(it) }
                )
            }
        }

        if (adsEnabled) {
            Box(modifier = Modifier.fillMaxWidth().background(BgPrimary)) {
                AdBanner(adUnitId = bannerAdUnitId)
            }
        }
    }
}

@Composable
private fun ColumnScope.ChannelsGrid(
    channels: List<com.sportiptv.app.domain.model.Channel>,
    isLandscape: Boolean,
    onChannelClick: (Long) -> Unit,
    viewModel: ChannelsViewModel
) {
    LazyVerticalGrid(
        columns = GridCells.Fixed(if (isLandscape) 6 else 3),
        modifier = Modifier.fillMaxWidth().weight(1f),
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
private fun ColumnScope.NetworkPackageGrid(
    entries: List<GridEntry>,
    columnsCount: Int,
    isArabic: Boolean,
    bannerAdUnitId: String,
    onCategoryClick: (Long) -> Unit,
    onPackageClick: (Long) -> Unit
) {
    LazyVerticalGrid(
        columns = GridCells.Fixed(columnsCount),
        modifier = Modifier.fillMaxWidth().weight(1f),
        contentPadding = PaddingValues(start = 16.dp, top = 8.dp, end = 16.dp, bottom = 20.dp),
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        verticalArrangement = Arrangement.spacedBy(8.dp)
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
    val hash = id.hashCode()
    val color1 = Color(
        red = ((hash and 0xFF) / 255f * 0.35f) + 0.05f,
        green = (((hash shr 8) and 0xFF) / 255f * 0.35f) + 0.05f,
        blue = (((hash shr 16) and 0xFF) / 255f * 0.35f) + 0.05f,
        alpha = 1.0f
    )
    val color2 = Color(
        red = ((hash shr 4) and 0xFF) / 255f * 0.15f,
        green = ((hash shr 12) and 0xFF) / 255f * 0.15f,
        blue = ((hash shr 20) and 0xFF) / 255f * 0.15f,
        alpha = 1.0f
    )

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .aspectRatio(1.35f)
            .clip(RoundedCornerShape(12.dp))
            .border(BorderStroke(1.dp, GlassBorder), RoundedCornerShape(12.dp))
            .clickable { onClick() },
        colors = CardDefaults.cardColors(containerColor = Color.Transparent)
    ) {
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Brush.verticalGradient(listOf(color1, color2)))
                .padding(8.dp)
        ) {
            Column(
                modifier = Modifier.fillMaxSize(),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Box(
                    modifier = Modifier.weight(1f).fillMaxWidth(0.85f),
                    contentAlignment = Alignment.Center
                ) {
                    val resolvedUrl = ImageUrlResolver.resolve(logoUrl)
                    if (!resolvedUrl.isNullOrBlank()) {
                        AsyncImage(
                            model = resolvedUrl,
                            contentDescription = null,
                            modifier = Modifier.fillMaxSize().padding(4.dp),
                            contentScale = ContentScale.Fit
                        )
                    } else {
                        Text(name.take(2).uppercase(), color = Color.White, fontSize = 24.sp, fontWeight = FontWeight.Bold)
                    }
                }
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = name,
                    color = Color.White,
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Bold,
                    textAlign = TextAlign.Center,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis
                )
            }
        }
    }
}

@Composable
fun LoadingState(message: String) {
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
