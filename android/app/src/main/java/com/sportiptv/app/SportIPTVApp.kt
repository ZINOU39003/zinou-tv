package com.sportiptv.app

import android.app.Application
import dagger.hilt.android.HiltAndroidApp

import androidx.work.Configuration
import androidx.hilt.work.HiltWorkerFactory
import javax.inject.Inject

@HiltAndroidApp
class SportIPTVApp : Application(), Configuration.Provider {

    @Inject
    lateinit var workerFactory: HiltWorkerFactory

    override val workManagerConfiguration: Configuration
        get() = Configuration.Builder()
            .setWorkerFactory(workerFactory)
            .build()

    override fun onCreate() {
        super.onCreate()
        com.google.android.gms.ads.MobileAds.initialize(this) {}
        // OneSignal Initialization
        com.onesignal.OneSignal.initWithContext(this, "caca1acd-7cc9-4d14-af31-4ce9bcf4e52b")
        
        // Setup WorkManager to periodically check for matches
        com.sportiptv.app.notifications.MatchNotificationWorker.setupPeriodicWork(this)
    }
}
