package com.sportiptv.app.data.local.db

import androidx.room.Database
import androidx.room.RoomDatabase
import com.sportiptv.app.data.local.db.dao.CategoryDao
import com.sportiptv.app.data.local.db.dao.ChannelDao
import com.sportiptv.app.data.local.db.dao.FavoriteDao
import com.sportiptv.app.data.local.db.dao.PackageDao
import com.sportiptv.app.data.local.db.dao.StreamLogDao
import com.sportiptv.app.data.local.db.entity.CategoryEntity
import com.sportiptv.app.data.local.db.entity.ChannelEntity
import com.sportiptv.app.data.local.db.entity.FavoriteEntity
import com.sportiptv.app.data.local.db.entity.PackageEntity
import com.sportiptv.app.data.local.db.entity.StreamLogEntity

@Database(
    entities = [
        ChannelEntity::class,
        CategoryEntity::class,
        FavoriteEntity::class,
        PackageEntity::class,
        StreamLogEntity::class
    ],
    version = 7,
    exportSchema = false
)
abstract class SportDatabase : RoomDatabase() {

    abstract fun channelDao(): ChannelDao
    abstract fun categoryDao(): CategoryDao
    abstract fun favoriteDao(): FavoriteDao
    abstract fun packageDao(): PackageDao
    abstract fun streamLogDao(): StreamLogDao
}
