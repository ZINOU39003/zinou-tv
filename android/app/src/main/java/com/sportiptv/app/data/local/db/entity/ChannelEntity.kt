package com.sportiptv.app.data.local.db.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "channels")
data class ChannelEntity(
    @PrimaryKey val id: Long,
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
    val serversJson: String? = null,
    val drmLicenseUrl: String? = null,
    val drmHeaders: String? = null
)
