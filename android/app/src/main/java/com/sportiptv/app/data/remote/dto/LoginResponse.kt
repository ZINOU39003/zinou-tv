package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class LoginResponse(
    val token: String,
    val token_type: String,
    val expires_in: Long,
    val user: UserDto
)

@Serializable
data class UserDto(
    val id: Long,
    val name: String,
    val email: String,
    val role: String
)
