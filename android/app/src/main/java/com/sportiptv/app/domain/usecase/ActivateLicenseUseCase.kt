package com.sportiptv.app.domain.usecase

import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.Subscription
import com.sportiptv.app.domain.repository.SubscriptionRepository
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import javax.inject.Inject

class ActivateLicenseUseCase @Inject constructor(
    private val subscriptionRepository: SubscriptionRepository
) {
    operator fun invoke(code: String): Flow<Resource<Subscription>> {
        val cleanCode = code.trim().uppercase()
        return subscriptionRepository.activateLicense(cleanCode)
    }
}
