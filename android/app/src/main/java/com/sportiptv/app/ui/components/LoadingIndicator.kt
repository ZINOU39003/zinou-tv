package com.sportiptv.app.ui.components

import androidx.compose.animation.core.*
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.scale
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.sportiptv.app.ui.theme.Primary
import com.sportiptv.app.ui.theme.AccentBlue
import com.sportiptv.app.ui.theme.TextMuted

@Composable
fun LoadingIndicator(
    modifier: Modifier = Modifier,
    message: String = "Loading…"
) {
    val infiniteTransition = rememberInfiniteTransition(label = "pulse")
    val scale by infiniteTransition.animateFloat(
        initialValue = 0.8f,
        targetValue = 1.3f,
        animationSpec = infiniteRepeatable(
            animation = tween(800, easing = FastOutSlowInEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "scale"
    )

    Column(
        modifier = modifier.fillMaxSize(),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Box(
            modifier = Modifier
                .size(70.dp)
                .scale(scale)
                .background(
                    brush = Brush.radialGradient(
                        colors = listOf(Primary, AccentBlue, androidx.compose.ui.graphics.Color.Transparent)
                    ),
                    shape = CircleShape
                ),
            contentAlignment = Alignment.Center
        ) {
            Box(
                modifier = Modifier
                    .size(36.dp)
                    .background(androidx.compose.ui.graphics.Color.Black, shape = CircleShape)
            )
        }
        
        Spacer(modifier = Modifier.height(24.dp))
        
        Text(
            text = message,
            color = TextMuted,
            fontSize = 14.sp,
            fontWeight = FontWeight.Medium
        )
    }
}
