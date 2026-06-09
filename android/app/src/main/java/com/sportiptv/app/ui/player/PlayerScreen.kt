package com.sportiptv.app.ui.player

import android.net.Uri
import android.view.ViewGroup
import android.widget.FrameLayout
import androidx.annotation.OptIn
import androidx.compose.animation.*
import androidx.compose.animation.core.*
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.gestures.detectDragGestures
import androidx.compose.foundation.gestures.detectTapGestures
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.AbsoluteAlignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.alpha
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.input.pointer.pointerInput
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalLifecycleOwner
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.LifecycleEventObserver
import androidx.media3.common.MediaItem
import androidx.media3.common.MimeTypes
import androidx.media3.common.Player
import androidx.media3.common.util.UnstableApi
import androidx.media3.exoplayer.ExoPlayer
import androidx.media3.ui.AspectRatioFrameLayout
import androidx.media3.ui.PlayerView
import coil3.compose.AsyncImage
import com.sportiptv.app.R
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.model.ChannelServer
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.ui.components.ErrorView
import com.sportiptv.app.ui.components.LoadingIndicator
import com.sportiptv.app.ui.theme.*
import androidx.compose.ui.focus.FocusRequester
import androidx.compose.ui.focus.focusRequester
import androidx.compose.foundation.focusable
import androidx.compose.ui.input.key.onKeyEvent
import androidx.compose.ui.input.key.KeyEventType
import androidx.compose.ui.input.key.type

data class PlayableStream(
    val id: String,
    val label: String,
    val url: String,
    val isServer: Boolean,
    val serverObject: ChannelServer? = null
)

// Helper functions for YouTube URLs
fun getYouTubeVideoId(url: String): String? {
    val regex = "^(?:https?:\\/\\/)?(?:www\\.)?(?:youtube\\.com\\/(?:[^\\/\\n\\s]+\\/\\S+\\/|(?:v|e(?:mbed)?)\\/|\\S*?[?&]v=)|youtu\\.be\\/|youtube\\.com\\/live\\/)([a-zA-Z0-9_-]{11})".toRegex()
    val matchResult = regex.find(url)
    return matchResult?.groupValues?.get(1)
}

fun isYouTubeUrl(url: String): Boolean {
    return getYouTubeVideoId(url) != null
}

enum class AspectRatioMode {
    FIT, STRETCH, CROP
}

