package com.sportiptv.app.ui.subscription

import android.content.Intent
import android.net.Uri
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Star
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalConfiguration
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.Subscription
import com.sportiptv.app.ui.components.ErrorView
import com.sportiptv.app.ui.components.LoadingIndicator
import com.sportiptv.app.ui.theme.*
import com.sportiptv.app.util.Constants

data class SubscriptionPackage(
    val id: String,
    val nameAr: String,
    val nameEn: String,
    val durationAr: String,
    val price: String,
    val features: List<String>,
    val isPopular: Boolean = false
)

val packagesList = listOf(
    SubscriptionPackage(
        id = "1_month",
        nameAr = "باقة الشهر الواحد",
        nameEn = "1 Month Pro Plan",
        durationAr = "30 يوم",
        price = "500 DZD",
        features = listOf("بدون إعلانات تماماً", "جودة عالية FHD / UHD", "جميع باقات القنوات والسينما", "دعم فني 24/7")
    ),
    SubscriptionPackage(
        id = "3_months",
        nameAr = "باقة 3 أشهر",
        nameEn = "3 Months Pro Plan",
        durationAr = "90 يوم",
        price = "1200 DZD",
        features = listOf("بدون إعلانات تماماً", "جودة عالية FHD / UHD", "جميع باقات القنوات والسينما", "دعم فني 24/7")
    ),
    SubscriptionPackage(
        id = "6_months",
        nameAr = "باقة 6 أشهر",
        nameEn = "6 Months Pro Plan",
        durationAr = "180 يوم",
        price = "2000 DZD",
        features = listOf("بدون إعلانات تماماً", "جودة عالية FHD / UHD", "جميع باقات القنوات والسينما", "دعم فني 24/7", "تحديثات قائمة مجانية")
    ),
    SubscriptionPackage(
        id = "12_months",
        nameAr = "باقة 12 شهراً",
        nameEn = "12 Months Gold Plan",
        durationAr = "365 يوم",
        price = "3500 DZD",
        features = listOf("بدون إعلانات تماماً", "جودة عالية FHD / UHD", "جميع باقات القنوات والسينما", "دعم فني 24/7", "تحديثات قائمة مجانية", "خصم خاص للدفع السنوي"),
        isPopular = true
    )
)

