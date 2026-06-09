package com.sportiptv.app.ui.auth

import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import com.sportiptv.app.ui.theme.*
import kotlinx.coroutines.flow.collectLatest

@Composable
fun ActivationScreen(
    onActivationSuccess: () -> Unit,
    onNavigateToLogin: () -> Unit,
    viewModel: ActivationViewModel = hiltViewModel()
) {
    val context = LocalContext.current
    val code by viewModel.code.collectAsState()
    val activationState by viewModel.activationState.collectAsState()
    val ipAddress = remember { com.sportiptv.app.util.NetworkUtils.getLocalIpAddress() }

    LaunchedEffect(key1 = true) {
        viewModel.eventFlow.collectLatest { event ->
            when (event) {
                is ActivationViewModel.ActivationEvent.ActivationSuccess -> {
                    Toast.makeText(context, "تم تفعيل الحساب بنجاح!", Toast.LENGTH_SHORT).show()
                    onActivationSuccess()
                }
                is ActivationViewModel.ActivationEvent.ShowError -> {
                    Toast.makeText(context, event.message, Toast.LENGTH_LONG).show()
                }
            }
        }
    }

    Box(
        modifier = Modifier.fillMaxSize(),
        contentAlignment = Alignment.Center
    ) {
        androidx.compose.foundation.Image(
            painter = androidx.compose.ui.res.painterResource(id = com.sportiptv.app.R.drawable.world_cup_bg),
            contentDescription = null,
            modifier = Modifier.fillMaxSize(),
            contentScale = androidx.compose.ui.layout.ContentScale.Crop
        )

        // Dark overlay
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Color.Black.copy(alpha = 0.65f))
        )

        Column(
            modifier = Modifier
                .fillMaxWidth(if (androidx.compose.ui.platform.LocalConfiguration.current.orientation == android.content.res.Configuration.ORIENTATION_LANDSCAPE) 0.6f else 0.9f)
                .padding(28.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            // Activation Logo
            androidx.compose.foundation.Image(
                painter = androidx.compose.ui.res.painterResource(id = com.sportiptv.app.R.drawable.zinou_tv_logo),
                contentDescription = "ZINOU Tv Logo",
                modifier = Modifier.size(100.dp)
            )

            Spacer(modifier = Modifier.height(16.dp))

            Text(
                text = "تسجيل الدخول - تفعيل ZINOU TV PRO",
                color = Color.White,
                fontSize = 24.sp,
                fontWeight = FontWeight.Bold,
                textAlign = TextAlign.Center
            )

            Text(
                text = "أدخل كود التفعيل لربط حسابك وتنشيط الخدمة على هذا الجهاز",
                color = TextMuted,
                fontSize = 13.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.padding(top = 4.dp)
            )

            Spacer(modifier = Modifier.height(24.dp))

            // Premium IP address card
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(bottom = 20.dp),
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = Color(0x33000000)),
                border = BorderStroke(1.dp, Primary.copy(alpha = 0.4f))
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp, vertical = 12.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "كود IP الخاص بجهازك:",
                        color = Color.White,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Medium
                    )
                    Text(
                        text = ipAddress,
                        color = Primary,
                        fontSize = 16.sp,
                        fontWeight = FontWeight.Bold,
                        letterSpacing = 1.sp
                    )
                }
            }

            // Activation Code Segment Field
            OutlinedTextField(
                value = code,
                onValueChange = { viewModel.onCodeChange(it) },
                label = { Text("كود التفعيل") },
                singleLine = true,
                placeholder = { Text("XXXX-XXXX-XXXX-XXXX") },
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(10.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = Primary,
                    unfocusedBorderColor = GlassBorder,
                    focusedLabelColor = Primary,
                    unfocusedLabelColor = TextMuted,
                    focusedTextColor = Color.White,
                    unfocusedTextColor = Color.White
                )
            )

            Spacer(modifier = Modifier.height(8.dp))

            Text(
                text = "الصيغة المطلوبة: XXXX-XXXX-XXXX-XXXX",
                color = TextMuted,
                fontSize = 11.sp,
                textAlign = TextAlign.Right,
                modifier = Modifier.fillMaxWidth().padding(end = 4.dp)
            )

            Spacer(modifier = Modifier.height(24.dp))

            // Activate Button
            Button(
                onClick = { viewModel.activate() },
                enabled = activationState !is ActivationViewModel.ActivationState.Loading,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp)
                    .clip(RoundedCornerShape(10.dp)),
                colors = ButtonDefaults.buttonColors(
                    containerColor = Primary,
                    contentColor = Color.Black
                )
            ) {
                if (activationState is ActivationViewModel.ActivationState.Loading) {
                    CircularProgressIndicator(color = Color.Black, modifier = Modifier.size(24.dp))
                } else {
                    Text(
                        text = "تفعيل البرنامج والولوج",
                        fontSize = 16.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }

            Spacer(modifier = Modifier.height(20.dp))

            // Direct link back to login page
            Row(
                modifier = Modifier.clickable { onNavigateToLogin() },
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "هل تملك حساباً مسجلاً بالفعل؟ ",
                    color = TextMuted,
                    fontSize = 13.sp
                )
                Text(
                    text = "سجل الدخول من هنا",
                    color = Primary,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold
                )
            }
        }
    }
}
