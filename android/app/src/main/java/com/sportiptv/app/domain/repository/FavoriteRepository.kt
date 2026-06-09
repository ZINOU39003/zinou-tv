package com.sportiptv.app.domain.repository

import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.model.Resource
import kotlinx.coroutines.flow.Flow

interface FavoriteRepository {
    fun getFavorites(): Flow<List<Channel>>
    fun toggleFavorite(channelId: Long, isFavorite: Boolean): Flow<Resource<Unit>>
    fun isChannelFavorited(channelId: Long): Flow<Boolean>
    fun syncFavorites(): Flow<Resource<Unit>>
}
