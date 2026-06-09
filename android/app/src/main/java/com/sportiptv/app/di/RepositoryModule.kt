package com.sportiptv.app.di

import com.sportiptv.app.data.repository.AuthRepositoryImpl
import com.sportiptv.app.data.repository.ChannelRepositoryImpl
import com.sportiptv.app.data.repository.FavoriteRepositoryImpl
import com.sportiptv.app.data.repository.SubscriptionRepositoryImpl
import com.sportiptv.app.domain.repository.AuthRepository
import com.sportiptv.app.domain.repository.ChannelRepository
import com.sportiptv.app.domain.repository.FavoriteRepository
import com.sportiptv.app.domain.repository.SubscriptionRepository
import dagger.Binds
import dagger.Module
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
abstract class RepositoryModule {

    @Binds
    @Singleton
    abstract fun bindAuthRepository(
        authRepositoryImpl: AuthRepositoryImpl
    ): AuthRepository

    @Binds
    @Singleton
    abstract fun bindChannelRepository(
        channelRepositoryImpl: ChannelRepositoryImpl
    ): ChannelRepository

    @Binds
    @Singleton
    abstract fun bindFavoriteRepository(
        favoriteRepositoryImpl: FavoriteRepositoryImpl
    ): FavoriteRepository

    @Binds
    @Singleton
    abstract fun bindSubscriptionRepository(
        subscriptionRepositoryImpl: SubscriptionRepositoryImpl
    ): SubscriptionRepository
}
