package com.sportiptv.app.ui.settings

import android.widget.Toast
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Info
import androidx.compose.material.icons.filled.LockReset
import androidx.compose.material.icons.filled.Logout
import androidx.compose.material.icons.filled.Star
import androidx.compose.material.icons.filled.AdminPanelSettings
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import com.sportiptv.app.ui.theme.*
import kotlinx.coroutines.flow.collectLatest

@Composable
fun SettingsScreen(
    onNavigateToSubscription: () -> Unit,
    onNavigateToAdminPanel: () -> Unit = {},
    onLogout: () -> Unit,
    viewModel: SettingsViewModel = hiltViewModel()
) {
    val context = LocalContext.current
    var showLogoutDialog by remember { mutableStateOf(false) }
    val versionName = remember {
        context.packageManager.getPackageInfo(context.packageName, 0).versionName ?: "1.0.0"
    }

    // --- Admin Mode Secret Activation ---
    val adminPrefs = remember {
        context.getSharedPreferences("admin_prefs", android.content.Context.MODE_PRIVATE)
    }
    var isAdminUnlocked by remember { mutableStateOf(adminPrefs.getBoolean("admin_mode", false)) }
    var aboutTapCount by remember { mutableIntStateOf(0) }
    val requiredTaps = 5

    LaunchedEffect(key1 = true) {
        viewModel.eventFlow.collectLatest { event ->
            when (event) {
                is SettingsViewModel.SettingsEvent.LogoutSuccess -> onLogout()
                is SettingsViewModel.SettingsEvent.ShowToast -> {
                    Toast.makeText(context, event.message, Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    if (showLogoutDialog) {
        AlertDialog(
            onDismissRequest = { showLogoutDialog = false },
            title = {
                Text(
                    text = "\u062a\u0633\u062c\u064a\u0644 \u0627\u0644\u062e\u0631\u0648\u062c",
                    fontWeight = FontWeight.Bold
                )
            },
            text = {
                Text(
                    text = "\u0647\u0644 \u0623\u0646\u062a \u0645\u062a\u0623\u0643\u062f\u061f \u0633\u064a\u062a\u0645 \u0645\u0633\u062d \u0628\u064a\u0627\u0646\u0627\u062a \u0627\u0644\u062f\u062e\u0648\u0644 \u0645\u0646 \u0647\u0630\u0627 \u0627\u0644\u062c\u0647\u0627\u0632."
                )
            },
            confirmButton = {
                TextButton(onClick = { showLogoutDialog = false; viewModel.logout() }) {
                    Text(
                        text = "\u062e\u0631\u0648\u062c",
                        color = DangerColor,
                        fontWeight = FontWeight.Bold
                    )
                }
            },
            dismissButton = {
                TextButton(onClick = { showLogoutDialog = false }) {
                    Text(
                        text = "\u0625\u0644\u063a\u0627\u0621",
                        color = Color.White
                    )
                }
            },
            containerColor = BgSecondary
        )
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(listOf(BgPrimary, Color(0xFF0D1225)))
            )
    ) {
        Column(modifier = Modifier.padding(20.dp)) {
            Text(
                text = "\u0627\u0644\u0625\u0639\u062f\u0627\u062f\u0627\u062a",
                color = Color.White, fontSize = 26.sp, fontWeight = FontWeight.Bold
            )
            Text(
                text = "\u0625\u062f\u0627\u0631\u0629 \u062d\u0633\u0627\u0628\u0643 \u0648\u062a\u0641\u0636\u064a\u0644\u0627\u062a \u0627\u0644\u062a\u0637\u0628\u064a\u0642",
                color = TextMuted, fontSize = 13.sp,
                modifier = Modifier.padding(top = 4.dp)
            )
        }

        LazyColumn(
            modifier = Modifier.fillMaxWidth().weight(1f),
            verticalArrangement = Arrangement.spacedBy(12.dp),
            contentPadding = PaddingValues(horizontal = 20.dp, vertical = 8.dp)
        ) {
            item {
                SettingsRowItem(
                    title = "\u0645\u0639\u0644\u0648\u0645\u0627\u062a \u0627\u0644\u0627\u0634\u062a\u0631\u0627\u0643",
                    subtitle = "\u062a\u062d\u0642\u0642 \u0645\u0646 \u062a\u0627\u0631\u064a\u062e \u0627\u0644\u0627\u0646\u062a\u0647\u0627\u0621 \u0648\u0627\u0644\u0628\u0627\u0642\u0629",
                    icon = Icons.Default.Star,
                    iconColor = Primary,
                    onClick = onNavigateToSubscription
                )
            }
            item {
                SettingsRowItem(
                    title = "\u062a\u062d\u062f\u064a\u062b \u0627\u0644\u0642\u0646\u0648\u0627\u062a",
                    subtitle = "\u0645\u0633\u062d \u0627\u0644\u0630\u0627\u0643\u0631\u0629 \u0627\u0644\u0645\u0624\u0642\u062a\u0629 \u0648\u0625\u0639\u0627\u062f\u0629 \u062a\u062d\u0645\u064a\u0644 \u0627\u0644\u0628\u064a\u0627\u0646\u0627\u062a",
                    icon = Icons.Default.LockReset,
                    iconColor = Color(0xFF3B82F6),
                    onClick = { viewModel.clearCache() }
                )
            }
            item {
                Card(
                    modifier = Modifier.fillMaxWidth().border(
                        androidx.compose.foundation.BorderStroke(0.5.dp, GlassBorder),
                        RoundedCornerShape(14.dp)
                    ),
                    colors = CardDefaults.cardColors(containerColor = BgSecondary.copy(alpha = 0.9f)),
                    shape = RoundedCornerShape(14.dp)
                ) {
                    Column(modifier = Modifier.padding(18.dp)) {
                        Text(
                            text = "\u0645\u0639\u0631\u0651\u0641 \u0627\u0644\u062c\u0647\u0627\u0632",
                            color = Color.White, fontSize = 15.sp, fontWeight = FontWeight.Bold
                        )
                        Text(
                            text = viewModel.getDeviceIdSignature(),
                            color = Primary,
                            fontSize = 12.sp,
                            fontFamily = androidx.compose.ui.text.font.FontFamily.Monospace,
                            modifier = Modifier.padding(top = 6.dp)
                        )
                    }
                }
            }
            item {
                SettingsRowItem(
                    title = "\u062d\u0648\u0644 ZINOU TV",
                    subtitle = "\u0627\u0644\u0625\u0635\u062f\u0627\u0631 $versionName \u2014 \u062a\u0637\u0628\u064a\u0642 IPTV \u0627\u062d\u062a\u0631\u0627\u0641\u064a",
                    icon = Icons.Default.Info,
                    iconColor = Color(0xFF94A3B8),
                    onClick = {
                        if (!isAdminUnlocked) {
                            aboutTapCount++
                            val remaining = requiredTaps - aboutTapCount
                            if (remaining > 0) {
                                Toast.makeText(
                                    context,
                                    "\u0628\u0627\u0642\u064a $remaining \u0646\u0642\u0631\u0627\u062a \u0644\u062a\u0641\u0639\u064a\u0644 \u0648\u0636\u0639 \u0627\u0644\u0645\u0637\u0648\u0631",
                                    Toast.LENGTH_SHORT
                                ).show()
                            } else {
                                isAdminUnlocked = true
                                adminPrefs.edit().putBoolean("admin_mode", true).apply()
                                Toast.makeText(
                                    context,
                                    "\u2705 \u062a\u0645 \u062a\u0641\u0639\u064a\u0644 \u0644\u0648\u062d\u0629 \u0627\u0644\u0625\u062f\u0627\u0631\u0629!",
                                    Toast.LENGTH_LONG
                                ).show()
                                aboutTapCount = 0
                            }
                        }
                    }
                )
            }

            // Admin Panel - Only visible after secret activation
            if (isAdminUnlocked) {
                item {
                    SettingsRowItem(
                        title = "\u0644\u0648\u062d\u0629 \u0627\u0644\u0625\u062f\u0627\u0631\u0629",
                        subtitle = "\u0627\u0644\u0648\u0635\u0648\u0644 \u0625\u0644\u0649 \u0644\u0648\u062d\u0629 \u062a\u062d\u0643\u0645 \u0627\u0644\u062e\u0627\u062f\u0645",
                        icon = Icons.Default.AdminPanelSettings,
                        iconColor = Color(0xFF9D00FF),
                        onClick = onNavigateToAdminPanel
                    )
                }
            }

            item {
                SettingsRowItem(
                    title = "\u062a\u0633\u062c\u064a\u0644 \u0627\u0644\u062e\u0631\u0648\u062c",
                    subtitle = "\u0627\u0644\u062e\u0631\u0648\u062c \u0645\u0646 \u0627\u0644\u062d\u0633\u0627\u0628 \u0639\u0644\u0649 \u0647\u0630\u0627 \u0627\u0644\u062c\u0647\u0627\u0632",
                    icon = Icons.Default.Logout,
                    iconColor = DangerColor,
                    onClick = { showLogoutDialog = true }
                )
            }
        }
    }
}

@Composable
fun SettingsRowItem(
    title: String,
    subtitle: String,
    icon: ImageVector,
    iconColor: Color,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Card(
        modifier = modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(14.dp))
            .border(androidx.compose.foundation.BorderStroke(0.5.dp, GlassBorder), RoundedCornerShape(14.dp))
            .clickable { onClick() },
        colors = CardDefaults.cardColors(containerColor = BgSecondary.copy(alpha = 0.85f)),
        shape = RoundedCornerShape(14.dp)
    ) {
        Row(modifier = Modifier.padding(18.dp), verticalAlignment = Alignment.CenterVertically) {
            Box(
                modifier = Modifier
                    .size(44.dp)
                    .background(iconColor.copy(alpha = 0.15f), RoundedCornerShape(12.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(imageVector = icon, contentDescription = title, tint = iconColor, modifier = Modifier.size(22.dp))
            }
            Spacer(modifier = Modifier.width(16.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(text = title, color = Color.White, fontSize = 15.sp, fontWeight = FontWeight.Bold)
                Text(text = subtitle, color = TextMuted, fontSize = 12.sp, modifier = Modifier.padding(top = 3.dp))
            }
        }
    }
}
