package com.sportiptv.app.domain.repository

import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.User
import kotlinx.coroutines.flow.Flow

interface AuthRepository {
    fun login(email: String, password: String): Flow<Resource<User>>
    fun logout(): Flow<Resource<Unit>>
    fun checkLocalSession(): Flow<Resource<User?>>
    fun isUserLoggedIn(): Boolean
}
