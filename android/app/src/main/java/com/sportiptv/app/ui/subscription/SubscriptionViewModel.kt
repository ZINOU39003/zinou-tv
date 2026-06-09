package com.sportiptv.app.ui.subscription

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.Subscription
import com.sportiptv.app.domain.repository.SubscriptionRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class SubscriptionViewModel @Inject constructor(
    private val subscriptionRepository: SubscriptionRepository
) : ViewModel() {

    private val _subscriptionState = MutableStateFlow<Resource<Subscription>>(Resource.Idle)
    val subscriptionState: StateFlow<Resource<Subscription>> = _subscriptionState.asStateFlow()

    private val _appConfigState = MutableStateFlow<Resource<com.sportiptv.app.data.remote.dto.AppConfigDto>>(Resource.Idle)
    val appConfigState: StateFlow<Resource<com.sportiptv.app.data.remote.dto.AppConfigDto>> = _appConfigState.asStateFlow()

    init {
        loadSubscriptionDetails()
        loadAppConfig()
    }

    fun loadAppConfig() {
        viewModelScope.launch {
            subscriptionRepository.getAppConfig().collect { result ->
                _appConfigState.value = result
            }
        }
    }

    fun loadSubscriptionDetails() {
        viewModelScope.launch {
            subscriptionRepository.getSubscriptionDetails().collect { result ->
                _subscriptionState.value = result
            }
        }
    }
}
