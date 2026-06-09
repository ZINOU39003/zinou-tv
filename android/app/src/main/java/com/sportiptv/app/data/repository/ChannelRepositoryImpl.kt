package com.sportiptv.app.data.repository

import com.sportiptv.app.data.local.db.dao.CategoryDao
import com.sportiptv.app.data.local.db.dao.ChannelDao
import com.sportiptv.app.data.local.db.dao.FavoriteDao
import com.sportiptv.app.data.local.db.dao.PackageDao
import com.sportiptv.app.data.local.db.entity.CategoryEntity
import com.sportiptv.app.data.local.db.entity.PackageEntity
import com.sportiptv.app.data.local.db.entity.ChannelEntity
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.domain.model.Category
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.model.Package
import com.sportiptv.app.domain.model.ChannelServer
import com.sportiptv.app.data.remote.dto.ChannelServerDto
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.ChannelRepository
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.combine
import kotlinx.coroutines.flow.flow
import kotlinx.coroutines.flow.flowOn
import kotlinx.coroutines.flow.map
import kotlinx.coroutines.flow.first
import kotlinx.serialization.json.Json
import kotlinx.serialization.encodeToString
import javax.inject.Inject

class ChannelRepositoryImpl @Inject constructor(
    private val sportApi: SportApi,
    private val channelDao: ChannelDao,
    private val categoryDao: CategoryDao,
    private val packageDao: PackageDao,
    private val favoriteDao: FavoriteDao,
    private val json: Json
) : ChannelRepository {

    override fun getCategories(): Flow<List<Category>> {
        return categoryDao.getAllCategories().map { list ->
            list.map { entity ->
                Category(
                    id = entity.id,
                    name = entity.name,
                    nameAr = entity.nameAr,
                    slug = entity.slug,
                    icon = entity.icon,
                    type = entity.type,
                    sortOrder = entity.sortOrder,
                    isActive = entity.isActive,
                    channelsCount = entity.channelsCount
                )
            }
        }.flowOn(Dispatchers.IO)
    }

    override fun getPackages(categoryId: Long?): Flow<List<Package>> {
        val packageFlow = if (categoryId != null) {
            packageDao.getPackagesByCategory(categoryId)
        } else {
            packageDao.getAllPackages()
        }
        
        return packageFlow.map { list ->
            list.map { entity ->
                Package(
                    id = entity.id,
                    categoryId = entity.categoryId,
                    name = entity.name,
                    nameAr = entity.nameAr,
                    slug = entity.slug,
                    logoUrl = entity.logoUrl,
                    sortOrder = entity.sortOrder,
                    isActive = entity.isActive,
                    channelsCount = entity.channelsCount
                )
            }
        }.flowOn(Dispatchers.IO)
    }

    override fun getChannels(categoryId: Long?, packageId: Long?): Flow<List<Channel>> {
        val channelFlow = when {
            packageId != null && categoryId != null -> {
                channelDao.getChannelsByPackageWithFallback(packageId, categoryId)
            }
            packageId != null -> channelDao.getChannelsByPackage(packageId)
            categoryId != null -> channelDao.getChannelsByCategory(categoryId)
            else -> channelDao.getAllChannels()
        }

        // Combine channels with favorites local table to set the boolean isFavorited flag dynamically
        return combine(channelFlow, favoriteDao.getAllFavorites()) { dbChannels, favList ->
            val favIds = favList.map { it.id }.toSet()
            dbChannels.map { entity ->
                val serversList = try {
                    entity.serversJson?.let {
                        json.decodeFromString<List<ChannelServerDto>>(it).map { dto ->
                            ChannelServer(
                                id = dto.id,
                                name = dto.name,
                                streamUrl = dto.stream_url,
                                streamType = dto.stream_type,
                                quality = dto.quality
                            )
                        }
                    } ?: emptyList()
                } catch (e: Exception) {
                    emptyList()
                }

                Channel(
                    id = entity.id,
                    name = entity.name,
                    nameAr = entity.nameAr,
                    categoryId = entity.categoryId,
                    categoryName = entity.categoryName,
                    categoryNameAr = entity.categoryNameAr,
                    packageId = entity.packageId,
                    packageName = entity.packageName,
                    packageNameAr = entity.packageNameAr,
                    logoUrl = entity.logoUrl,
                    streamUrl = entity.streamUrl,
                    streamType = entity.streamType,
                    quality = entity.quality,
                    backupUrl = entity.backupUrl,
                    sortOrder = entity.sortOrder,
                    isActive = entity.isActive,
                    country = entity.country,
                    language = entity.language,
                    continent = entity.continent,
                    isFavorited = favIds.contains(entity.id),
                    drmLicenseUrl = entity.drmLicenseUrl,
                    drmHeaders = entity.drmHeaders,
                    servers = serversList
                )
            }
        }.flowOn(Dispatchers.IO)
    }

    override fun searchChannels(query: String): Flow<List<Channel>> {
        return combine(channelDao.searchChannels(query), favoriteDao.getAllFavorites()) { dbChannels, favList ->
            val favIds = favList.map { it.id }.toSet()
            dbChannels.map { entity ->
                val serversList = try {
                    entity.serversJson?.let {
                        json.decodeFromString<List<ChannelServerDto>>(it).map { dto ->
                            ChannelServer(
                                id = dto.id,
                                name = dto.name,
                                streamUrl = dto.stream_url,
                                streamType = dto.stream_type,
                                quality = dto.quality
                            )
                        }
                    } ?: emptyList()
                } catch (e: Exception) {
                    emptyList()
                }

                Channel(
                    id = entity.id,
                    name = entity.name,
                    nameAr = entity.nameAr,
                    categoryId = entity.categoryId,
                    categoryName = entity.categoryName,
                    categoryNameAr = entity.categoryNameAr,
                    packageId = entity.packageId,
                    packageName = entity.packageName,
                    packageNameAr = entity.packageNameAr,
                    logoUrl = entity.logoUrl,
                    streamUrl = entity.streamUrl,
                    streamType = entity.streamType,
                    quality = entity.quality,
                    backupUrl = entity.backupUrl,
                    sortOrder = entity.sortOrder,
                    isActive = entity.isActive,
                    country = entity.country,
                    language = entity.language,
                    continent = entity.continent,
                    isFavorited = favIds.contains(entity.id),
                    drmLicenseUrl = entity.drmLicenseUrl,
                    drmHeaders = entity.drmHeaders,
                    servers = serversList
                )
            }
        }.flowOn(Dispatchers.IO)
    }

    override fun getChannelDetails(id: Long): Flow<Resource<Channel>> = flow {
        emit(Resource.Loading)
        var fetchSuccessful = false
        val isFav = try {
            favoriteDao.isChannelFavorited(id).first()
        } catch (e: Exception) {
            false
        }
        
        try {
            // 1. Always attempt to fetch the latest details from the remote API first
            val response = sportApi.getChannelDetails(id)
            if (response.isSuccessful && response.body()?.success == true) {
                val dto = response.body()?.data
                if (dto != null) {
                    val serversList = dto.servers.map { serverDto ->
                        ChannelServer(
                            id = serverDto.id,
                            name = serverDto.name,
                            streamUrl = serverDto.stream_url,
                            streamType = serverDto.stream_type,
                            quality = serverDto.quality
                        )
                    }
                    val channel = Channel(
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
                        isFavorited = isFav,
                        drmLicenseUrl = dto.drm_license_url,
                        drmHeaders = dto.drm_headers,
                        servers = serversList
                    )
                    
                    // Update the local database cache with the fresh details
                    val serJson = try {
                        json.encodeToString(dto.servers)
                    } catch (e: Exception) {
                        null
                    }
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
                        serversJson = serJson,
                        drmLicenseUrl = dto.drm_license_url,
                        drmHeaders = dto.drm_headers
                    )
                    channelDao.insertChannels(listOf(entity))
                    
                    emit(Resource.Success(channel))
                    fetchSuccessful = true
                }
            }
        } catch (e: Exception) {
            // Network or API failure, will fallback to local cache
            e.printStackTrace()
        }

        // 2. If remote fetch failed, fallback to local database cache
        if (!fetchSuccessful) {
            try {
                val local = channelDao.getChannelById(id)
                if (local != null) {
                    val serversList = try {
                        local.serversJson?.let {
                            json.decodeFromString<List<ChannelServerDto>>(it).map { dto ->
                                ChannelServer(
                                    id = dto.id,
                                    name = dto.name,
                                    streamUrl = dto.stream_url,
                                    streamType = dto.stream_type,
                                    quality = dto.quality
                                )
                            }
                        } ?: emptyList()
                    } catch (e: Exception) {
                        emptyList()
                    }

                    emit(Resource.Success(
                        Channel(
                            id = local.id,
                            name = local.name,
                            nameAr = local.nameAr,
                            categoryId = local.categoryId,
                            categoryName = local.categoryName,
                            categoryNameAr = local.categoryNameAr,
                            packageId = local.packageId,
                            packageName = local.packageName,
                            packageNameAr = local.packageNameAr,
                            logoUrl = local.logoUrl,
                            streamUrl = local.streamUrl,
                            streamType = local.streamType,
                            quality = local.quality,
                            backupUrl = local.backupUrl,
                            sortOrder = local.sortOrder,
                            isActive = local.isActive,
                            country = local.country,
                            language = local.language,
                            continent = local.continent,
                            isFavorited = isFav,
                            drmLicenseUrl = local.drmLicenseUrl,
                            drmHeaders = local.drmHeaders,
                            servers = serversList
                        )
                    ))
                } else {
                    emit(Resource.Error("Failed to fetch channel details and no offline cache available."))
                }
            } catch (e: Exception) {
                emit(Resource.Error("Offline cache read error: ${e.localizedMessage}"))
            }
        }
    }.flowOn(Dispatchers.IO)

    override fun syncContent(): Flow<Resource<Unit>> = flow {
        emit(Resource.Loading)
        try {
            // Fetch categories first
            val catResponse = sportApi.getCategories()
            if (catResponse.isSuccessful && catResponse.body()?.success == true) {
                val catList = catResponse.body()?.data ?: emptyList()
                val catEntities = catList.map { dto ->
                    CategoryEntity(
                        id = dto.id,
                        name = dto.name,
                        nameAr = dto.name_ar,
                        slug = dto.slug,
                        icon = dto.icon,
                        type = dto.type,
                        sortOrder = dto.sort_order,
                        isActive = dto.is_active,
                        channelsCount = dto.channels_count
                    )
                }
                categoryDao.clearCategories()
                categoryDao.insertCategories(catEntities)
            } else {
                emit(Resource.Error("Failed to sync categories."))
                return@flow
            }

            // Sync packages
            val pkgResponse = sportApi.getPackages()
            if (pkgResponse.isSuccessful && pkgResponse.body()?.success == true) {
                val pkgList = pkgResponse.body()?.data ?: emptyList()
                val pkgEntities = pkgList.map { dto ->
                    PackageEntity(
                        id = dto.id,
                        categoryId = dto.category_id,
                        name = dto.name,
                        nameAr = dto.name_ar,
                        slug = dto.slug,
                        logoUrl = dto.logo_url,
                        sortOrder = dto.sort_order,
                        isActive = dto.is_active,
                        channelsCount = dto.channels_count
                    )
                }
                packageDao.clearPackages()
                packageDao.insertPackages(pkgEntities)
            } else {
                emit(Resource.Error("Failed to sync packages."))
                return@flow
            }

            // Next sync channels list
            val chanResponse = sportApi.getChannels()
            if (chanResponse.isSuccessful && chanResponse.body()?.success == true) {
                val chanList = chanResponse.body()?.data ?: emptyList()
                val chanEntities = chanList.map { dto ->
                    val serJson = try {
                        json.encodeToString(dto.servers)
                    } catch (e: Exception) {
                        null
                    }
                    ChannelEntity(
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
                        serversJson = serJson,
                        drmLicenseUrl = dto.drm_license_url,
                        drmHeaders = dto.drm_headers
                    )
                }
                channelDao.clearChannels()
                channelDao.insertChannels(chanEntities)
                emit(Resource.Success(Unit))
            } else {
                emit(Resource.Error("Failed to sync channels."))
            }
        } catch (e: Exception) {
            emit(Resource.Error("Sync failed: ${e.localizedMessage ?: "Connection error"}"))
        }
    }.flowOn(Dispatchers.IO)

    override fun clearCache(): Flow<Resource<Unit>> = flow {
        emit(Resource.Loading)
        try {
            channelDao.clearChannels()
            packageDao.clearPackages()
            categoryDao.clearCategories()
            emit(Resource.Success(Unit))
        } catch (e: Exception) {
            emit(Resource.Error("Failed to clear local database cache: ${e.localizedMessage}"))
        }
    }.flowOn(Dispatchers.IO)
}
