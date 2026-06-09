package com.sportiptv.app.ui.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.usecase.LoginUseCase
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
class LoginViewModel @Inject constructor(
    private val loginUseCase: LoginUseCase
) : ViewModel() {

    private val _email = MutableStateFlow("")
    val email: StateFlow<String> = _email.asStateFlow()

    private val _password = MutableStateFlow("")
    val password: StateFlow<String> = _password.asStateFlow()

    private val _loginState = MutableStateFlow<LoginState>(LoginState.Idle)
    val loginState: StateFlow<LoginState> = _loginState.asStateFlow()

    private val _eventFlow = MutableSharedFlow<LoginEvent>()
    val eventFlow: SharedFlow<LoginEvent> = _eventFlow.asSharedFlow()

    fun onEmailChange(value: String) {
        _email.value = value
    }

    fun onPasswordChange(value: String) {
        _password.value = value
    }

    fun login() {
        viewModelScope.launch {
            loginUseCase(_email.value, _password.value).collect { resource ->
                when (resource) {
                    is Resource.Loading -> {
                        _loginState.value = LoginState.Loading
                    }
                    is Resource.Success -> {
                        _loginState.value = LoginState.Success
                        _eventFlow.emit(LoginEvent.LoginSuccess)
                    }
                    is Resource.Error -> {
                        _loginState.value = LoginState.Error(resource.message)
                        _eventFlow.emit(LoginEvent.ShowError(resource.message))
                    }
                    is Resource.Idle -> {
                        _loginState.value = LoginState.Idle
                    }

                }
            }
        }
    }

    sealed class LoginState {
        object Idle : LoginState()
        object Loading : LoginState()
        object Success : LoginState()
        data class Error(val message: String) : LoginState()
    }

    sealed class LoginEvent {
        object LoginSuccess : LoginEvent()
        data class ShowError(val message: String) : LoginEvent()
    }
}
