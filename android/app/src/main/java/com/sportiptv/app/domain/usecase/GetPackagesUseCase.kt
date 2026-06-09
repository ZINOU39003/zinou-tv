package com.sportiptv.app.domain.usecase

import com.sportiptv.app.domain.model.Package
import com.sportiptv.app.domain.repository.ChannelRepository
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetPackagesUseCase @Inject constructor(
    private val channelRepository: ChannelRepository
) {
    operator fun invoke(categoryId: Long? = null): Flow<List<Package>> {
        return channelRepository.getPackages(categoryId)
    }
}
