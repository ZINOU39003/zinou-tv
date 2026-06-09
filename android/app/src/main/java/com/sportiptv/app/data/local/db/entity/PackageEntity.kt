package com.sportiptv.app.data.local.db.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "packages")
data class PackageEntity(
    @PrimaryKey val id: Long,
    val categoryId: Long,
    val name: String,
    val nameAr: String?,
    val slug: String,
    val logoUrl: String?,
    val sortOrder: Int,
    val isActive: Boolean,
    val channelsCount: Int
)