@OptIn(UnstableApi::class)
@Composable
fun PlayerScreen(
    channelId: Long,
    directStreamUrl: String? = null,
    onBackClick: () -> Unit,
    viewModel: PlayerViewModel = hiltViewModel()
) {
    val channelState by viewModel.channelState.collectAsState()

    val context = LocalContext.current
    val activity = remember(context) { context as? android.app.Activity }

    DisposableEffect(Unit) {
        val originalOrientation = activity?.requestedOrientation ?: android.content.pm.ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED
        
        // Force Landscape Sensor mode
        activity?.requestedOrientation = android.content.pm.ActivityInfo.SCREEN_ORIENTATION_SENSOR_LANDSCAPE
        
        // Hide status and navigation bars for true fullscreen
        val window = activity?.window
        if (window != null) {
            androidx.core.view.WindowCompat.setDecorFitsSystemWindows(window, false)
            val controller = androidx.core.view.WindowCompat.getInsetsController(window, window.decorView)
            controller.hide(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            controller.systemBarsBehavior = androidx.core.view.WindowInsetsControllerCompat.BEHAVIOR_SHOW_TRANSIENT_BARS_BY_SWIPE
        }
        
        onDispose {
            // Restore original orientation and system UI
            activity?.requestedOrientation = originalOrientation
            if (window != null) {
                androidx.core.view.WindowCompat.setDecorFitsSystemWindows(window, true)
                val controller = androidx.core.view.WindowCompat.getInsetsController(window, window.decorView)
                controller.show(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            }
        }
    }

    // If direct stream URL is provided (e.g. World Cup Match), create a mock channel
    LaunchedEffect(key1 = channelId, key2 = directStreamUrl) {
        if (!directStreamUrl.isNullOrBlank()) {
            val mockChannel = Channel(
                id = 0L,
                name = "Direct Match Stream",
                nameAr = "بث مباشر للمباراة",
                categoryId = 0L,
                categoryName = "World Cup",
                categoryNameAr = "كأس العالم",
                logoUrl = "https://upload.wikimedia.org/wikipedia/en/thumb/e/e3/2026_FIFA_World_Cup.svg/200px-2026_FIFA_World_Cup.svg.png",
                streamUrl = directStreamUrl,
                streamType = "M3U8",
                quality = "FHD",
                backupUrl = null,
                sortOrder = 9,
                isActive = true
            )
            viewModel.setMockChannel(mockChannel)
        } else {
            viewModel.loadChannel(channelId)
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.Black)
    ) {
        when (val state = channelState) {
            is Resource.Loading -> {
                LoadingIndicator(message = "Acquiring secure stream parameters…")
            }
            is Resource.Success -> {
                VideoPlayer(
                    channel = state.data,
                    onBackClick = onBackClick,
                    viewModel = viewModel
                )
            }
            is Resource.Error -> {
                ErrorView(
                    message = state.message,
                    onRetry = {
                        if (!directStreamUrl.isNullOrBlank()) {
                            // Mock channel reload
                        } else {
                            viewModel.loadChannel(channelId)
                        }
                    }
                )
            }
            else -> {}
        }
    }
}

@Composable
fun StreamCircleButton(
    label: String,
    isSelected: Boolean,
    onClick: () -> Unit
) {
    Box(
        modifier = Modifier
            .defaultMinSize(minWidth = 40.dp, minHeight = 40.dp)
            .height(40.dp)
            .clip(RoundedCornerShape(50))
            .background(if (isSelected) Color(0xFFF0B429) else Color(0x33FFFFFF))
            .clickable { onClick() }
            .border(
                border = if (isSelected) BorderStroke(1.5.dp, Color(0xFFD4941A)) else BorderStroke(1.dp, Color.White.copy(alpha = 0.15f)),
                shape = RoundedCornerShape(50)
            )
            .padding(horizontal = 8.dp),
        contentAlignment = Alignment.Center
    ) {
        Text(
            text = label,
            color = if (isSelected) Color(0xFF06080F) else Color.White,
            fontSize = 11.sp,
            fontWeight = FontWeight.Bold,
            maxLines = 1,
            textAlign = TextAlign.Center
        )
    }
}

@OptIn(UnstableApi::class)
@Composable
fun VideoPlayer(
    channel: Channel,
    onBackClick: () -> Unit,
    viewModel: PlayerViewModel
) {
    val context = LocalContext.current
    val lifecycleOwner = LocalLifecycleOwner.current
    val isArabic = remember { java.util.Locale.getDefault().language == "ar" }

    val defaultHeaders = remember(channel.id) {
        val headers = mutableMapOf<String, String>()
        if (!channel.drmHeaders.isNullOrBlank()) {
            try {
                val json = org.json.JSONObject(channel.drmHeaders)
                val keys = json.keys()
                while (keys.hasNext()) {
                    val key = keys.next()
                    headers[key] = json.getString(key)
                }
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
        if (!headers.containsKey("User-Agent")) {
            headers["User-Agent"] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36"
        }
        if (!headers.containsKey("Referer")) {
            headers["Referer"] = "https://x.com"
        }
        headers["ngrok-skip-browser-warning"] = "true"
        headers["Bypass-Tunnel-Reminder"] = "true"
        headers
    }

    val okhttpClient = remember(channel.id) {
        okhttp3.OkHttpClient.Builder()
            .followRedirects(true)
            .followSslRedirects(true)
            .addInterceptor { chain ->
                val originalRequest = chain.request()
                val builder = originalRequest.newBuilder()
                val userAgent = defaultHeaders["User-Agent"] ?: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36"
                builder.header("User-Agent", userAgent)
                defaultHeaders.forEach { (key, value) ->
                    if (key != "User-Agent") {
                        builder.header(key, value)
                    }
                }
                chain.proceed(builder.build())
            }
            .build()
    }

    val dataSourceFactory = remember(channel.id) {
        androidx.media3.datasource.okhttp.OkHttpDataSource.Factory(okhttpClient)
    }

    // Initialize ExoPlayer with custom network request headers and optimized rendering pipeline
    val exoPlayer = remember(channel.id) {
        val mediaSourceFactory = androidx.media3.exoplayer.source.DefaultMediaSourceFactory(context)
            .setDataSourceFactory(dataSourceFactory)

        val renderersFactory = androidx.media3.exoplayer.DefaultRenderersFactory(context)
            .forceEnableMediaCodecAsynchronousQueueing()
            .setEnableDecoderFallback(true)

        ExoPlayer.Builder(context, renderersFactory)
            .setMediaSourceFactory(mediaSourceFactory)
            .build()
            .apply {
                playWhenReady = true
                // Default to 720p HD limit to ensure smooth playback across all devices
                trackSelectionParameters = trackSelectionParameters.buildUpon()
                    .setMaxVideoSize(1280, 720)
                    .build()
            }
    }

    var isPlaying by remember { mutableStateOf(false) }
    var playbackError by remember { mutableStateOf<String?>(null) }
    var isLoading by remember { mutableStateOf(true) }

    val configState by viewModel.appConfigState.collectAsState()

    val adsEnabled = remember(configState) {
        val configData = (configState as? Resource.Success)?.data
        configData?.ads_enabled ?: true
    }

    val admobInterstitialAdUnitId = remember(configState) {
        val configData = (configState as? Resource.Success)?.data
        configData?.admob_interstitial_ad_unit_id ?: com.sportiptv.app.util.Constants.ADMOB_INTERSTITIAL_AD_UNIT_ID
    }

    val isPremium = remember(channel.id) { viewModel.isPremiumUser() }
    var isAdPlaying by remember(channel.id, adsEnabled) { mutableStateOf(!isPremium && adsEnabled) }
    var isAdLoading by remember(channel.id, adsEnabled) { mutableStateOf(!isPremium && adsEnabled) }

    val activity = remember(context) { context as? android.app.Activity }

    LaunchedEffect(channel.id, adsEnabled, admobInterstitialAdUnitId) {
        if (!isPremium && adsEnabled) {
            isAdLoading = true
            android.util.Log.d("AdMob", "Loading interstitial ad...")
            
            val adRequest = com.google.android.gms.ads.AdRequest.Builder().build()
            com.google.android.gms.ads.interstitial.InterstitialAd.load(
                context,
                admobInterstitialAdUnitId,
                adRequest,
                object : com.google.android.gms.ads.interstitial.InterstitialAdLoadCallback() {
                    override fun onAdLoaded(interstitialAd: com.google.android.gms.ads.interstitial.InterstitialAd) {
                        android.util.Log.d("AdMob", "Ad loaded successfully, showing it.")
                        isAdLoading = false
                        
                        interstitialAd.fullScreenContentCallback = object : com.google.android.gms.ads.FullScreenContentCallback() {
                            override fun onAdDismissedFullScreenContent() {
                                android.util.Log.d("AdMob", "Ad dismissed, starting playback.")
                                isAdPlaying = false
                            }

                            override fun onAdFailedToShowFullScreenContent(adError: com.google.android.gms.ads.AdError) {
                                android.util.Log.e("AdMob", "Ad failed to show: ${adError.message}")
                                isAdPlaying = false
                            }
                        }
                        
                        activity?.let {
                            interstitialAd.show(it)
                        } ?: run {
                            isAdPlaying = false
                        }
                    }

                    override fun onAdFailedToLoad(loadAdError: com.google.android.gms.ads.LoadAdError) {
                        android.util.Log.e("AdMob", "Ad failed to load: ${loadAdError.message}")
                        isAdLoading = false
                        isAdPlaying = false
                    }
                }
            )
        } else {
            isAdLoading = false
            isAdPlaying = false
        }
    }

    val infiniteTransition = rememberInfiniteTransition(label = "player_pulsing")
    val liveDotAlpha by infiniteTransition.animateFloat(
        initialValue = 0.3f,
        targetValue = 1f,
        animationSpec = infiniteRepeatable(
            animation = tween(1000, easing = LinearEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "alpha"
    )

    val focusRequester = remember { FocusRequester() }

    // List of available stream options for circular buttons
    val availableStreams = remember(channel) {
        val list = mutableListOf<PlayableStream>()
        // 1. Primary
        list.add(
            PlayableStream(
                id = "primary",
                label = channel.quality ?: "FHD",
                url = channel.streamUrl,
                isServer = false
            )
        )
        // 2. Backup
        if (!channel.backupUrl.isNullOrBlank()) {
            list.add(
                PlayableStream(
                    id = "backup",
                    label = if (isArabic) "الاحتياطي" else "Backup",
                    url = channel.backupUrl,
                    isServer = false
                )
            )
        }
        // 3. Servers
        channel.servers.forEach { server ->
            list.add(
                PlayableStream(
                    id = "server_${server.id}",
                    label = server.name,
                    url = server.streamUrl,
                    isServer = true,
                    serverObject = server
                )
            )
        }
        list
    }

    var activeStream by remember { mutableStateOf<PlayableStream?>(null) }

    var videoWidth by remember { mutableStateOf(1280) }
    var videoHeight by remember { mutableStateOf(720) }

    // Aspect ratio resize mode
    var aspectRatioMode by remember { mutableStateOf(AspectRatioMode.FIT) }

    // Controls Overlays State
    var showControls by remember { mutableStateOf(true) }
    var isLocked by remember { mutableStateOf(false) }
    var showUnlockButton by remember { mutableStateOf(false) }

    val configuration = androidx.compose.ui.platform.LocalConfiguration.current
    val isLandscape = configuration.orientation == android.content.res.Configuration.ORIENTATION_LANDSCAPE
    var drawerOpen by remember { mutableStateOf(false) }

    var watermarkAlignment by remember { mutableStateOf(Alignment.BottomStart) }
    var isWatermarkVisible by remember { mutableStateOf(false) }

    LaunchedEffect(Unit) {
        val alignments = listOf(
            Alignment.TopStart, Alignment.TopCenter, Alignment.TopEnd,
            Alignment.CenterStart, Alignment.Center, Alignment.CenterEnd,
            Alignment.BottomStart, Alignment.BottomCenter, Alignment.BottomEnd
        )
        while (true) {
            watermarkAlignment = alignments.random()
            isWatermarkVisible = true
            kotlinx.coroutines.delay(8000) // Show for 8 seconds
            isWatermarkVisible = false
            kotlinx.coroutines.delay(52000) // Hide for 52 seconds
        }
    }

    // Drawer dynamic flows
    val categories by viewModel.drawerCategories.collectAsState()
    val channels by viewModel.drawerChannels.collectAsState()
    val selectedCategoryId by viewModel.selectedCategoryId.collectAsState()
    val searchCategoryQuery by viewModel.searchCategoryQuery.collectAsState()
    val searchChannelQuery by viewModel.searchChannelQuery.collectAsState()

    // Gestures states
    val audioManager = remember { context.getSystemService(android.content.Context.AUDIO_SERVICE) as android.media.AudioManager }
    val maxVolume = remember { audioManager.getStreamMaxVolume(android.media.AudioManager.STREAM_MUSIC) }
    var currentVolume by remember { mutableStateOf(audioManager.getStreamVolume(android.media.AudioManager.STREAM_MUSIC).toFloat()) }
    
    var currentBrightness by remember {
        mutableStateOf(
            activity?.window?.attributes?.screenBrightness?.let { if (it < 0) 0.5f else it } ?: 0.5f
        )
    }

    // Active drag tracking
    var activeDragBrightness by remember { mutableStateOf<Float?>(null) }
    var activeDragVolume by remember { mutableStateOf<Float?>(null) }

    // Auto-hide controls timer
    LaunchedEffect(showControls, drawerOpen, activeDragBrightness, activeDragVolume, isLocked) {
        if (showControls && !drawerOpen && activeDragBrightness == null && activeDragVolume == null && !isLocked) {
            kotlinx.coroutines.delay(5000)
            showControls = false
        }
    }

    LaunchedEffect(channel) {
        activeStream = availableStreams.firstOrNull()
    }

    LaunchedEffect(drawerOpen) {
        if (!drawerOpen) {
            focusRequester.requestFocus()
        }
    }

    // Set player listener
    DisposableEffect(exoPlayer) {
        val listener = object : Player.Listener {
            override fun onIsPlayingChanged(isPlayingChanged: Boolean) {
                isPlaying = isPlayingChanged
            }

            override fun onPlaybackStateChanged(state: Int) {
                isLoading = state == Player.STATE_BUFFERING
                if (state == Player.STATE_READY) {
                    playbackError = null
                }
                if (state == Player.STATE_ENDED && isAdPlaying) {
                    isAdPlaying = false
                }
            }

            override fun onVideoSizeChanged(videoSize: androidx.media3.common.VideoSize) {
                if (videoSize.width > 0 && videoSize.height > 0) {
                    videoWidth = videoSize.width
                    videoHeight = videoSize.height
                }
            }

            override fun onPlayerError(error: androidx.media3.common.PlaybackException) {
                if (isAdPlaying) {
                    isAdPlaying = false
                    return
                }
                var isAuthError = false
                var cause = error.cause
                while (cause != null) {
                    if (cause is androidx.media3.datasource.HttpDataSource.InvalidResponseCodeException) {
                        if (cause.responseCode == 401 || cause.responseCode == 403) {
                            isAuthError = true
                            break
                        }
                    }
                    cause = cause.cause
                }

                if (isAuthError) {
                    playbackError = "Token expired. Requesting new secure token..."
                    isLoading = true
                    android.util.Log.e("VideoPlayer", "Stream token expired (403/401). Reloading channel parameters...", error)
                    
                    if (channel.id != 0L) {
                        // This will trigger Resource.Loading, destroying this player and creating a new one with fresh token
                        viewModel.loadChannel(channel.id)
                    }
                } else {
                    playbackError = "Stream failed: ${error.localizedMessage ?: "Connection reset"}. Retrying…"
                    isLoading = true
                    android.util.Log.e("VideoPlayer", "ExoPlayer error occurred: ${error.message}", error)
                    
                    if (channel.id != 0L) {
                        viewModel.loadChannel(channel.id)
                    }
                }
            }
        }
        exoPlayer.addListener(listener)
        onDispose {
            exoPlayer.removeListener(listener)
            exoPlayer.release()
        }
    }

    // Set stream source when active stream changes or ad status changes
    LaunchedEffect(activeStream, channel.streamUrl, channel.backupUrl, isAdPlaying) {
        if (isAdPlaying) {
            // Wait until the ad finishes playing before loading the stream
            return@LaunchedEffect
        }
        val rawUrl = activeStream?.url ?: channel.streamUrl
        if (rawUrl.isEmpty() || isYouTubeUrl(rawUrl)) return@LaunchedEffect
        
        // Clean any syntax errors in the URL (e.g. replace "&?" with "&")
        val url = rawUrl.replace("&?", "&")
        val streamUri = Uri.parse(url)
        val mimeType = when {
            url.contains(".m3u8") -> MimeTypes.APPLICATION_M3U8
            url.contains(".mpd") -> MimeTypes.APPLICATION_MPD
            url.contains(".ts") -> MimeTypes.VIDEO_MP2T
            url.endsWith(".mp4") || url.contains(".mp4") -> MimeTypes.VIDEO_MP4
            else -> MimeTypes.APPLICATION_M3U8
        }

        val drmLicenseUrl = channel.drmLicenseUrl
        val isClearKey = !drmLicenseUrl.isNullOrBlank() && (
            drmLicenseUrl.startsWith("clearkey:", ignoreCase = true) ||
            (!drmLicenseUrl.startsWith("http://", ignoreCase = true) && !drmLicenseUrl.startsWith("https://", ignoreCase = true) && drmLicenseUrl.contains(":"))
        )

        val mediaItemBuilder = MediaItem.Builder()
            .setUri(streamUri)
            .setMimeType(mimeType)

        if (!isClearKey && !drmLicenseUrl.isNullOrBlank()) {
            val drmConfigurationBuilder = MediaItem.DrmConfiguration.Builder(androidx.media3.common.C.WIDEVINE_UUID)
                .setLicenseUri(Uri.parse(drmLicenseUrl))
                
            // Parse custom DRM Headers if configured
            val headersMap = mutableMapOf<String, String>()
            if (!channel.drmHeaders.isNullOrBlank()) {
                try {
                    val json = org.json.JSONObject(channel.drmHeaders)
                    val keys = json.keys()
                    while (keys.hasNext()) {
                        val key = keys.next()
                        headersMap[key] = json.getString(key)
                    }
                } catch (e: Exception) {
                    e.printStackTrace()
                }
            }
            // Always set a default User-Agent and Referer if none is provided to avoid authentication rejection
            if (!headersMap.containsKey("User-Agent")) {
                headersMap["User-Agent"] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36"
            }
            if (!headersMap.containsKey("Referer")) {
                headersMap["Referer"] = "https://x.com"
            }
            
            drmConfigurationBuilder.setLicenseRequestHeaders(headersMap)
            mediaItemBuilder.setDrmConfiguration(drmConfigurationBuilder.build())
        }

        val mediaItem = mediaItemBuilder.build()

        val drmSessionManager = if (isClearKey) {
            try {
                val cleanUrl = if (drmLicenseUrl!!.startsWith("clearkey:", ignoreCase = true)) {
                    drmLicenseUrl.substring("clearkey:".length)
                } else {
                    drmLicenseUrl
                }
                val parts = cleanUrl.split(":")
                if (parts.size >= 2) {
                    val kidHex = parts[0].trim()
                    val keyHex = parts[1].trim()
                    
                    val hexToBase64Url = { hex: String ->
                        val cleanHex = hex.replace("[^a-fA-F0-9]".toRegex(), "")
                        val bytes = ByteArray(cleanHex.length / 2)
                        for (i in bytes.indices) {
                            bytes[i] = cleanHex.substring(i * 2, i * 2 + 2).toInt(16).toByte()
                        }
                        android.util.Base64.encodeToString(
                            bytes,
                            android.util.Base64.URL_SAFE or android.util.Base64.NO_WRAP or android.util.Base64.NO_PADDING
                        )
                    }
                    
                    val kidBase64 = hexToBase64Url(kidHex)
                    val keyBase64 = hexToBase64Url(keyHex)
                    
                    val jwkJson = """
                        {
                          "keys": [
                            {
                              "kty": "oct",
                              "k": "$keyBase64",
                              "kid": "$kidBase64"
                            }
                          ]
                        }
                    """.trimIndent()
                    
                    val mediaDrmCallback = androidx.media3.exoplayer.drm.LocalMediaDrmCallback(jwkJson.toByteArray(Charsets.UTF_8))
                    androidx.media3.exoplayer.drm.DefaultDrmSessionManager.Builder()
                        .setUuidAndExoMediaDrmProvider(
                            androidx.media3.common.C.CLEARKEY_UUID,
                            androidx.media3.exoplayer.drm.FrameworkMediaDrm.DEFAULT_PROVIDER
                        )
                        .build(mediaDrmCallback)
                } else null
            } catch (e: Exception) {
                e.printStackTrace()
                null
            }
        } else null

        val mediaSource = when (mimeType) {
            MimeTypes.APPLICATION_MPD -> {
                val factory = androidx.media3.exoplayer.dash.DashMediaSource.Factory(dataSourceFactory)
                if (drmSessionManager != null) {
                    factory.setDrmSessionManagerProvider { drmSessionManager }
                }
                factory.createMediaSource(mediaItem)
            }
            MimeTypes.APPLICATION_M3U8 -> {
                val factory = androidx.media3.exoplayer.hls.HlsMediaSource.Factory(dataSourceFactory)
                if (drmSessionManager != null) {
                    factory.setDrmSessionManagerProvider { drmSessionManager }
                }
                factory.createMediaSource(mediaItem)
            }
            else -> {
                val factory = androidx.media3.exoplayer.source.ProgressiveMediaSource.Factory(dataSourceFactory)
                if (drmSessionManager != null) {
                    factory.setDrmSessionManagerProvider { drmSessionManager }
                }
                factory.createMediaSource(mediaItem)
            }
        }

        // Set dynamic resolution cap based on active server or channel quality settings
        val activeQuality = activeStream?.let { stream ->
            if (stream.isServer) {
                stream.serverObject?.quality
            } else if (stream.id == "primary") {
                channel.quality
            } else {
                "SD"
            }
        } ?: channel.quality
        val maxVideoSize = when (activeQuality.uppercase()) {
            "SD" -> Pair(854, 480)
            "HD" -> Pair(1280, 720)
            "FHD" -> Pair(1920, 1080)
            "4K" -> Pair(3840, 2160)
            else -> Pair(1280, 720)
        }
        exoPlayer.trackSelectionParameters = exoPlayer.trackSelectionParameters.buildUpon()
            .setMaxVideoSize(maxVideoSize.first, maxVideoSize.second)
            .build()

        exoPlayer.setMediaSource(mediaSource)
        exoPlayer.prepare()
        exoPlayer.play()
    }

    // Manage Activity Lifecycle events
    DisposableEffect(lifecycleOwner) {
        val observer = LifecycleEventObserver { _, event ->
            when (event) {
                Lifecycle.Event.ON_PAUSE -> exoPlayer.pause()
                Lifecycle.Event.ON_RESUME -> exoPlayer.play()
                else -> {}
            }
        }
        lifecycleOwner.lifecycle.addObserver(observer)
        onDispose {
            lifecycleOwner.lifecycle.removeObserver(observer)
        }
    }

    val currentUrl = activeStream?.url ?: channel.streamUrl
    val youtubeVideoId = getYouTubeVideoId(currentUrl)
    val isYouTube = youtubeVideoId != null

    if (isLandscape && drawerOpen && !isLocked) {
        Row(
            modifier = Modifier
                .fillMaxSize()
                .background(Color(0xFF07040D))
        ) {
            // ── 1. Far-left Vertical Sidebar (60dp) ──
            Column(
                modifier = Modifier
                    .width(60.dp)
                    .fillMaxHeight()
                    .background(Color(0xFF07040D))
                    .padding(vertical = 12.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                Image(
                    painter = painterResource(id = R.drawable.zinou_tv_logo_thin),
                    contentDescription = "ZINOU TV Logo",
                    modifier = Modifier
                        .size(45.dp, 25.dp)
                        .padding(bottom = 4.dp),
                    contentScale = ContentScale.Fit
                )

                val navIcons = listOf(
                    Pair(Icons.Default.Search, "Search"),
                    Pair(Icons.Default.Home, "Home"),
                    Pair(Icons.Default.LiveTv, "Live"),
                    Pair(Icons.Default.Movie, "Movies"),
                    Pair(Icons.Default.Favorite, "Favorites"),
                    Pair(Icons.Default.Settings, "Settings")
                )

                navIcons.forEach { item ->
                    val isSelected = item.second == "Live"
                    Box(
                        modifier = Modifier
                            .size(38.dp)
                            .clip(RoundedCornerShape(8.dp))
                            .background(if (isSelected) Color(0x33E5A93C) else Color.Transparent)
                            .clickable {
                                if (item.second == "Home") {
                                    onBackClick()
                                }
                            },
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = item.first,
                            contentDescription = item.second,
                            tint = if (isSelected) Primary else Color.LightGray.copy(alpha = 0.6f),
                            modifier = Modifier.size(18.dp)
                        )
                    }
                }
            }

            // Vertical divider
            Box(modifier = Modifier.fillMaxHeight().width(1.dp).background(Color.White.copy(alpha = 0.08f)))

            // ── 2. Category & Channels Panels (420dp) ──
            Row(
                modifier = Modifier
                    .width(420.dp)
                    .fillMaxHeight()
                    .background(Color(0xFF0F0B18))
            ) {
                // Category List Panel (42% width)
                Column(
                    modifier = Modifier
                        .weight(0.42f)
                        .fillMaxHeight()
                        .background(Color(0xFF0A041A))
                        .padding(10.dp)
                ) {
                    Text(
                        text = if (isArabic) "التصنيفات" else "Live",
                        color = Color.White,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(bottom = 6.dp)
                    )

                    OutlinedTextField(
                        value = searchCategoryQuery,
                        onValueChange = { viewModel.setSearchCategory(it) },
                        placeholder = { Text(if (isArabic) "بحث تصنيف..." else "Search", fontSize = 10.sp, color = TextMuted) },
                        singleLine = true,
                        textStyle = androidx.compose.ui.text.TextStyle(color = Color.White, fontSize = 11.sp),
                        colors = OutlinedTextFieldDefaults.colors(
                            unfocusedContainerColor = Color(0x1AFFFFFF),
                            focusedContainerColor = Color(0x1AFFFFFF),
                            unfocusedBorderColor = Color.Transparent,
                            focusedBorderColor = Primary
                        ),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(38.dp)
                            .padding(bottom = 6.dp),
                        shape = RoundedCornerShape(6.dp)
                    )

                    LazyColumn(
                        verticalArrangement = Arrangement.spacedBy(4.dp),
                        modifier = Modifier.weight(1f)
                    ) {
                        item {
                            CategoryListItem(
                                name = if (isArabic) "الكل" else "All",
                                isSelected = selectedCategoryId == null,
                                count = viewModel.allChannels.value.size,
                                onClick = { viewModel.selectDrawerCategory(null) }
                            )
                        }
                        items(categories) { cat ->
                            val categoryName = if (isArabic && !cat.nameAr.isNullOrEmpty()) cat.nameAr else cat.name
                            val count = remember(viewModel.allChannels.value, cat.id) {
                                viewModel.allChannels.value.count { it.categoryId == cat.id }
                            }
                            CategoryListItem(
                                name = categoryName,
                                isSelected = selectedCategoryId == cat.id,
                                count = count,
                                onClick = { viewModel.selectDrawerCategory(cat.id) }
                            )
                        }
                    }
                }

                // Vertical Divider between panels
                Box(modifier = Modifier.fillMaxHeight().width(1.dp).background(Color.White.copy(alpha = 0.08f)))

                // Channels List Panel (58% width)
                Column(
                    modifier = Modifier
                        .weight(0.58f)
                        .fillMaxHeight()
                        .background(Color(0xFF0F0B18))
                        .padding(10.dp)
                ) {
                    Text(
                        text = if (isArabic) "القنوات" else "Channels",
                        color = Color.White,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(bottom = 6.dp)
                    )

                    OutlinedTextField(
                        value = searchChannelQuery,
                        onValueChange = { viewModel.setSearchChannel(it) },
                        placeholder = { Text(if (isArabic) "بحث القنوات..." else "Search Channels", fontSize = 10.sp, color = TextMuted) },
                        singleLine = true,
                        textStyle = androidx.compose.ui.text.TextStyle(color = Color.White, fontSize = 11.sp),
                        colors = OutlinedTextFieldDefaults.colors(
                            unfocusedContainerColor = Color(0x1AFFFFFF),
                            focusedContainerColor = Color(0x1AFFFFFF),
                            unfocusedBorderColor = Color.Transparent,
                            focusedBorderColor = Primary
                        ),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(38.dp)
                            .padding(bottom = 6.dp),
                        shape = RoundedCornerShape(6.dp)
                    )

                    LazyColumn(
                        verticalArrangement = Arrangement.spacedBy(4.dp),
                        modifier = Modifier.weight(1f)
                    ) {
                        items(channels) { chan ->
                            val isCurrentPlaying = chan.id == channel.id
                            ChannelListItem(
                                channel = chan,
                                isPlaying = isCurrentPlaying,
                                onClick = {
                                    viewModel.loadChannel(chan.id)
                                }
                            )
                        }
                    }
                }
            }

            // Divider
            Box(modifier = Modifier.fillMaxHeight().width(1.dp).background(Color.White.copy(alpha = 0.08f)))

            // ── 3. Embedded Video Player & Info Panel (Remaining width) ──
            Column(
                modifier = Modifier
                    .weight(1f)
                    .fillMaxHeight()
                    .background(Color.Black)
            ) {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1.3f)
                        .background(Color.Black)
                ) {
                    if (isYouTube) {
                        AndroidView(
                            factory = { ctx ->
                                android.webkit.WebView(ctx).apply {
                                    settings.javaScriptEnabled = true
                                    settings.mediaPlaybackRequiresUserGesture = false
                                    settings.useWideViewPort = true
                                    settings.loadWithOverviewMode = true
                                    settings.domStorageEnabled = true
                                    
                                    val defaultUA = settings.userAgentString
                                    val modifiedUA = defaultUA
                                        .replace("; wv", "")
                                        .replace("wv", "")
                                        .replace("Version/4.0 ", "")
                                        .replace("Version/4.0", "")
                                    settings.userAgentString = modifiedUA
                                    
                                    webChromeClient = android.webkit.WebChromeClient()
                                    webViewClient = android.webkit.WebViewClient()
                                    
                                    layoutParams = FrameLayout.LayoutParams(
                                        ViewGroup.LayoutParams.MATCH_PARENT,
                                        ViewGroup.LayoutParams.MATCH_PARENT
                                    )
                                    
                                    val html = """
                                        <!DOCTYPE html>
                                        <html>
                                        <head>
                                            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
                                            <style>
                                                html, body { margin: 0; padding: 0; width: 100%; height: 100%; background-color: #000; overflow: hidden; }
                                                iframe { width: 100%; height: 100%; border: 0; }
                                            </style>
                                        </head>
                                        <body>
                                            <iframe 
                                                src="https://www.youtube.com/embed/$youtubeVideoId?autoplay=1&modestbranding=1&rel=0&showinfo=0&fs=1&mute=0&playsinline=1" 
                                                referrerpolicy="strict-origin-when-cross-origin"
                                                allow="autoplay; encrypted-media; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                        </body>
                                        </html>
                                    """.trimIndent()
                                    
                                    loadDataWithBaseURL("https://www.youtube.com", html, "text/html", "utf-8", null)
                                }
                            },
                            modifier = Modifier.fillMaxSize()
                        )
                    } else {
                        AndroidView(
                            factory = { ctx ->
                                PlayerView(ctx).apply {
                                    player = exoPlayer
                                    useController = false
                                    resizeMode = AspectRatioFrameLayout.RESIZE_MODE_FIT
                                    layoutParams = FrameLayout.LayoutParams(
                                        ViewGroup.LayoutParams.MATCH_PARENT,
                                        ViewGroup.LayoutParams.MATCH_PARENT
                                    )
                                }
                            },
                            update = { playerView ->
                                playerView.resizeMode = AspectRatioFrameLayout.RESIZE_MODE_FIT
                            },
                            modifier = Modifier.fillMaxSize()
                        )
                    }

                    // Compose-based glassmorphic WatermarkLogo overlay aligned to top-right of video
                    WatermarkLogo(
                        modifier = Modifier
                            .align(AbsoluteAlignment.TopRight)
                            .padding(top = 12.dp, end = 12.dp)
                    )

                    // Chevron overlay button on left edge to go full-screen or toggle drawer
                    IconButton(
                        onClick = { drawerOpen = false },
                        modifier = Modifier
                            .align(Alignment.CenterStart)
                            .padding(start = 12.dp)
                            .size(36.dp)
                            .background(Color(0x99000000), shape = RoundedCornerShape(50))
                    ) {
                        Icon(Icons.Default.ChevronLeft, "Collapse Sidebar", tint = Color.White)
                    }

                    // Fullscreen floating button at bottom right corner
                    IconButton(
                        onClick = { drawerOpen = false },
                        modifier = Modifier
                            .align(Alignment.BottomEnd)
                            .padding(12.dp)
                            .size(36.dp)
                            .background(Color(0x99000000), shape = RoundedCornerShape(50))
                    ) {
                        Icon(Icons.Default.Fullscreen, "Fullscreen", tint = Color.White)
                    }
                }

                // Info Panel below video player (Split Screen Metadata)
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(0.7f)
                        .background(Color(0xFF0F0B18))
                        .padding(16.dp)
                ) {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(
                            text = if (isArabic && !channel.nameAr.isNullOrEmpty()) channel.nameAr else channel.name,
                            color = Color.White,
                            fontSize = 18.sp,
                            fontWeight = FontWeight.Bold,
                            modifier = Modifier.weight(1f)
                        )
                        IconButton(onClick = { viewModel.toggleFavorite(channel.id, !channel.isFavorited) }) {
                            Icon(
                                imageVector = if (channel.isFavorited) Icons.Default.Favorite else Icons.Default.FavoriteBorder,
                                contentDescription = "Favorite",
                                tint = if (channel.isFavorited) Color.Red else Color.White
                            )
                        }
                    }
                    
                    Spacer(modifier = Modifier.height(4.dp))
                    
                    Text(
                        text = if (isArabic) "لا توجد معلومات للبرنامج حالياً" else "No EPG information available.",
                        color = Color.Gray,
                        fontSize = 12.sp
                    )

                    Spacer(modifier = Modifier.weight(1f))

                    // Buttons like Epg and Add to Favorites
                    Row(
                        horizontalArrangement = Arrangement.spacedBy(12.dp),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Button(
                            onClick = {
                                android.widget.Toast.makeText(context, if (isArabic) "دليل البرامج غير متوفر حالياً" else "EPG data is not available", android.widget.Toast.LENGTH_SHORT).show()
                            },
                            colors = ButtonDefaults.buttonColors(containerColor = Color(0x1DFFFFFF)),
                            shape = RoundedCornerShape(8.dp),
                            contentPadding = PaddingValues(horizontal = 14.dp, vertical = 8.dp)
                        ) {
                            Icon(Icons.Default.List, contentDescription = null, tint = Color.White, modifier = Modifier.size(16.dp))
                            Spacer(modifier = Modifier.width(6.dp))
                            Text("Epg", color = Color.White, fontSize = 12.sp)
                        }

                        Button(
                            onClick = { viewModel.toggleFavorite(channel.id, !channel.isFavorited) },
                            colors = ButtonDefaults.buttonColors(containerColor = if (channel.isFavorited) Color.Red.copy(alpha = 0.2f) else Color(0x1DFFFFFF)),
                            shape = RoundedCornerShape(8.dp),
                            border = if (channel.isFavorited) BorderStroke(1.dp, Color.Red) else null,
                            contentPadding = PaddingValues(horizontal = 14.dp, vertical = 8.dp)
                        ) {
                            Icon(
                                imageVector = if (channel.isFavorited) Icons.Default.Favorite else Icons.Default.FavoriteBorder,
                                contentDescription = null,
                                tint = if (channel.isFavorited) Color.Red else Color.White,
                                modifier = Modifier.size(16.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = if (channel.isFavorited) (if (isArabic) "مفضلة" else "Favorited") else (if (isArabic) "إضافة للمفضلة" else "Add to Favorite"),
                                color = Color.White,
                                fontSize = 12.sp
                            )
                        }
                    }
                }
            }
        }
    } else {
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Color.Black)
                .focusRequester(focusRequester)
                .focusable()
                .onKeyEvent { keyEvent ->
                    if (keyEvent.type == KeyEventType.KeyDown) {
                        val nativeKeyCode = keyEvent.nativeKeyEvent.keyCode
                        if (!drawerOpen && !isLocked) {
                            when (nativeKeyCode) {
                                android.view.KeyEvent.KEYCODE_DPAD_UP,
                                android.view.KeyEvent.KEYCODE_CHANNEL_UP,
                                android.view.KeyEvent.KEYCODE_PAGE_UP -> {
                                    viewModel.playPreviousChannel()
                                    true
                                }
                                android.view.KeyEvent.KEYCODE_DPAD_DOWN,
                                android.view.KeyEvent.KEYCODE_CHANNEL_DOWN,
                                android.view.KeyEvent.KEYCODE_PAGE_DOWN -> {
                                    viewModel.playNextChannel()
                                    true
                                }
                                android.view.KeyEvent.KEYCODE_DPAD_CENTER,
                                android.view.KeyEvent.KEYCODE_ENTER -> {
                                    drawerOpen = true
                                    true
                                }
                                else -> false
                            }
                        } else {
                            false
                        }
                    } else {
                        false
                    }
                }
        ) {
            // ── 1. ExoPlayer / WebView Layer ──────────────────
            if (isYouTube) {
                LaunchedEffect(youtubeVideoId) {
                    exoPlayer.stop()
                }
                AndroidView(
                    factory = { ctx ->
                        android.webkit.WebView(ctx).apply {
                            settings.javaScriptEnabled = true
                            settings.mediaPlaybackRequiresUserGesture = false
                            settings.useWideViewPort = true
                            settings.loadWithOverviewMode = true
                            settings.domStorageEnabled = true
                            
                            val defaultUA = settings.userAgentString
                            val modifiedUA = defaultUA
                                .replace("; wv", "")
                                .replace("wv", "")
                                .replace("Version/4.0 ", "")
                                .replace("Version/4.0", "")
                            settings.userAgentString = modifiedUA
                            
                            webChromeClient = android.webkit.WebChromeClient()
                            webViewClient = android.webkit.WebViewClient()
                            
                            layoutParams = FrameLayout.LayoutParams(
                                ViewGroup.LayoutParams.MATCH_PARENT,
                                ViewGroup.LayoutParams.MATCH_PARENT
                            )
                            
                            val html = """
                                <!DOCTYPE html>
                                <html>
                                <head>
                                    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
                                    <style>
                                        html, body { margin: 0; padding: 0; width: 100%; height: 100%; background-color: #000; overflow: hidden; }
                                        iframe { width: 100%; height: 100%; border: 0; }
                                    </style>
                                </head>
                                <body>
                                    <iframe 
                                        src="https://www.youtube.com/embed/$youtubeVideoId?autoplay=1&modestbranding=1&rel=0&showinfo=0&fs=1&mute=0&playsinline=1" 
                                        referrerpolicy="strict-origin-when-cross-origin"
                                        allow="autoplay; encrypted-media; picture-in-picture" 
                                        allowfullscreen>
                                    </iframe>
                                </body>
                                </html>
                            """.trimIndent()
                            
                            loadDataWithBaseURL("https://www.youtube.com", html, "text/html", "utf-8", null)
                        }
                    },
                    modifier = Modifier.fillMaxSize()
                )
            } else {
                AndroidView(
                    factory = { ctx ->
                        PlayerView(ctx).apply {
                            player = exoPlayer
                            useController = false // Draw our own beautiful controllers overlay!
                            resizeMode = when (aspectRatioMode) {
                                AspectRatioMode.FIT -> AspectRatioFrameLayout.RESIZE_MODE_FIT
                                AspectRatioMode.STRETCH -> AspectRatioFrameLayout.RESIZE_MODE_FILL
                                AspectRatioMode.CROP -> AspectRatioFrameLayout.RESIZE_MODE_ZOOM
                            }
                            layoutParams = FrameLayout.LayoutParams(
                                ViewGroup.LayoutParams.MATCH_PARENT,
                                ViewGroup.LayoutParams.MATCH_PARENT
                            )
                        }
                    },
                    update = { playerView ->
                        playerView.resizeMode = when (aspectRatioMode) {
                            AspectRatioMode.FIT -> AspectRatioFrameLayout.RESIZE_MODE_FIT
                            AspectRatioMode.STRETCH -> AspectRatioFrameLayout.RESIZE_MODE_FILL
                            AspectRatioMode.CROP -> AspectRatioFrameLayout.RESIZE_MODE_ZOOM
                        }
                    },
                    modifier = Modifier.fillMaxSize()
                )
            }

            // Compose-based glassmorphic WatermarkLogo overlay aligned to top-right of video
            WatermarkLogo(
                modifier = Modifier
                    .align(AbsoluteAlignment.TopRight)
                    .padding(top = 12.dp, end = 12.dp)
            )

        // App Logo Watermark (Moving Anti-Piracy Watermark)
        if (isWatermarkVisible) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 60.dp, vertical = 60.dp)
            ) {
                Image(
                    painter = painterResource(id = R.drawable.zinou_tv_logo),
                    contentDescription = null,
                    modifier = Modifier
                        .align(watermarkAlignment)
                        .size(70.dp, 35.dp)
                        .alpha(0.2f)
                )
            }
        }

        // ── AdMob Interstitial Ad Loading Overlay ──
        if (isAdLoading) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Color.Black)
                    .clickable(
                        interactionSource = remember { androidx.compose.foundation.interaction.MutableInteractionSource() },
                        indication = null
                    ) { /* Intercept all clicks to avoid triggering controls */ },
                contentAlignment = Alignment.Center
            ) {
                // Back Button in case they want to quit loading
                IconButton(
                    onClick = onBackClick,
                    modifier = Modifier
                        .align(Alignment.TopStart)
                        .padding(24.dp)
                        .background(Color.Black.copy(alpha = 0.5f), shape = RoundedCornerShape(50))
                ) {
                    Icon(
                        imageVector = Icons.Default.ArrowBack,
                        contentDescription = "Back",
                        tint = Color.White
                    )
                }

                Column(
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.Center
                ) {
                    CircularProgressIndicator(color = Primary, modifier = Modifier.size(44.dp))
                    Spacer(modifier = Modifier.height(16.dp))
                    Text(
                        text = if (isArabic) "جاري تحميل الإعلان..." else "Loading Sponsor Ad...",
                        color = Color.White,
                        fontSize = 15.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }

        // ── 2. Click Catcher & Touch Gesture Box ──────────
        Box(
            modifier = Modifier
                .fillMaxSize()
                .pointerInput(isLocked) {
                    detectTapGestures(
                        onTap = {
                            if (isLocked) {
                                showUnlockButton = true
                            } else {
                                showControls = !showControls
                            }
                        }
                    )
                }
        ) {
            if (!isLocked) {
                // Drag detector for Brightness (Left half of the screen)
                Box(
                    modifier = Modifier
                        .fillMaxHeight()
                        .fillMaxWidth(0.35f)
                        .align(Alignment.CenterStart)
                        .pointerInput(currentBrightness) {
                            detectDragGestures(
                                onDragStart = { activeDragBrightness = currentBrightness },
                                onDrag = { change, dragAmount ->
                                    change.consume()
                                    // Height drag sensitivity
                                    val delta = -dragAmount.y / 500f
                                    currentBrightness = (currentBrightness + delta).coerceIn(0.01f, 1.0f)
                                    activity?.let { act ->
                                        val lp = act.window.attributes
                                        lp.screenBrightness = currentBrightness
                                        act.window.attributes = lp
                                    }
                                    activeDragBrightness = currentBrightness
                                    showControls = false
                                },
                                onDragEnd = { activeDragBrightness = null }
                            )
                        }
                )

                // Drag detector for Volume (Right half of the screen)
                Box(
                    modifier = Modifier
                        .fillMaxHeight()
                        .fillMaxWidth(0.35f)
                        .align(Alignment.CenterEnd)
                        .pointerInput(currentVolume) {
                            detectDragGestures(
                                onDragStart = { activeDragVolume = currentVolume },
                                onDrag = { change, dragAmount ->
                                    change.consume()
                                    val delta = -dragAmount.y / 500f
                                    currentVolume = (currentVolume + delta * maxVolume).coerceIn(0f, maxVolume.toFloat())
                                    audioManager.setStreamVolume(android.media.AudioManager.STREAM_MUSIC, currentVolume.toInt(), 0)
                                    activeDragVolume = currentVolume
                                    showControls = false
                                },
                                onDragEnd = { activeDragVolume = null }
                            )
                        }
                )
            }
        }

        // ── 3. Centered Large Gesture Indicators ──────────
        activeDragBrightness?.let { valVal ->
            Box(modifier = Modifier.align(Alignment.Center)) {
                GestureIndicatorOverlay(value = valVal, isBrightness = true)
            }
        }

        activeDragVolume?.let { valVal ->
            Box(modifier = Modifier.align(Alignment.Center)) {
                GestureIndicatorOverlay(value = valVal / maxVolume, isBrightness = false)
            }
        }

        // ── 4. Immersive Controls Overlays ──────────────
        AnimatedVisibility(
            visible = showControls && !isLocked,
            enter = fadeIn() + slideInVertically(initialOffsetY = { -it }),
            exit = fadeOut() + slideOutVertically(targetOffsetY = { -it }),
            modifier = Modifier.align(Alignment.TopCenter)
        ) {
            // Gradient top shadow
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(90.dp)
                    .background(
                        Brush.verticalGradient(
                            colors = listOf(Color.Black.copy(alpha = 0.8f), Color.Transparent)
                        )
                    )
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 16.dp, start = 16.dp, end = 16.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    IconButton(onClick = onBackClick) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back", tint = Color.White)
                    }
                    
                    Spacer(modifier = Modifier.width(8.dp))
                    
                    // Render Stream Selection Circles next to Back Button
                    Row(
                        horizontalArrangement = Arrangement.spacedBy(8.dp),
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier
                            .weight(1f)
                            .padding(end = 16.dp)
                    ) {
                        availableStreams.forEach { stream ->
                            val isSelected = activeStream?.id == stream.id
                            StreamCircleButton(
                                label = stream.label,
                                isSelected = isSelected,
                                onClick = {
                                    activeStream = stream
                                }
                            )
                        }
                    }

                    // Top Action Icons
                    Row(horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                        // CC Subtitle icon
                        IconButton(onClick = {}) {
                            Icon(Icons.Default.ClosedCaption, "Subtitles", tint = Color.White)
                        }

                        // Aspect ratio switch
                        IconButton(
                            onClick = {
                                aspectRatioMode = when (aspectRatioMode) {
                                    AspectRatioMode.FIT -> AspectRatioMode.STRETCH
                                    AspectRatioMode.STRETCH -> AspectRatioMode.CROP
                                    AspectRatioMode.CROP -> AspectRatioMode.FIT
                                }
                            }
                        ) {
                            Icon(Icons.Default.AspectRatio, "Aspect Ratio", tint = Color.White)
                        }

                        // Heart favorite
                        IconButton(onClick = { viewModel.toggleFavorite(channel.id, !channel.isFavorited) }) {
                            Icon(
                                imageVector = if (channel.isFavorited) Icons.Default.Favorite else Icons.Default.FavoriteBorder,
                                contentDescription = "Favorite",
                                tint = if (channel.isFavorited) Color.Red else Color.White
                            )
                        }

                        // Padlock screen lock
                        IconButton(onClick = { isLocked = true }) {
                            Icon(Icons.Default.Lock, "Lock screen", tint = Color.White)
                        }

                        // Search drawer open
                        IconButton(onClick = { drawerOpen = true }) {
                            Icon(Icons.Default.Search, "Search", tint = Color.White)
                        }

                        // Cast to TV (Screen Mirroring)
                        IconButton(
                            onClick = {
                                try {
                                    val intent = android.content.Intent("android.settings.CAST_SETTINGS")
                                    context.startActivity(intent)
                                } catch (e: Exception) {
                                    android.widget.Toast.makeText(context, if (isArabic) "إعدادات البث غير متوفرة" else "Cast settings not available", android.widget.Toast.LENGTH_SHORT).show()
                                }
                            }
                        ) {
                            Icon(Icons.Default.Cast, "Cast to TV", tint = Color.White)
                        }

                        // Picture in Picture
                        IconButton(
                            onClick = {
                                activity?.enterPictureInPictureMode(
                                    android.app.PictureInPictureParams.Builder().build()
                                )
                            }
                        ) {
                            Icon(Icons.Default.PictureInPicture, "PiP", tint = Color.White)
                        }
                    }
                }
            }
        }

        // Left/Right center side sliders
        if (showControls && !isLocked) {
            VerticalSlider(
                value = currentBrightness,
                icon = Icons.Default.WbSunny,
                modifier = Modifier
                    .align(Alignment.CenterStart)
                    .padding(start = 24.dp)
            )

            VerticalSlider(
                value = currentVolume / maxVolume,
                icon = Icons.Default.VolumeUp,
                modifier = Modifier
                    .align(Alignment.CenterEnd)
                    .padding(end = 24.dp)
            )
        }

        // Center playback control buttons
        if (showControls && !isLocked) {
            Row(
                modifier = Modifier.align(Alignment.Center),
                horizontalArrangement = Arrangement.spacedBy(40.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Prev Channel
                IconButton(
                    onClick = { viewModel.playPreviousChannel() },
                    modifier = Modifier
                        .size(56.dp)
                        .background(Color(0x66000000), shape = RoundedCornerShape(50))
                ) {
                    Icon(Icons.Default.SkipPrevious, "Prev Channel", tint = Color.White, modifier = Modifier.size(32.dp))
                }

                // Play / Pause
                IconButton(
                    onClick = {
                        if (isPlaying) exoPlayer.pause() else exoPlayer.play()
                    },
                    modifier = Modifier
                        .size(72.dp)
                        .background(Color(0x99000000), shape = RoundedCornerShape(50))
                        .border(BorderStroke(1.5.dp, Primary), shape = RoundedCornerShape(50))
                ) {
                    Icon(
                        imageVector = if (isPlaying) Icons.Default.Pause else Icons.Default.PlayArrow,
                        contentDescription = "Play/Pause",
                        tint = Primary,
                        modifier = Modifier.size(44.dp)
                    )
                }

                // Next Channel
                IconButton(
                    onClick = { viewModel.playNextChannel() },
                    modifier = Modifier
                        .size(56.dp)
                        .background(Color(0x66000000), shape = RoundedCornerShape(50))
                ) {
                    Icon(Icons.Default.SkipNext, "Next Channel", tint = Color.White, modifier = Modifier.size(32.dp))
                }
            }
        }

        // Floating Drawer Toggle Chevrons on middle-left edge
        if (showControls && !isLocked && !drawerOpen) {
            IconButton(
                onClick = { drawerOpen = true },
                modifier = Modifier
                    .align(Alignment.CenterStart)
                    .padding(start = 80.dp)
                    .size(40.dp)
                    .background(Color(0x66000000), shape = RoundedCornerShape(50))
            ) {
                Icon(Icons.Default.ChevronRight, "Open drawer", tint = Color.White)
            }
        }

        // ── 5. Bottom Metadata Overlay ──────────────────
        AnimatedVisibility(
            visible = showControls && !isLocked,
            enter = fadeIn() + slideInVertically(initialOffsetY = { it }),
            exit = fadeOut() + slideOutVertically(targetOffsetY = { it }),
            modifier = Modifier.align(Alignment.BottomCenter)
        ) {
            // Gradient bottom shadow
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(
                        Brush.verticalGradient(
                            colors = listOf(Color.Transparent, Color.Black.copy(alpha = 0.9f))
                        )
                    )
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 24.dp, vertical = 16.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Left Column: Channel Info Card
                    AsyncImage(
                        model = channel.logoUrl,
                        contentDescription = null,
                        modifier = Modifier
                            .size(54.dp)
                            .clip(RoundedCornerShape(8.dp))
                            .background(Color.White.copy(alpha = 0.1f)),
                        contentScale = ContentScale.Fit
                    )
                    
                    Spacer(modifier = Modifier.width(16.dp))
                    
                    Column(modifier = Modifier.weight(1f)) {
                        Text(
                            text = "${channel.sortOrder} • ${if (isArabic) "المجموعة" else "Group"} : ${if (isArabic && !channel.categoryNameAr.isNullOrEmpty()) channel.categoryNameAr else channel.categoryName ?: "General"}",
                            color = Color.LightGray,
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Medium
                        )
                        Spacer(modifier = Modifier.height(2.dp))
                        Text(
                            text = if (isArabic && !channel.nameAr.isNullOrEmpty()) channel.nameAr else channel.name,
                            color = Color.White,
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(2.dp))
                        Text(
                            text = if (isArabic) "لا توجد معلومات للبرنامج حالياً" else "No Information Available",
                            color = Color.Gray,
                            fontSize = 12.sp
                        )
                    }

                    // Right Column: Decoder, Bitrate, Resolution and Live Badge
                    Column(
                        horizontalAlignment = Alignment.End,
                        modifier = Modifier.padding(start = 16.dp)
                    ) {
                        Text(
                            text = if (isYouTube) "YouTube Decoder" else "c2.qti.avc.decoder",
                            color = Color.LightGray,
                            fontSize = 12.sp,
                            fontWeight = FontWeight.SemiBold
                        )
                        Text(
                            text = if (isYouTube) "Dynamic" else "12132 Kb/s",
                            color = Color.LightGray,
                            fontSize = 12.sp
                        )
                        Text(
                            text = if (isArabic) "لا توجد معلومات" else "No Information",
                            color = Color.Gray,
                            fontSize = 11.sp
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            modifier = Modifier
                                .background(Color.Red.copy(alpha = 0.2f), shape = RoundedCornerShape(4.dp))
                                .padding(horizontal = 8.dp, vertical = 2.dp)
                        ) {
                            Box(
                                modifier = Modifier
                                    .size(6.dp)
                                    .alpha(liveDotAlpha)
                                    .background(Color.Red, shape = RoundedCornerShape(50))
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = "LIVE | ${videoWidth}×${videoHeight}",
                                color = Color.Red,
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold
                            )
                        }
                    }
                }

                // Buffer/Timeline solid bar
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(3.dp)
                        .background(Color.White.copy(alpha = 0.2f))
                ) {
                    Box(
                        modifier = Modifier
                            .fillMaxHeight()
                            .fillMaxWidth(0.85f) // Buffer indicator fake width
                            .background(Color.White)
                    )
                }
            }
        }

        // ── 6. Lock Unlock overlay screen ──────────────────
        if (isLocked) {
            AnimatedVisibility(
                visible = showUnlockButton,
                enter = fadeIn(),
                exit = fadeOut(),
                modifier = Modifier
                    .align(Alignment.CenterStart)
                    .padding(start = 24.dp)
            ) {
                Button(
                    onClick = {
                        isLocked = false
                        showUnlockButton = false
                        showControls = true
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0x99000000)),
                    shape = RoundedCornerShape(50),
                    border = BorderStroke(1.dp, Color.White.copy(alpha = 0.3f)),
                    modifier = Modifier.size(54.dp),
                    contentPadding = PaddingValues(0.dp)
                ) {
                    Icon(
                        imageVector = Icons.Default.LockOpen,
                        contentDescription = "Unlock",
                        tint = Color.White,
                        modifier = Modifier.size(24.dp)
                    )
                }
                
                LaunchedEffect(showUnlockButton) {
                    if (showUnlockButton) {
                        kotlinx.coroutines.delay(3000)
                        showUnlockButton = false
                    }
                }
            }
        }

        // ── 7. Buffering Progress Indicator ────────────────
        if (isLoading && !isYouTube) {
            CircularProgressIndicator(
                color = Primary,
                strokeWidth = 4.dp,
                modifier = Modifier
                    .size(54.dp)
                    .align(Alignment.Center)
            )
        }

        // Stream failed prompt error
        playbackError?.let { err ->
            if (!isYouTube) {
                Box(
                    modifier = Modifier
                        .align(Alignment.Center)
                        .background(Color.Black.copy(alpha = 0.8f), shape = RoundedCornerShape(8.dp))
                        .padding(horizontal = 24.dp, vertical = 12.dp)
                        .border(1.dp, Color.Red.copy(alpha = 0.5f), shape = RoundedCornerShape(8.dp))
                ) {
                    Text(
                        text = err,
                        color = Color.Red,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }

        // ── 8. Side Channel Switcher Drawer (Image 2) ──────
        if (drawerOpen) {
            // Click catcher to dismiss drawer
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Color.Black.copy(alpha = 0.5f))
                    .clickable(
                        interactionSource = remember { MutableInteractionSource() },
                        indication = null
                    ) {
                        drawerOpen = false
                    }
            )
        }

        AnimatedVisibility(
            visible = drawerOpen && !isLocked,
            enter = slideInHorizontally(initialOffsetX = { -it }) + fadeIn(),
            exit = slideOutHorizontally(targetOffsetX = { -it }) + fadeOut(),
            modifier = Modifier
                .fillMaxHeight()
                .width(460.dp)
                .background(Color(0xFA0F0B18))
                .border(BorderStroke(1.dp, GlassBorder), shape = RoundedCornerShape(0.dp))
                .align(Alignment.CenterStart)
        ) {
            Row(modifier = Modifier.fillMaxSize()) {
                // Panel 1: Categories (40% width)
                Column(
                    modifier = Modifier
                        .weight(0.4f)
                        .fillMaxHeight()
                        .background(Color(0xFF0A041A))
                        .padding(12.dp)
                ) {
                    Text(
                        text = if (isArabic) "التصنيفات" else "Live",
                        color = Color.White,
                        fontSize = 16.sp,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(bottom = 8.dp)
                    )

                    OutlinedTextField(
                        value = searchCategoryQuery,
                        onValueChange = { viewModel.setSearchCategory(it) },
                        placeholder = { Text(if (isArabic) "بحث تصنيف..." else "Search Category", fontSize = 11.sp, color = TextMuted) },
                        singleLine = true,
                        textStyle = androidx.compose.ui.text.TextStyle(color = Color.White, fontSize = 12.sp),
                        colors = OutlinedTextFieldDefaults.colors(
                            unfocusedContainerColor = Color(0x1AFFFFFF),
                            focusedContainerColor = Color(0x1AFFFFFF),
                            unfocusedBorderColor = Color.Transparent,
                            focusedBorderColor = Primary
                        ),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(44.dp)
                            .padding(bottom = 8.dp),
                        shape = RoundedCornerShape(8.dp)
                    )

                    LazyColumn(
                        verticalArrangement = Arrangement.spacedBy(4.dp),
                        modifier = Modifier.weight(1f)
                    ) {
                        item {
                            CategoryListItem(
                                name = if (isArabic) "الكل" else "All",
                                isSelected = selectedCategoryId == null,
                                count = viewModel.allChannels.value.size,
                                onClick = { viewModel.selectDrawerCategory(null) }
                            )
                        }
                        items(categories) { cat ->
                            val categoryName = if (isArabic && !cat.nameAr.isNullOrEmpty()) cat.nameAr else cat.name
                            val count = remember(viewModel.allChannels.value, cat.id) {
                                viewModel.allChannels.value.count { it.categoryId == cat.id }
                            }
                            CategoryListItem(
                                name = categoryName,
                                isSelected = selectedCategoryId == cat.id,
                                count = count,
                                onClick = { viewModel.selectDrawerCategory(cat.id) }
                            )
                        }
                    }
                }

                // Vertical Divider
                Box(
                    modifier = Modifier
                        .fillMaxHeight()
                        .width(1.dp)
                        .background(Color.White.copy(alpha = 0.1f))
                )

                // Panel 2: Channels (60% width)
                Column(
                    modifier = Modifier
                        .weight(0.6f)
                        .fillMaxHeight()
                        .background(Color(0xFF0F0B18))
                        .padding(12.dp)
                ) {
                    Text(
                        text = if (isArabic) "القنوات" else "Channels",
                        color = Color.White,
                        fontSize = 16.sp,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(bottom = 8.dp)
                    )

                    OutlinedTextField(
                        value = searchChannelQuery,
                        onValueChange = { viewModel.setSearchChannel(it) },
                        placeholder = { Text(if (isArabic) "بحث القنوات..." else "Search Channels", fontSize = 11.sp, color = TextMuted) },
                        singleLine = true,
                        textStyle = androidx.compose.ui.text.TextStyle(color = Color.White, fontSize = 12.sp),
                        colors = OutlinedTextFieldDefaults.colors(
                            unfocusedContainerColor = Color(0x1AFFFFFF),
                            focusedContainerColor = Color(0x1AFFFFFF),
                            unfocusedBorderColor = Color.Transparent,
                            focusedBorderColor = Primary
                        ),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(44.dp)
                            .padding(bottom = 8.dp),
                        shape = RoundedCornerShape(8.dp)
                    )

                    LazyColumn(
                        verticalArrangement = Arrangement.spacedBy(4.dp),
                        modifier = Modifier.weight(1f)
                    ) {
                        items(channels) { chan ->
                            val isCurrentPlaying = chan.id == channel.id
                            ChannelListItem(
                                channel = chan,
                                isPlaying = isCurrentPlaying,
                                onClick = {
                                    viewModel.loadChannel(chan.id)
                                    // Keep drawer open or close depending on preference, closing is clean
                                }
                            )
                        }
                    }
                }

                // Edge chevron to collapse drawer
                Box(
                    modifier = Modifier
                        .fillMaxHeight()
                        .width(32.dp)
                        .background(Color(0xFF0F0B18))
                        .clickable { drawerOpen = false },
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.ChevronLeft,
                        contentDescription = "Close Drawer",
                        tint = Color.White.copy(alpha = 0.5f)
                    )
                }
            }
        }
    }
}
}

