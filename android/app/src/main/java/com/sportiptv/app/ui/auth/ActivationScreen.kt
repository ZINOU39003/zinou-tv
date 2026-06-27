package com.sportiptv.app.ui.auth

import android.content.res.Configuration
import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
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
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalConfiguration
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import com.sportiptv.app.R
import com.sportiptv.app.ui.theme.*
import com.sportiptv.app.util.NetworkUtils
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
    val ipAddress = remember { NetworkUtils.getLocalIpAddress() }
    val configuration = LocalConfiguration.current
    val isLandscape = configuration.orientation == Configuration.ORIENTATION_LANDSCAPE

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
        modifier = Modifier
            .fillMaxSize()
            .background(
                brush = Brush.radialGradient(
                    colors = listOf(Color(0xFF141926), Color(0xFF070B13)),
                    radius = 1200f
                )
            ),
        contentAlignment = Alignment.Center
    ) {
        if (isLandscape) {
            Row(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(40.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.Center
            ) {
                // Left Pane: Large App Logo
                Box(
                    modifier = Modifier
                        .weight(1f)
                        .fillMaxHeight(),
                    contentAlignment = Alignment.Center
                ) {
                    Image(
                        painter = painterResource(id = R.drawable.zinou_tv_logo),
                        contentDescription = "ZINOU Tv Logo",
                        modifier = Modifier.size(280.dp),
                        contentScale = ContentScale.Fit
                    )
                }

                // Right Pane: Activation Form
                Box(
                    modifier = Modifier
                        .weight(1f)
                        .fillMaxHeight(),
                    contentAlignment = Alignment.Center
                ) {
                    ActivationFormCard(
                        code = code,
                        ipAddress = ipAddress,
                        activationState = activationState,
                        onCodeChange = viewModel::onCodeChange,
                        onActivate = viewModel::activate,
                        onNavigateToLogin = onNavigateToLogin
                    )
                }
            }
        } else {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(24.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Image(
                    painter = painterResource(id = R.drawable.zinou_tv_logo),
                    contentDescription = "ZINOU Tv Logo",
                    modifier = Modifier.size(140.dp),
                    contentScale = ContentScale.Fit
                )
                Spacer(modifier = Modifier.height(32.dp))
                ActivationFormCard(
                    code = code,
                    ipAddress = ipAddress,
                    activationState = activationState,
                    onCodeChange = viewModel::onCodeChange,
                    onActivate = viewModel::activate,
                    onNavigateToLogin = onNavigateToLogin
                )
            }
        }
    }
}

@Composable
fun ActivationFormCard(
    code: String,
    ipAddress: String,
    activationState: ActivationViewModel.ActivationState,
    onCodeChange: (String) -> Unit,
    onActivate: () -> Unit,
    onNavigateToLogin: () -> Unit
) {
    Card(
        modifier = Modifier.widthIn(max = 400.dp).fillMaxWidth(),
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0x660A0E17)),
        border = BorderStroke(1.dp, Color(0x33FFFFFF))
    ) {
        Column(
            modifier = Modifier.padding(28.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(
                text = "تسجيل الدخول - تفعيل ZINOU TV PRO",
                color = Color.White,
                fontSize = 20.sp,
                fontWeight = FontWeight.Bold,
                textAlign = TextAlign.Center
            )

            Spacer(modifier = Modifier.height(20.dp))

            OutlinedTextField(
                value = code,
                onValueChange = onCodeChange,
                label = { Text("كود التفعيل", color = Color(0xFFB0B0B0)) },
                singleLine = true,
                placeholder = { Text("أدخل كود التفعيل هنا") },
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(8.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = Color(0xFFFF9800), // Orange matching the screenshot
                    unfocusedBorderColor = Color(0x44FFFFFF),
                    focusedTextColor = Color.White,
                    unfocusedTextColor = Color.White
                )
            )

            Spacer(modifier = Modifier.height(24.dp))

            Button(
                onClick = onActivate,
                enabled = activationState !is ActivationViewModel.ActivationState.Loading,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp),
                shape = RoundedCornerShape(8.dp),
                colors = ButtonDefaults.buttonColors(
                    containerColor = Color(0xFFFF9800),
                    contentColor = Color.White
                )
            ) {
                if (activationState is ActivationViewModel.ActivationState.Loading) {
                    CircularProgressIndicator(color = Color.White, modifier = Modifier.size(24.dp))
                } else {
                    Text(
                        text = "تفعيل",
                        fontSize = 18.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }

            Spacer(modifier = Modifier.height(20.dp))

            Row(
                modifier = Modifier.clickable { onNavigateToLogin() },
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "هل تملك حساباً؟ ",
                    color = Color(0xFFB0B0B0),
                    fontSize = 13.sp
                )
                Text(
                    text = "سجل الدخول هنا",
                    color = Color(0xFFFF9800),
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold
                )
            }
        }
    }
}
