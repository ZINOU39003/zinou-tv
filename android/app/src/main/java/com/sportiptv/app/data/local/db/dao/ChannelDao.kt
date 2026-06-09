package com.sportiptv.app.data.local.db.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.sportiptv.app.data.local.db.entity.ChannelEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface ChannelDao {

    @Query("SELECT * FROM channels WHERE isActive = 1 ORDER BY sortOrder ASC")
    fun getAllChannels(): Flow<List<ChannelEntity>>

    @Query("SELECT * FROM channels WHERE categoryId = :categoryId AND isActive = 1 ORDER BY sortOrder ASC")
    fun getChannelsByCategory(categoryId: Long): Flow<List<ChannelEntity>>

    @Query("SELECT * FROM channels WHERE packageId = :packageId AND isActive = 1 ORDER BY sortOrder ASC")
    fun getChannelsByPackage(packageId: Long): Flow<List<ChannelEntity>>

    @Query("""
        SELECT * FROM channels
        WHERE isActive = 1
          AND (packageId = :packageId OR (packageId IS NULL AND categoryId = :categoryId))
        ORDER BY sortOrder ASC
    """)
    fun getChannelsByPackageWithFallback(packageId: Long, categoryId: Long): Flow<List<ChannelEntity>>

    @Query("SELECT * FROM channels WHERE isActive = 1 AND (name LIKE '%' || :query || '%' OR nameAr LIKE '%' || :query || '%') ORDER BY sortOrder ASC")
    fun searchChannels(query: String): Flow<List<ChannelEntity>>

    @Query("SELECT * FROM channels WHERE id = :id LIMIT 1")
    suspend fun getChannelById(id: Long): ChannelEntity?

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertChannels(channels: List<ChannelEntity>)

    @Query("DELETE FROM channels")
    suspend fun clearChannels()
}
