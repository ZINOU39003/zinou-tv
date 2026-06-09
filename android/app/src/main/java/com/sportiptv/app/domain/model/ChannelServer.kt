package com.sportiptv.app.domain.model

data class ChannelServer(
    val id: Long,
    val name: String,
    val streamUrl: String,
    val streamType: String,
    val quality: String
)
