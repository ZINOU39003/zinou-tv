package com.sportiptv.app.data.remote.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class FavoriteDto(
    @SerialName("id") val id: Int = 0,
    @SerialName("channel_id") val channelId: Int = 0,
    @SerialName("channel") val channel: ChannelDto? = null,
    @SerialName("created_at") val createdAt: String = ""
)
