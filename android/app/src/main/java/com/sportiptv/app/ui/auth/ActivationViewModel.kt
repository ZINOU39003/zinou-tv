package com.sportiptv.app.ui.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.Subscription
import com.sportiptv.app.domain.usecase.ActivateLicenseUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableSharedFlow
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.SharedFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asSharedFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ActivationViewModel @Inject constructor(
    private val activateLicenseUseCase: ActivateLicenseUseCase
) : ViewModel() {

    private val _code = MutableStateFlow("")
    val code: StateFlow<String> = _code.asStateFlow()

    private val _activationState = MutableStateFlow<ActivationState>(ActivationState.Idle)
    val activationState: StateFlow<ActivationState> = _activationState.asStateFlow()

    private val _eventFlow = MutableSharedFlow<ActivationEvent>()
    val eventFlow: SharedFlow<ActivationEvent> = _eventFlow.asSharedFlow()

    fun onCodeChange(value: String) {
        // Strip out non-alphanumeric characters and automatically format code format: ABCD-1234-EFGH-5678
        val cleaned = value.replace("[^A-Za-z0-9]".toRegex(), "").uppercase()
        val builder = StringBuilder()
        for (i in cleaned.indices) {
            if (i > 0 && i % 4 == 0) {
                builder.append("-")
            }
            builder.append(cleaned[i])
        }
        val formatted = builder.toString().take(19) // XXXX-XXXX-XXXX-XXXX = 19 chars maximum
        _code.value = formatted
    }

    fun activate() {
        viewModelScope.launch {
            activateLicenseUseCase(_code.value).collect { resource ->
                when (resource) {
                    is Resource.Loading -> {
                        _activationState.value = ActivationState.Loading
                    }
                    is Resource.Success -> {
                        _activationState.value = ActivationState.Success
                        _eventFlow.emit(ActivationEvent.ActivationSuccess)
                    }
                    is Resource.Error -> {
                        _activationState.value = ActivationState.Error(resource.message)
                        _eventFlow.emit(ActivationEvent.ShowError(resource.message))
                    }
                    is Resource.Idle -> {
                        _activationState.value = ActivationState.Idle
                    }

                }
            }
        }
    }

    sealed class ActivationState {
        object Idle : ActivationState()
        object Loading : ActivationState()
        object Success : ActivationState()
        data class Error(val message: String) : ActivationState()
    }

    sealed class ActivationEvent {
        object ActivationSuccess : ActivationEvent()
        data class ShowError(val message: String) : ActivationEvent()
    }
}
