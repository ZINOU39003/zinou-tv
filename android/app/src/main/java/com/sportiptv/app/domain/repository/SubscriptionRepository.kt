package com.sportiptv.app.domain.repository

import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.Subscription
import kotlinx.coroutines.flow.Flow

interface SubscriptionRepository {
    fun activateLicense(code: String): Flow<Resource<Subscription>>
    fun validateLicense(): Flow<Resource<Subscription>>
    fun getSubscriptionDetails(): Flow<Resource<Subscription>>
    fun getAppConfig(): Flow<Resource<com.sportiptv.app.data.remote.dto.AppConfigDto>>
}
