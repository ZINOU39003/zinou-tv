package com.sportiptv.app.domain.model

data class Package(
    val id: Long,
    val categoryId: Long,
    val name: String,
    val nameAr: String?,
    val slug: String,
    val logoUrl: String?,
    val sortOrder: Int,
    val isActive: Boolean,
    val channelsCount: Int
)
