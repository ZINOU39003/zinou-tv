package com.sportiptv.app.data.local.db.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "favorites")
data class FavoriteEntity(
    @PrimaryKey val id: Long, // Use channel ID as primary key since favorite list is unique per user session
    val name: String,
    val nameAr: String?,
    val logoUrl: String?,
    val streamUrl: String,
    val streamType: String,
    val quality: String
)
