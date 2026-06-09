package com.sportiptv.app.ui.settings

import android.content.Context
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.AuthRepository
import com.sportiptv.app.domain.repository.ChannelRepository
import com.sportiptv.app.util.DeviceUtils
import dagger.hilt.android.lifecycle.HiltViewModel
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.flow.MutableSharedFlow
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.SharedFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asSharedFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class SettingsViewModel @Inject constructor(
    private val authRepository: AuthRepository,
    private val channelRepository: ChannelRepository,
    @ApplicationContext private val context: Context
) : ViewModel() {

    private val _clearCacheState = MutableStateFlow<Resource<Unit>>(Resource.Idle)
    val clearCacheState: StateFlow<Resource<Unit>> = _clearCacheState.asStateFlow()

    private val _eventFlow = MutableSharedFlow<SettingsEvent>()
    val eventFlow: SharedFlow<SettingsEvent> = _eventFlow.asSharedFlow()

    fun getDeviceIdSignature(): String {
        return DeviceUtils.getDeviceId(context)
    }

    fun clearCache() {
        viewModelScope.launch {
            channelRepository.clearCache().collect { result ->
                _clearCacheState.value = result
                if (result is Resource.Success) {
                    _eventFlow.emit(SettingsEvent.ShowToast("Local database cache cleared!"))
                }
            }
        }
    }

    fun logout() {
        viewModelScope.launch {
            authRepository.logout().collect { result ->
                if (result is Resource.Success) {
                    _eventFlow.emit(SettingsEvent.LogoutSuccess)
                }
            }
        }
    }

    sealed class SettingsEvent {
        object LogoutSuccess : SettingsEvent()
        data class ShowToast(val message: String) : SettingsEvent()
    }
}
