package com.sportiptv.app.domain.repository

import com.sportiptv.app.domain.model.Category
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.model.Resource
import kotlinx.coroutines.flow.Flow

import com.sportiptv.app.domain.model.Package

interface ChannelRepository {
    fun getCategories(): Flow<List<Category>>
    fun getPackages(categoryId: Long? = null): Flow<List<Package>>
    fun getChannels(categoryId: Long? = null, packageId: Long? = null): Flow<List<Channel>>
    fun searchChannels(query: String): Flow<List<Channel>>
    fun getChannelDetails(id: Long): Flow<Resource<Channel>>
    fun syncContent(): Flow<Resource<Unit>>
    fun clearCache(): Flow<Resource<Unit>>
}