@Composable
fun CategoryListItem(
    name: String,
    isSelected: Boolean,
    count: Int,
    onClick: () -> Unit
) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(6.dp))
            .background(if (isSelected) Color(0x22E5A93C) else Color.Transparent)
            .clickable { onClick() }
            .padding(horizontal = 10.dp, vertical = 10.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        if (isSelected) {
            Box(
                modifier = Modifier
                    .width(3.dp)
                    .height(14.dp)
                    .background(Primary, shape = RoundedCornerShape(50))
            )
            Spacer(modifier = Modifier.width(6.dp))
        }
        Text(
            text = name,
            color = if (isSelected) Primary else Color.White,
            fontSize = 12.sp,
            fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Medium,
            maxLines = 1,
            overflow = TextOverflow.Ellipsis,
            modifier = Modifier.weight(1f)
        )
        Text(
            text = count.toString(),
            color = TextMuted,
            fontSize = 10.sp,
            modifier = Modifier.padding(start = 4.dp)
        )
    }
}

@Composable
fun ChannelListItem(
    channel: Channel,
    isPlaying: Boolean,
    onClick: () -> Unit
) {
    val isArabic = java.util.Locale.getDefault().language == "ar"
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(6.dp))
            .background(if (isPlaying) Color(0x15FFFFFF) else Color.Transparent)
            .clickable { onClick() }
            .padding(horizontal = 8.dp, vertical = 8.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = channel.sortOrder.toString(),
            color = if (isPlaying) Primary else TextMuted,
            fontSize = 12.sp,
            fontWeight = FontWeight.Bold,
            modifier = Modifier.width(26.dp)
        )
        
        AsyncImage(
            model = channel.logoUrl,
            contentDescription = null,
            modifier = Modifier
                .size(26.dp)
                .clip(RoundedCornerShape(4.dp))
                .background(Color.White.copy(alpha = 0.1f)),
            contentScale = ContentScale.Fit
        )
        
        Spacer(modifier = Modifier.width(8.dp))
        
        Text(
            text = if (isArabic && !channel.nameAr.isNullOrEmpty()) channel.nameAr else channel.name,
            color = if (isPlaying) Primary else Color.White,
            fontSize = 13.sp,
            fontWeight = if (isPlaying) FontWeight.Bold else FontWeight.Normal,
            maxLines = 1,
            overflow = TextOverflow.Ellipsis,
            modifier = Modifier.weight(1f)
        )

        if (isPlaying) {
            LiveSignalIndicator()
        }
    }
}

