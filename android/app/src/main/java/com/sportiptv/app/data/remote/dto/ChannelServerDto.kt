package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.Serializable

@Serializable
data class ChannelServerDto(
    val id: Long,
    val name: String,
    val stream_url: String,
    val stream_type: String,
    val quality: String
)
