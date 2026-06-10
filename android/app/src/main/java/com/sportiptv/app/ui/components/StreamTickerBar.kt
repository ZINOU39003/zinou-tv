package com.sportiptv.app.ui.components

import androidx.compose.animation.core.LinearEasing
import androidx.compose.animation.core.animateFloat
import androidx.compose.animation.core.infiniteRepeatable
import androidx.compose.animation.core.rememberInfiniteTransition
import androidx.compose.animation.core.tween
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clipToBounds
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.layout
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

@Composable
fun StreamTickerBar(
    text: String,
    modifier: Modifier = Modifier
) {
    if (text.isBlank()) return

    val transition = rememberInfiniteTransition(label = "ticker")
    val offset by transition.animateFloat(
        initialValue = 1f,
        targetValue = -1f,
        animationSpec = infiniteRepeatable(
            animation = tween(durationMillis = (text.length * 120).coerceIn(8000, 20000), easing = LinearEasing)
        ),
        label = "tickerOffset"
    )

    Box(
        modifier = modifier
            .fillMaxWidth()
            .background(Color(0xCC000000))
            .padding(vertical = 6.dp)
            .clipToBounds(),
        contentAlignment = Alignment.CenterStart
    ) {
        Text(
            text = text,
            color = Color(0xFFFFD700),
            fontSize = 13.sp,
            fontWeight = FontWeight.Bold,
            maxLines = 1,
            modifier = Modifier.layout { measurable, constraints ->
                val placeable = measurable.measure(constraints.copy(minWidth = 0))
                val parentWidth = constraints.maxWidth
                val x = (parentWidth * offset).toInt()
                layout(placeable.width, placeable.height) {
                    placeable.placeRelative(x, 0)
                }
            }
        )
    }
}