@Composable
fun LiveSignalIndicator() {
    val infiniteTransition = rememberInfiniteTransition(label = "pulse")
    val alpha by infiniteTransition.animateFloat(
        initialValue = 0.3f,
        targetValue = 1f,
        animationSpec = infiniteRepeatable(
            animation = tween(800, easing = LinearEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "alpha"
    )
    Row(
        verticalAlignment = Alignment.CenterVertically,
        modifier = Modifier
            .alpha(alpha)
            .background(Color.Red.copy(alpha = 0.2f), shape = RoundedCornerShape(4.dp))
            .padding(horizontal = 6.dp, vertical = 2.dp)
    ) {
        Box(
            modifier = Modifier
                .size(5.dp)
                .background(Color.Red, shape = RoundedCornerShape(50))
        )
        Spacer(modifier = Modifier.width(4.dp))
        Text(
            text = "((o))",
            color = Color.Red,
            fontSize = 9.sp,
            fontWeight = FontWeight.Bold
        )
    }
}

@Composable
fun GestureIndicatorOverlay(
    value: Float,
    isBrightness: Boolean
) {
    Box(
        modifier = Modifier
            .size(110.dp, 80.dp)
            .background(Color(0xCC0A041A), shape = RoundedCornerShape(12.dp))
            .border(1.dp, Color.White.copy(alpha = 0.15f), shape = RoundedCornerShape(12.dp)),
        contentAlignment = Alignment.Center
    ) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            Icon(
                imageVector = if (isBrightness) Icons.Default.WbSunny else Icons.Default.VolumeUp,
                contentDescription = null,
                tint = Color.White,
                modifier = Modifier.size(28.dp)
            )
            Spacer(modifier = Modifier.height(6.dp))
            Text(
                text = "${(value * 100).toInt()}%",
                color = Color.White,
                fontSize = 14.sp,
                fontWeight = FontWeight.Bold
            )
        }
    }
}

