package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class ActivateResponse(
    val subscription: SubscriptionDto,
    val user: UserDto? = null,
    val token: String? = null,
    val token_type: String? = null,
    val expires_in: Long? = null
)
