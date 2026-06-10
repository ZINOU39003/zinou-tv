package com.sportiptv.app.ui.config

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.SubscriptionRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class AppConfigViewModel @Inject constructor(
    private val subscriptionRepository: SubscriptionRepository
) : ViewModel() {

    private val _configState = MutableStateFlow<Resource<com.sportiptv.app.data.remote.dto.AppConfigDto>>(Resource.Idle)
    val configState: StateFlow<Resource<com.sportiptv.app.data.remote.dto.AppConfigDto>> = _configState.asStateFlow()

    init {
        loadConfig()
    }

    fun loadConfig() {
        viewModelScope.launch {
            subscriptionRepository.getAppConfig().collect { _configState.value = it }
        }
    }
}
