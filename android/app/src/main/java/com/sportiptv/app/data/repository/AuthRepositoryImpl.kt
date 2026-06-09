package com.sportiptv.app.data.repository

import com.sportiptv.app.data.local.prefs.SecurePreferences
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.dto.LoginRequest
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.User
import com.sportiptv.app.domain.repository.AuthRepository
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import kotlinx.coroutines.flow.flowOn
import javax.inject.Inject

class AuthRepositoryImpl @Inject constructor(
    private val sportApi: SportApi,
    private val securePreferences: SecurePreferences
) : AuthRepository {

    override fun login(email: String, password: String): Flow<Resource<User>> = flow {
        emit(Resource.Loading)
        try {
            val deviceId = securePreferences.getDeviceId() ?: "unknown_device"
            val request = LoginRequest(
                email = email,
                password = password,
                device_id = deviceId,
                device_name = android.os.Build.DEVICE,
                device_model = android.os.Build.MODEL,
                android_version = android.os.Build.VERSION.RELEASE,
                app_version = "1.0.0"
            )
            
            val response = sportApi.login(request)
            if (response.isSuccessful) {
                val apiResponse = response.body()
                if (apiResponse != null && apiResponse.success && apiResponse.data != null) {
                    val loginResponse = apiResponse.data
                    
                    // Save JWT auth token to secure preferences
                    securePreferences.saveToken(loginResponse.token)
                    
                    val userDto = loginResponse.user
                    emit(Resource.Success(User(
                        id = userDto.id,
                        name = userDto.name,
                        email = userDto.email,
                        role = userDto.role
                    )))
                } else {
                    emit(Resource.Error(apiResponse?.message ?: "Login failed. Invalid response."))
                }
            } else {
                emit(Resource.Error("Invalid credentials or deactivated account."))
            }
        } catch (e: Exception) {
            emit(Resource.Error("Network error: ${e.localizedMessage ?: "Connection timed out"}"))
        }
    }.flowOn(Dispatchers.IO)

    override fun logout(): Flow<Resource<Unit>> = flow {
        emit(Resource.Loading)
        try {
            sportApi.logout()
            securePreferences.clear()
            emit(Resource.Success(Unit))
        } catch (e: Exception) {
            // Force clear tokens locally anyway
            securePreferences.clear()
            emit(Resource.Success(Unit))
        }
    }.flowOn(Dispatchers.IO)

    override fun checkLocalSession(): Flow<Resource<User?>> = flow {
        emit(Resource.Loading)
        val token = securePreferences.getToken()
        if (token.isNullOrEmpty()) {
            emit(Resource.Success(null))
        } else {
            try {
                val response = sportApi.me()
                if (response.isSuccessful && response.body()?.success == true) {
                    val userDto = response.body()?.data
                    if (userDto != null) {
                        emit(Resource.Success(User(
                            id = userDto.id,
                            name = userDto.name,
                            email = userDto.email,
                            role = userDto.role
                        )))
                    } else {
                        emit(Resource.Success(null))
                    }
                } else {
                    securePreferences.clear()
                    emit(Resource.Success(null))
                }
            } catch (e: Exception) {
                // If network fails but token exists, we can still load a mock session or return network error
                emit(Resource.Error("Network verification failed."))
            }
        }
    }.flowOn(Dispatchers.IO)

    override fun isUserLoggedIn(): Boolean {
        return !securePreferences.getToken().isNullOrEmpty()
    }
}
