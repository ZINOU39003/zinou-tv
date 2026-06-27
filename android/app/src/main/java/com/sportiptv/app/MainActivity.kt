package com.sportiptv.app

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.ui.Modifier
import com.sportiptv.app.ui.navigation.NavGraph
import com.sportiptv.app.ui.theme.SportIPTVTheme
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import androidx.lifecycle.lifecycleScope
import com.sportiptv.app.domain.repository.SubscriptionRepository
import javax.inject.Inject
import kotlinx.coroutines.flow.collectLatest

@AndroidEntryPoint
class MainActivity : AppCompatActivity() {
    
    @Inject
    lateinit var subscriptionRepository: SubscriptionRepository
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Match status and navigation bars to app background
        window.statusBarColor = android.graphics.Color.parseColor("#0A041A")
        window.navigationBarColor = android.graphics.Color.parseColor("#0A041A")
        
        // Request Notification Permission (Android 13+)
        lifecycleScope.launch {
            com.onesignal.OneSignal.Notifications.requestPermission(true)
        }
        
        setContent {
            SportIPTVTheme {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    NavGraph()
                }
            }
        }
    }
}
