package com.sportiptv.app.domain.usecase

import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.model.User
import com.sportiptv.app.domain.repository.AuthRepository
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import javax.inject.Inject

class LoginUseCase @Inject constructor(
    private val authRepository: AuthRepository
) {
    operator fun invoke(email: String, password: String): Flow<Resource<User>> {
        if (email.isBlank() || password.isBlank()) {
            return flow { emit(Resource.Error("Email and Password cannot be empty.")) }
        }
        return authRepository.login(email, password)
    }
}
