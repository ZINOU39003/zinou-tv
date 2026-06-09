package com.sportiptv.app.data.repository

import com.sportiptv.app.data.local.db.dao.ChannelDao
import com.sportiptv.app.data.local.db.dao.FavoriteDao
import com.sportiptv.app.data.local.db.entity.ChannelEntity
import com.sportiptv.app.data.local.db.entity.FavoriteEntity
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.FavoriteRepository
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.flow.flow
import kotlinx.coroutines.flow.flowOn
import kotlinx.coroutines.flow.map
import javax.inject.Inject

class FavoriteRepositoryImpl @Inject constructor(
    private val sportApi: SportApi,
    private val favoriteDao: FavoriteDao,
    private val channelDao: ChannelDao
) : FavoriteRepository {

    override fun getFavorites(): Flow<List<Channel>> {
        return favoriteDao.getAllFavorites().map { list ->
            list.map { entity ->
                Channel(
                    id = entity.id,
                    name = entity.name,
                    nameAr = entity.nameAr,
                    categoryId = 0,
                    categoryName = null,
                    categoryNameAr = null,
                    logoUrl = entity.logoUrl,
                    streamUrl = entity.streamUrl,
                    streamType = entity.streamType,
                    quality = entity.quality,
                    backupUrl = null,
                    sortOrder = 0,
                    isActive = true,
                    isFavorited = true
                )
            }
        }.flowOn(Dispatchers.IO)
    }

    override fun toggleFavorite(channelId: Long, isFavorite: Boolean): Flow<Resource<Unit>> = flow {
        emit(Resource.Loading)
        try {
            if (isFavorite) {
                var channel = channelDao.getChannelById(channelId)
                if (channel == null) {
                    channel = fetchAndCacheChannel(channelId)
                }
                if (channel != null) {
                    favoriteDao.insertFavorite(
                        FavoriteEntity(
                            id = channel.id,
                            name = channel.name,
                            nameAr = channel.nameAr,
                            logoUrl = channel.logoUrl,
                            streamUrl = channel.streamUrl,
                            streamType = channel.streamType,
                            quality = channel.quality
                        )
                    )
                } else {
                    emit(Resource.Error("Channel not found"))
                    return@flow
                }
            } else {
                favoriteDao.deleteByChannelId(channelId)
            }

            try {
                if (isFavorite) sportApi.addToFavorites(channelId)
                else sportApi.removeFromFavorites(channelId)
            } catch (e: Exception) {
                e.printStackTrace()
            }

            emit(Resource.Success(Unit))
        } catch (e: Exception) {
            emit(Resource.Error("Local DB error: ${e.localizedMessage}"))
        }
    }.flowOn(Dispatchers.IO)

    override fun isChannelFavorited(channelId: Long): Flow<Boolean> {
        return favoriteDao.isChannelFavorited(channelId).flowOn(Dispatchers.IO)
    }

    override fun syncFavorites(): Flow<Resource<Unit>> = flow {
        emit(Resource.Loading)
        try {
            val response = sportApi.getFavorites()
            if (response.isSuccessful && response.body()?.success == true) {
                val list = response.body()?.data ?: emptyList()
                list.forEach { dto ->
                    favoriteDao.insertFavorite(
                        FavoriteEntity(
                            id = dto.id,
                            name = dto.name,
                            nameAr = dto.name_ar,
                            logoUrl = dto.logo_url,
                            streamUrl = dto.stream_url,
                            streamType = dto.stream_type,
                            quality = dto.quality
                        )
                    )
                }
                emit(Resource.Success(Unit))
            } else {
                emit(Resource.Error("Failed to fetch remote favorites."))
            }
        } catch (e: Exception) {
            emit(Resource.Error("Favorites sync failed: ${e.localizedMessage}"))
        }
    }.flowOn(Dispatchers.IO)

    private suspend fun fetchAndCacheChannel(channelId: Long): ChannelEntity? {
        return try {
            val response = sportApi.getChannelDetails(channelId)
            if (response.isSuccessful && response.body()?.success == true) {
                val dto = response.body()?.data ?: return null
                val entity = ChannelEntity(
                    id = dto.id,
                    name = dto.name,
                    nameAr = dto.name_ar,
                    categoryId = dto.category_id,
                    categoryName = dto.category_name,
                    categoryNameAr = dto.category_name_ar,
                    packageId = dto.package_id,
                    packageName = dto.package_name,
                    packageNameAr = dto.package_name_ar,
                    logoUrl = dto.logo_url,
                    streamUrl = dto.stream_url,
                    streamType = dto.stream_type,
                    quality = dto.quality,
                    backupUrl = dto.backup_url,
                    sortOrder = dto.sort_order,
                    isActive = dto.is_active,
                    country = dto.country,
                    language = dto.language,
                    continent = dto.continent,
                    serversJson = null,
                    drmLicenseUrl = dto.drm_license_url,
                    drmHeaders = dto.drm_headers
                )
                channelDao.insertChannels(listOf(entity))
                entity
            } else null
        } catch (_: Exception) {
            null
        }
    }
}
