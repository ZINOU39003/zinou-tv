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
    onLogout: () -> Unit,
    viewModel: SettingsViewModel = hiltViewModel()
) {
    val context = LocalContext.current
    var showLogoutDialog by remember { mutableStateOf(false) }
    val versionName = remember {
        context.packageManager.getPackageInfo(context.packageName, 0).versionName ?: "1.0.0"
    }

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
            title = { Text("تسجيل الخروج", fontWeight = FontWeight.Bold) },
            text = { Text("هل أنت متأكد؟ سيتم مسح بيانات الدخول من هذا الجهاز.") },
            confirmButton = {
                TextButton(onClick = { showLogoutDialog = false; viewModel.logout() }) {
                    Text("خروج", color = DangerColor, fontWeight = FontWeight.Bold)
                }
            },
            dismissButton = {
                TextButton(onClick = { showLogoutDialog = false }) {
                    Text("إلغاء", color = Color.White)
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
            Text("الإعدادات", color = Color.White, fontSize = 26.sp, fontWeight = FontWeight.Bold)
            Text("إدارة حسابك وتفضيلات التطبيق", color = TextMuted, fontSize = 13.sp, modifier = Modifier.padding(top = 4.dp))
        }

        LazyColumn(
            modifier = Modifier.fillMaxWidth().weight(1f),
            verticalArrangement = Arrangement.spacedBy(12.dp),
            contentPadding = PaddingValues(horizontal = 20.dp, vertical = 8.dp)
        ) {
            item {
                SettingsRowItem(
                    title = "معلومات الاشتراك",
                    subtitle = "تحقق من تاريخ الانتهاء والباقة",
                    icon = Icons.Default.Star,
                    iconColor = Primary,
                    onClick = onNavigateToSubscription
                )
            }
            item {
                SettingsRowItem(
                    title = "تحديث القنوات",
                    subtitle = "مسح الذاكرة المؤقتة وإعادة تحميل البيانات",
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
                        Text("معرّف الجهاز", color = Color.White, fontSize = 15.sp, fontWeight = FontWeight.Bold)
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
                    title = "حول ZINOU TV",
                    subtitle = "الإصدار $versionName — تطبيق IPTV احترافي",
                    icon = Icons.Default.Info,
                    iconColor = Color(0xFF94A3B8),
                    onClick = {}
                )
            }
            item {
                SettingsRowItem(
                    title = "تسجيل الخروج",
                    subtitle = "الخروج من الحساب على هذا الجهاز",
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