@Composable
fun VerticalSlider(
    value: Float,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    modifier: Modifier = Modifier
) {
    Column(
        horizontalAlignment = Alignment.CenterHorizontally,
        modifier = modifier
            .width(36.dp)
            .fillMaxHeight(0.45f)
            .background(Color(0x33000000), shape = RoundedCornerShape(18.dp))
            .padding(vertical = 10.dp)
    ) {
        Icon(icon, null, tint = Color.White, modifier = Modifier.size(16.dp))
        Spacer(modifier = Modifier.height(8.dp))
        
        // Custom vertical progress bar
        Box(
            modifier = Modifier
                .weight(1f)
                .width(3.dp)
                .background(Color.White.copy(alpha = 0.2f), shape = RoundedCornerShape(1.5.dp)),
            contentAlignment = Alignment.BottomCenter
        ) {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .fillMaxHeight(value.coerceIn(0f, 1f))
                    .background(Color.White, shape = RoundedCornerShape(1.5.dp))
            )
        }
    }
}

@Composable
fun WatermarkLogo(modifier: Modifier = Modifier) {
    Box(
        modifier = modifier
            .width(160.dp)
            .height(55.dp)
            .clip(RoundedCornerShape(8.dp))
            .background(
                brush = Brush.verticalGradient(
                    colors = listOf(
                        Color(0xCC070B14), // Thicker/darker glassmorphic navy base
                        Color(0x990F0B18)
                    )
                ),
                shape = RoundedCornerShape(8.dp)
            )
            .border(
                BorderStroke(1.5.dp, Color.White.copy(alpha = 0.25f)), // Stronger/brighter frosted glass border
                shape = RoundedCornerShape(8.dp)
            )
            .padding(horizontal = 8.dp, vertical = 4.dp),
        contentAlignment = Alignment.Center
    ) {
        Image(
            painter = painterResource(id = R.drawable.zinou_tv_logo),
            contentDescription = "ZINOU TV Logo",
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Fit
        )
    }
}
