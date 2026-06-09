package com.sportiptv.app.ui.theme

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.runtime.Composable

private val DarkColorScheme = darkColorScheme(
    primary = Primary,
    onPrimary = BgPrimary,
    secondary = AccentBlue,
    onSecondary = TextMain,
    background = BgPrimary,
    onBackground = TextMain,
    surface = BgSecondary,
    onSurface = TextMain,
    error = DangerColor
)

@Composable
fun SportIPTVTheme(
    darkTheme: Boolean = true, // We always force Dark Theme for sport app premium aesthetics
    content: @Composable () -> Unit
) {
    val colorScheme = DarkColorScheme

    MaterialTheme(
        colorScheme = colorScheme,
        typography = Typography,
        shapes = Shapes,
        content = content
    )
}
