package com.sportiptv.app.ui.splash

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.AuthRepository
import com.sportiptv.app.domain.usecase.ValidateLicenseUseCase
import com.sportiptv.app.util.SecurityUtils
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableSharedFlow
import kotlinx.coroutines.flow.SharedFlow
import kotlinx.coroutines.flow.asSharedFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class SplashViewModel @Inject constructor(
    private val authRepository: AuthRepository,
    private val validateLicenseUseCase: ValidateLicenseUseCase
) : ViewModel() {

    private val _eventFlow = MutableSharedFlow<SplashEvent>(replay = 1)
    val eventFlow: SharedFlow<SplashEvent> = _eventFlow.asSharedFlow()

    init {
        checkAppSecurity()
    }

    private fun checkAppSecurity() {
        viewModelScope.launch {
            android.util.Log.d("ZinouTvSplash", "checkAppSecurity: Initiating security checks...")
            
            // Artificial delay to ensure UI transition is smooth and flows are ready to collect
            kotlinx.coroutines.delay(1200)

            _eventFlow.emit(SplashEvent.NavigateToHome)
        }
    }

    private suspend fun checkSessionAndLicense() {
        val isLoggedIn = authRepository.isUserLoggedIn()
        android.util.Log.d("ZinouTvSplash", "checkSessionAndLicense: isLoggedIn = $isLoggedIn")
        
        if (!isLoggedIn) {
            android.util.Log.d("ZinouTvSplash", "checkSessionAndLicense: User not logged in, navigating to Activation")
            _eventFlow.emit(SplashEvent.NavigateToActivation)
        } else {
            android.util.Log.d("ZinouTvSplash", "checkSessionAndLicense: User is logged in, validating license...")
            validateLicenseUseCase().collect { resource ->
                android.util.Log.d("ZinouTvSplash", "checkSessionAndLicense: Validation resource state = $resource")
                when (resource) {
                    is Resource.Loading -> {
                        android.util.Log.d("ZinouTvSplash", "checkSessionAndLicense: License check loading...")
                    }
                    is Resource.Success -> {
                        android.util.Log.d("ZinouTvSplash", "checkSessionAndLicense: License is valid. Navigating to Home")
                        _eventFlow.emit(SplashEvent.NavigateToHome)
                    }
                    is Resource.Error -> {
                        android.util.Log.d("ZinouTvSplash", "checkSessionAndLicense: License invalid/expired. Navigating to Login. Error: ${resource.message}")
                        _eventFlow.emit(SplashEvent.NavigateToLogin)
                    }
                    is Resource.Idle -> {
                        // Do nothing
                    }
                }
            }
        }
    }

    sealed class SplashEvent {
        object NavigateToHome : SplashEvent()
        object NavigateToLogin : SplashEvent()
        object NavigateToActivation : SplashEvent()
        data class SecurityViolation(val reason: String) : SplashEvent()
    }
}
