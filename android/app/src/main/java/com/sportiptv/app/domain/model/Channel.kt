package com.sportiptv.app.domain.model

data class Channel(
    val id: Long,
    val name: String,
    val nameAr: String?,
    val categoryId: Long,
    val categoryName: String?,
    val categoryNameAr: String?,
    val packageId: Long? = null,
    val packageName: String? = null,
    val packageNameAr: String? = null,
    val logoUrl: String?,
    val streamUrl: String,
    val streamType: String,
    val quality: String,
    val backupUrl: String?,
    val sortOrder: Int,
    val isActive: Boolean,
    val country: String? = null,
    val language: String? = null,
    val continent: String? = null,
    val isFavorited: Boolean = false,
    val drmLicenseUrl: String? = null,
    val drmHeaders: String? = null,
    val servers: List<ChannelServer> = emptyList()
)