@Composable
fun SubscriptionScreen(
    onBackClick: () -> Unit,
    viewModel: SubscriptionViewModel = hiltViewModel()
) {
    val state by viewModel.subscriptionState.collectAsState()
    val configState by viewModel.appConfigState.collectAsState()
    val context = LocalContext.current
    val configuration = LocalConfiguration.current
    val isLandscape = configuration.orientation == android.content.res.Configuration.ORIENTATION_LANDSCAPE

    val currentPackages = androidx.compose.runtime.remember(configState) {
        val configData = (configState as? Resource.Success)?.data
        configData?.packages?.map { pkg ->
            SubscriptionPackage(
                id = pkg.id,
                nameAr = pkg.nameAr,
                nameEn = pkg.nameEn,
                durationAr = pkg.durationAr,
                price = pkg.price,
                features = pkg.features,
                isPopular = pkg.isPopular
            )
        } ?: packagesList
    }

    val currentWhatsAppNumber = androidx.compose.runtime.remember(configState) {
        val configData = (configState as? Resource.Success)?.data
        configData?.whatsapp_number ?: Constants.WHATSAPP_NUMBER
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BgPrimary)
    ) {
        // Custom Header TopBar
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            IconButton(onClick = onBackClick) {
                Icon(
                    imageVector = Icons.Default.ArrowBack,
                    contentDescription = "Back",
                    tint = Color.White
                )
            }
            
            Spacer(modifier = Modifier.width(8.dp))
            
            Text(
                text = "باقات الاشتراك ZINOU TV PRO",
                color = Color.White,
                fontSize = 20.sp,
                fontWeight = FontWeight.Bold
            )
        }

        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 16.dp),
            verticalArrangement = Arrangement.spacedBy(20.dp),
            contentPadding = PaddingValues(bottom = 32.dp)
        ) {
            item {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(vertical = 12.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Text(
                        text = "اشترك في ZINOU TV PRO بدون إعلانات",
                        color = Primary,
                        fontSize = 22.sp,
                        fontWeight = FontWeight.ExtraBold,
                        textAlign = TextAlign.Center
                    )
                    Text(
                        text = "اختر الباقة المناسبة لك للدردشة المباشرة مع الدعم وشراء حسابك فوراً عبر الواتساب",
                        color = TextMuted,
                        fontSize = 13.sp,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.padding(top = 6.dp)
                    )
                }
            }

            // Render packages responsive
            if (isLandscape) {
                item {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(16.dp)
                    ) {
                        currentPackages.forEach { pkg ->
                            Box(modifier = Modifier.weight(1f)) {
                                PackageCard(pkg = pkg, onClick = {
                                    openWhatsAppForPackage(context, pkg, currentWhatsAppNumber)
                                })
                            }
                        }
                    }
                }
            } else {
                items(currentPackages) { pkg ->
                    PackageCard(pkg = pkg, onClick = {
                        openWhatsAppForPackage(context, pkg, currentWhatsAppNumber)
                    })
                }
            }

            // Current user details if logged in
            item {
                Spacer(modifier = Modifier.height(16.dp))
                Text(
                    text = "تفاصيل اشتراكي الحالي",
                    color = Color.White,
                    fontSize = 16.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(bottom = 12.dp)
                )
            }

            item {
                when (val subState = state) {
                    is Resource.Loading -> {
                        Box(
                            modifier = Modifier.fillMaxWidth().height(150.dp),
                            contentAlignment = Alignment.Center
                        ) {
                            CircularProgressIndicator(color = Primary)
                        }
                    }
                    is Resource.Success -> {
                        SubscriptionDetailsContent(subscription = subState.data)
                    }
                    is Resource.Error -> {
                        Card(
                            modifier = Modifier.fillMaxWidth(),
                            colors = CardDefaults.cardColors(containerColor = BgSecondary),
                            shape = RoundedCornerShape(12.dp)
                        ) {
                            Column(
                                modifier = Modifier.padding(16.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text(
                                    text = "لم يتم تفعيل أي كود اشتراك بعد على هذا الجهاز.",
                                    color = TextMuted,
                                    fontSize = 13.sp,
                                    textAlign = TextAlign.Center
                                )
                            }
                        }
                    }
                    else -> {}
                }
            }
        }
    }
}

