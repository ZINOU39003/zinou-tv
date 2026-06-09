package com.sportiptv.app.domain.model

data class Category(
    val id: Long,
    val name: String,
    val nameAr: String,
    val slug: String,
    val icon: String?,
    val type: String,
    val sortOrder: Int,
    val isActive: Boolean,
    val channelsCount: Int
)
