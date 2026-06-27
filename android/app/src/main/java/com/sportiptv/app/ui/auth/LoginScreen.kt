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
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import com.sportiptv.app.R
import com.sportiptv.app.ui.theme.*
import kotlinx.coroutines.flow.collectLatest

@Composable
fun LoginScreen(
    onLoginSuccess: () -> Unit,
    onNavigateToActivation: () -> Unit,
    viewModel: LoginViewModel = hiltViewModel()
) {
    val context = LocalContext.current
    val email by viewModel.email.collectAsState()
    val password by viewModel.password.collectAsState()
    val loginState by viewModel.loginState.collectAsState()
    val configuration = LocalConfiguration.current
    val isLandscape = configuration.orientation == Configuration.ORIENTATION_LANDSCAPE

    LaunchedEffect(key1 = true) {
        viewModel.eventFlow.collectLatest { event ->
            when (event) {
                is LoginViewModel.LoginEvent.LoginSuccess -> onLoginSuccess()
                is LoginViewModel.LoginEvent.ShowError -> {
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

                // Right Pane: Login Form
                Box(
                    modifier = Modifier
                        .weight(1f)
                        .fillMaxHeight(),
                    contentAlignment = Alignment.Center
                ) {
                    LoginFormCard(
                        email = email,
                        password = password,
                        loginState = loginState,
                        onEmailChange = viewModel::onEmailChange,
                        onPasswordChange = viewModel::onPasswordChange,
                        onLogin = viewModel::login,
                        onNavigateToActivation = onNavigateToActivation
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
                LoginFormCard(
                    email = email,
                    password = password,
                    loginState = loginState,
                    onEmailChange = viewModel::onEmailChange,
                    onPasswordChange = viewModel::onPasswordChange,
                    onLogin = viewModel::login,
                    onNavigateToActivation = onNavigateToActivation
                )
            }
        }
    }
}

@Composable
fun LoginFormCard(
    email: String,
    password: String,
    loginState: LoginViewModel.LoginState,
    onEmailChange: (String) -> Unit,
    onPasswordChange: (String) -> Unit,
    onLogin: () -> Unit,
    onNavigateToActivation: () -> Unit
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
                text = "تسجيل الدخول",
                color = Color.White,
                fontSize = 20.sp,
                fontWeight = FontWeight.Bold,
                textAlign = TextAlign.Center
            )

            Spacer(modifier = Modifier.height(20.dp))

            OutlinedTextField(
                value = email,
                onValueChange = onEmailChange,
                label = { Text("البريد الإلكتروني", color = Color(0xFFB0B0B0)) },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(8.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = Color(0xFFFF9800),
                    unfocusedBorderColor = Color(0x44FFFFFF),
                    focusedTextColor = Color.White,
                    unfocusedTextColor = Color.White
                )
            )

            Spacer(modifier = Modifier.height(16.dp))

            OutlinedTextField(
                value = password,
                onValueChange = onPasswordChange,
                label = { Text("كلمة المرور", color = Color(0xFFB0B0B0)) },
                singleLine = true,
                visualTransformation = PasswordVisualTransformation(),
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(8.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = Color(0xFFFF9800),
                    unfocusedBorderColor = Color(0x44FFFFFF),
                    focusedTextColor = Color.White,
                    unfocusedTextColor = Color.White
                )
            )

            Spacer(modifier = Modifier.height(24.dp))

            Button(
                onClick = onLogin,
                enabled = loginState !is LoginViewModel.LoginState.Loading,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp),
                shape = RoundedCornerShape(8.dp),
                colors = ButtonDefaults.buttonColors(
                    containerColor = Color(0xFFFF9800),
                    contentColor = Color.White
                )
            ) {
                if (loginState is LoginViewModel.LoginState.Loading) {
                    CircularProgressIndicator(color = Color.White, modifier = Modifier.size(24.dp))
                } else {
                    Text(
                        text = "تسجيل الدخول",
                        fontSize = 18.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }

            Spacer(modifier = Modifier.height(20.dp))

            Row(
                modifier = Modifier.clickable { onNavigateToActivation() },
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "لديك كود تفعيل؟ ",
                    color = Color(0xFFB0B0B0),
                    fontSize = 13.sp
                )
                Text(
                    text = "فعل من هنا",
                    color = Color(0xFFFF9800),
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold
                )
            }
        }
    }
}
