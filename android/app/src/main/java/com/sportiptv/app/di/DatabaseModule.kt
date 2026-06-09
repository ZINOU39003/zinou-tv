package com.sportiptv.app.di

import android.content.Context
import androidx.room.Room
import com.sportiptv.app.data.local.db.SportDatabase
import com.sportiptv.app.data.local.db.dao.CategoryDao
import com.sportiptv.app.data.local.db.dao.ChannelDao
import com.sportiptv.app.data.local.db.dao.FavoriteDao
import com.sportiptv.app.data.local.db.dao.PackageDao
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.android.qualifiers.ApplicationContext
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object DatabaseModule {

    @Provides
    @Singleton
    fun provideDatabase(@ApplicationContext context: Context): SportDatabase {
        return Room.databaseBuilder(
            context,
            SportDatabase::class.java,
            "sport_iptv.db"
        ).fallbackToDestructiveMigration()
         .build()
    }

    @Provides
    fun provideChannelDao(database: SportDatabase): ChannelDao {
        return database.channelDao()
    }

    @Provides
    fun provideCategoryDao(database: SportDatabase): CategoryDao {
        return database.categoryDao()
    }

    @Provides
    fun provideFavoriteDao(database: SportDatabase): FavoriteDao {
        return database.favoriteDao()
    }

    @Provides
    fun providePackageDao(database: SportDatabase): PackageDao {
        return database.packageDao()
    }
}
