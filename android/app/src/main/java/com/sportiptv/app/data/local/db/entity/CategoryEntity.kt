package com.sportiptv.app.data.local.db.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "categories")
data class CategoryEntity(
    @PrimaryKey val id: Long,
    val name: String,
    val nameAr: String,
    val slug: String,
    val icon: String?,
    val type: String,
    val sortOrder: Int,
    val isActive: Boolean,
    val channelsCount: Int
)
