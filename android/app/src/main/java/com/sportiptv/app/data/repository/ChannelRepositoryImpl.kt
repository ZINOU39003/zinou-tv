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
import com.sportiptv.app.util.M3uParser
import javax.inject.Inject

class ChannelRepositoryImpl @Inject constructor(
    private val sportApi: SportApi,
    private val channelDao: ChannelDao,
    private val categoryDao: CategoryDao,
    private val packageDao: PackageDao,
    private val favoriteDao: FavoriteDao,
    private val json: Json,
    private val m3uParser: M3uParser
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
                                streamUrl = com.sportiptv.app.util.CryptoUtils.decryptStreamUrl(dto.stream_url),
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
                    streamUrl = com.sportiptv.app.util.CryptoUtils.decryptStreamUrl(entity.streamUrl),
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
                                streamUrl = com.sportiptv.app.util.CryptoUtils.decryptStreamUrl(dto.stream_url),
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
                    streamUrl = com.sportiptv.app.util.CryptoUtils.decryptStreamUrl(entity.streamUrl),
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
        val isFav = try {
            favoriteDao.isChannelFavorited(id).first()
        } catch (e: Exception) {
            false
        }
        
        var localChannel: Channel? = null
        try {
            val local = channelDao.getChannelById(id)
            if (local != null) {
                val serversList = try {
                    local.serversJson?.let {
                        json.decodeFromString<List<ChannelServerDto>>(it).map { dto ->
                            ChannelServer(
                                id = dto.id,
                                name = dto.name,
                                streamUrl = com.sportiptv.app.util.CryptoUtils.decryptStreamUrl(dto.stream_url),
                                streamType = dto.stream_type,
                                quality = dto.quality
                            )
                        }
                    } ?: emptyList()
                } catch (e: Exception) {
                    emptyList()
                }

                localChannel = Channel(
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
                    streamUrl = com.sportiptv.app.util.CryptoUtils.decryptStreamUrl(local.streamUrl),
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
                emit(Resource.Success(localChannel))
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }

        var fetchSuccessful = false
        var remoteChannel: Channel? = null
        try {
            // Attempt to fetch the latest details from the remote API in the background
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
                    remoteChannel = Channel(
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
                    
                    emit(Resource.Success(remoteChannel))
                    fetchSuccessful = true
                }
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }

        // If remote fetch failed and we didn't have local cache, emit error
        if (!fetchSuccessful && localChannel == null) {
            emit(Resource.Error("Failed to fetch channel details and no offline cache available."))
        }
    }.flowOn(Dispatchers.IO)

    override fun syncContent(): Flow<Resource<Unit>> = flow {
        emit(Resource.Loading)
        try {
            // 1. Fetch channels
            val chanResponse = sportApi.getChannels()
            val chanListMutable = if (chanResponse.isSuccessful && chanResponse.body()?.success == true) {
                chanResponse.body()?.data?.toMutableList() ?: mutableListOf()
            } else {
                emit(Resource.Error("Failed to sync channels."))
                return@flow
            }

            // 2. Fetch categories
            val catResponse = sportApi.getCategories()
            val catListMutable = if (catResponse.isSuccessful && catResponse.body()?.success == true) {
                catResponse.body()?.data?.toMutableList() ?: mutableListOf()
            } else {
                emit(Resource.Error("Failed to sync categories."))
                return@flow
            }

            // 3. Fetch packages
            val pkgResponse = sportApi.getPackages()
            val pkgListMutable = if (pkgResponse.isSuccessful && pkgResponse.body()?.success == true) {
                pkgResponse.body()?.data?.toMutableList() ?: mutableListOf()
            } else {
                emit(Resource.Error("Failed to sync packages."))
                return@flow
            }

            // --- CATEGORY GROUPING LOGIC ---
            // Group categories belonging to the same company (e.g., BEIN, OSN, ROTANA, MBC, SSC)
            val groupedCategories = mutableMapOf<String, com.sportiptv.app.data.remote.dto.CategoryDto>()
            val categoryIdRemap = mutableMapOf<Long, Long>()

            val finalCatList = mutableListOf<com.sportiptv.app.data.remote.dto.CategoryDto>()

            for (cat in catListMutable) {
                val nameLower = cat.name.lowercase(java.util.Locale.getDefault())
                val groupKey = when {
                    nameLower.contains("bein") -> "BEIN NETWORK"
                    nameLower.contains("osn") -> "OSN NETWORK"
                    nameLower.contains("rotana") -> "ROTANA NETWORK"
                    nameLower.contains("mbc") || nameLower.contains("mbs") -> "MBC NETWORK"
                    nameLower.contains("ssc") -> "SSC NETWORK"
                    nameLower.contains("sky") -> "SKY NETWORK"
                    else -> null
                }

                if (groupKey != null) {
                    if (groupedCategories.containsKey(groupKey)) {
                        // We already have a master category for this group, remap this category's ID to the master ID
                        categoryIdRemap[cat.id] = groupedCategories[groupKey]!!.id
                    } else {
                        // This is the first category of this group. Make it the master category.
                        val masterCat = cat.copy(name = groupKey, name_ar = groupKey)
                        groupedCategories[groupKey] = masterCat
                        finalCatList.add(masterCat)
                        categoryIdRemap[cat.id] = masterCat.id
                    }
                } else {
                    finalCatList.add(cat)
                    categoryIdRemap[cat.id] = cat.id
                }
            }

            // Remap channel category IDs
            val finalChanList = chanListMutable.map { chan ->
                val newCatId = categoryIdRemap[chan.category_id] ?: chan.category_id
                val newCatName = finalCatList.find { it.id == newCatId }?.name ?: chan.category_name
                val newCatNameAr = finalCatList.find { it.id == newCatId }?.name_ar ?: chan.category_name_ar
                chan.copy(category_id = newCatId, category_name = newCatName, category_name_ar = newCatNameAr)
            }

            // Remap package category IDs
            val finalPkgList = pkgListMutable.map { pkg ->
                val newCatId = categoryIdRemap[pkg.category_id] ?: pkg.category_id
                pkg.copy(category_id = newCatId)
            }
            // --- END GROUPING LOGIC ---

            // Save Categories
            val catEntities = finalCatList.map { dto ->
                val actualCount = finalChanList.count { it.category_id == dto.id }
                
                // Use category's own icon, or fallback to the first available channel's logo in this category
                var finalIcon = dto.icon
                if (finalIcon.isNullOrBlank()) {
                    finalIcon = finalChanList.firstOrNull { it.category_id == dto.id && !it.logo_url.isNullOrBlank() }?.logo_url
                }

                CategoryEntity(
                    id = dto.id,
                    name = dto.name,
                    nameAr = dto.name_ar,
                    slug = dto.slug,
                    icon = finalIcon,
                    type = dto.type,
                    sortOrder = dto.sort_order,
                    isActive = dto.is_active,
                    channelsCount = actualCount
                )
            }
            categoryDao.clearCategories()
            categoryDao.insertCategories(catEntities)

            // Save Packages
            val pkgEntities = finalPkgList.map { dto ->
                val actualCount = finalChanList.count { it.package_id == dto.id }
                PackageEntity(
                    id = dto.id,
                    categoryId = dto.category_id,
                    name = dto.name,
                    nameAr = dto.name_ar,
                    slug = dto.slug,
                    logoUrl = dto.logo_url,
                    sortOrder = dto.sort_order,
                    isActive = dto.is_active,
                    channelsCount = actualCount
                )
            }
            packageDao.clearPackages()
            packageDao.insertPackages(pkgEntities)

            // Save Channels
            val chanEntities = finalChanList.map { dto ->
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
        } catch (e: Exception) {
            emit(Resource.Error("Sync failed: ${e.localizedMessage ?: "Connection error"}"))
        }
    }.flowOn(Dispatchers.IO)

    override fun syncFromM3u(url: String): Flow<Resource<Unit>> = flow {
        emit(Resource.Loading)
        try {
            val result = m3uParser.fetchAndParseM3u(url)
            
            // Clear current channels
            channelDao.clearChannels()
            categoryDao.clearCategories()
            
            // Insert parsed
            categoryDao.insertCategories(result.categories)
            channelDao.insertChannels(result.channels)
            
            emit(Resource.Success(Unit))
        } catch (e: Exception) {
            emit(Resource.Error("M3U Sync failed: ${e.localizedMessage}"))
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
