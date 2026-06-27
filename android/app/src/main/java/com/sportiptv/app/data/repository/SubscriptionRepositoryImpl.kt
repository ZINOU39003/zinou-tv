package com.sportiptv.app.data.repository

import com.sportiptv.app.data.local.prefs.SecurePreferences
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.dto.ActivateRequest
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.Subscription
import com.sportiptv.app.domain.repository.SubscriptionRepository
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import kotlinx.coroutines.flow.flowOn
import javax.inject.Inject

class SubscriptionRepositoryImpl @Inject constructor(
    private val sportApi: SportApi,
    private val securePreferences: SecurePreferences
) : SubscriptionRepository {

    override fun activateLicense(code: String): Flow<Resource<Subscription>> = flow {
        emit(Resource.Loading)
        try {
            val deviceId = securePreferences.getDeviceId() ?: "unknown_device"
            val request = ActivateRequest(
                code = code,
                device_id = deviceId,
                device_name = android.os.Build.DEVICE,
                device_model = android.os.Build.MODEL,
                android_version = android.os.Build.VERSION.RELEASE,
                app_version = "1.0.0"
            )

            val response = sportApi.activateLicense(request)
            if (response.isSuccessful && response.body()?.success == true) {
                val actData = response.body()?.data
                if (actData != null) {
                    // Save the authenticated JWT token if returned immediately
                    actData.token?.let { securePreferences.saveToken(it) }
                    
                    val subDto = actData.subscription
                    emit(Resource.Success(
                        Subscription(
                            code = subDto.code,
                            duration = subDto.duration,
                            status = subDto.status,
                            activatedAt = subDto.activated_at,
                            expiresAt = subDto.expires_at,
                            daysRemaining = subDto.days_remaining,
                            boundDeviceId = subDto.device?.device_id,
                            boundDeviceName = subDto.device?.device_name
                        )
                    ))
                } else {
                    emit(Resource.Error("Invalid response data."))
                }
            } else {
                val errorBody = response.errorBody()?.string()
                var errorMessage = "Failed to activate license."
                if (errorBody != null) {
                    try {
                        val json = org.json.JSONObject(errorBody)
                        if (json.has("message")) {
                            errorMessage = json.getString("message")
                        } else if (json.has("error")) {
                            errorMessage = json.getString("error")
                        }
                    } catch (e: Exception) {
                        // Ignore parse errors
                    }
                }
                emit(Resource.Error(errorMessage))
            }
        } catch (e: Exception) {
            emit(Resource.Error("Network error: ${e.localizedMessage ?: "Connection timed out"}"))
        }
    }.flowOn(Dispatchers.IO)

    override fun validateLicense(): Flow<Resource<Subscription>> = flow {
        emit(Resource.Loading)
        try {
            val response = sportApi.validateLicense()
            if (response.isSuccessful && response.body()?.success == true) {
                val subDto = response.body()?.data
                if (subDto != null) {
                    emit(Resource.Success(
                        Subscription(
                            code = subDto.code,
                            duration = subDto.duration,
                            status = subDto.status,
                            activatedAt = subDto.activated_at,
                            expiresAt = subDto.expires_at,
                            daysRemaining = subDto.days_remaining,
                            boundDeviceId = subDto.device?.device_id,
                            boundDeviceName = subDto.device?.device_name
                        )
                    ))
                } else {
                    emit(Resource.Error("Invalid response data."))
                }
            } else {
                emit(Resource.Error(response.body()?.message ?: "License validation failed."))
            }
        } catch (e: Exception) {
            emit(Resource.Error("Connection error: ${e.localizedMessage}"))
        }
    }.flowOn(Dispatchers.IO)

    override fun getSubscriptionDetails(): Flow<Resource<Subscription>> = flow {
        emit(Resource.Loading)
        try {
            val response = sportApi.getSubscription()
            if (response.isSuccessful && response.body()?.success == true) {
                val subDto = response.body()?.data
                if (subDto != null) {
                    emit(Resource.Success(
                        Subscription(
                            code = subDto.code,
                            duration = subDto.duration,
                            status = subDto.status,
                            activatedAt = subDto.activated_at,
                            expiresAt = subDto.expires_at,
                            daysRemaining = subDto.days_remaining,
                            boundDeviceId = subDto.device?.device_id,
                            boundDeviceName = subDto.device?.device_name
                        )
                    ))
                } else {
                    emit(Resource.Error("Subscription details empty."))
                }
            } else {
                emit(Resource.Error(response.body()?.message ?: "Failed to get subscription details."))
            }
        } catch (e: Exception) {
            emit(Resource.Error("Connection error: ${e.localizedMessage}"))
        }
    }.flowOn(Dispatchers.IO)

    override fun getAppConfig(): Flow<Resource<com.sportiptv.app.data.remote.dto.AppConfigDto>> = flow {
        emit(Resource.Loading)
        try {
            val response = sportApi.getAppConfig()
            if (response.isSuccessful && response.body()?.success == true) {
                val configDto = response.body()?.data
                if (configDto != null) {
                    emit(Resource.Success(configDto))
                } else {
                    emit(Resource.Error("Configuration data empty."))
                }
            } else {
                emit(Resource.Error(response.body()?.message ?: "Failed to get app configuration."))
            }
        } catch (e: Exception) {
            emit(Resource.Error("Connection error: ${e.localizedMessage}"))
        }
    }.flowOn(Dispatchers.IO)
}