@Composable
fun PackageCard(
    pkg: SubscriptionPackage,
    onClick: () -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() }
            .border(
                width = if (pkg.isPopular) 2.dp else 1.dp,
                color = if (pkg.isPopular) Primary else GlassBorder,
                shape = RoundedCornerShape(16.dp)
            ),
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(
            containerColor = if (pkg.isPopular) Color(0xFF161025) else BgSecondary
        )
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(20.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            if (pkg.isPopular) {
                Box(
                    modifier = Modifier
                        .background(Primary, shape = RoundedCornerShape(50))
                        .padding(horizontal = 12.dp, vertical = 4.dp)
                        .align(Alignment.End)
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(
                            imageVector = Icons.Default.Star,
                            contentDescription = null,
                            tint = Color.Black,
                            modifier = Modifier.size(14.dp)
                        )
                        Spacer(modifier = Modifier.width(4.dp))
                        Text(
                            text = "الأكثر شعبية",
                            color = Color.Black,
                            fontSize = 10.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }
                }
            }

            Text(
                text = pkg.nameAr,
                color = if (pkg.isPopular) Primary else Color.White,
                fontSize = 18.sp,
                fontWeight = FontWeight.ExtraBold,
                textAlign = TextAlign.Center
            )

            Spacer(modifier = Modifier.height(8.dp))

            Text(
                text = pkg.price,
                color = Color.White,
                fontSize = 28.sp,
                fontWeight = FontWeight.Black,
                textAlign = TextAlign.Center
            )

            Text(
                text = pkg.durationAr,
                color = TextMuted,
                fontSize = 12.sp,
                fontWeight = FontWeight.Medium
            )

            Spacer(modifier = Modifier.height(16.dp))

            Divider(color = GlassBorder)

            Spacer(modifier = Modifier.height(16.dp))

            Column(
                modifier = Modifier.fillMaxWidth(),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                pkg.features.forEach { feature ->
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.End
                    ) {
                        Text(
                            text = feature,
                            color = Color.White,
                            fontSize = 12.sp,
                            textAlign = TextAlign.End,
                            modifier = Modifier.weight(1f)
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        Icon(
                            imageVector = Icons.Default.CheckCircle,
                            contentDescription = null,
                            tint = Primary,
                            modifier = Modifier.size(16.dp)
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(20.dp))

            Button(
                onClick = onClick,
                modifier = Modifier.fillMaxWidth(),
                colors = ButtonDefaults.buttonColors(
                    containerColor = if (pkg.isPopular) Primary else Color.White,
                    contentColor = Color.Black
                ),
                shape = RoundedCornerShape(10.dp)
            ) {
                Text(
                    text = "شراء واشتراك عبر واتساب",
                    fontWeight = FontWeight.Bold,
                    fontSize = 13.sp
                )
            }
        }
    }
}

@Composable
fun SubscriptionDetailsContent(
    subscription: Subscription,
    modifier: Modifier = Modifier
) {
    Column(
        modifier = modifier
            .fillMaxWidth()
            .background(BgSecondary, shape = RoundedCornerShape(12.dp))
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        InfoRowItem(
            label = "كود الاشتراك",
            value = subscription.code
        )
        Divider(color = GlassBorder)
        InfoRowItem(
            label = "نوع الباقة",
            value = subscription.duration?.replace("_", " ")?.replaceFirstChar { it.uppercase() } ?: "—"
        )
        Divider(color = GlassBorder)
        InfoRowItem(
            label = "الأيام المتبقية",
            value = "${subscription.daysRemaining} يوم"
        )
        Divider(color = GlassBorder)
        InfoRowItem(
            label = "تاريخ التفعيل",
            value = subscription.activatedAt?.take(10) ?: "—"
        )
        Divider(color = GlassBorder)
        InfoRowItem(
            label = "تاريخ الانتهاء",
            value = subscription.expiresAt?.take(10) ?: "—"
        )
    }
}

@Composable
fun InfoRowItem(
    label: String,
    value: String,
    modifier: Modifier = Modifier
) {
    Row(
        modifier = modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = value,
            color = Color.White,
            fontSize = 14.sp,
            fontWeight = FontWeight.SemiBold
        )
        Text(
            text = label,
            color = TextMuted,
            fontSize = 13.sp,
            fontWeight = FontWeight.Medium
        )
    }
}

@Composable
fun Divider(color: Color, modifier: Modifier = Modifier) {
    Box(
        modifier = modifier
            .fillMaxWidth()
            .height(1.dp)
            .background(color)
    )
}

private fun openWhatsAppForPackage(context: android.content.Context, pkg: SubscriptionPackage, whatsappNumber: String) {
    try {
        val message = "السلام عليكم، أريد الاشتراك في ZINOU TV PRO. الباقة المختارة: ${pkg.nameAr} (${pkg.durationAr}) بسعر ${pkg.price}."
        val url = "https://api.whatsapp.com/send?phone=${whatsappNumber}&text=${Uri.encode(message)}"
        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
        context.startActivity(intent)
    } catch (e: Exception) {
        e.printStackTrace()
    }
}
