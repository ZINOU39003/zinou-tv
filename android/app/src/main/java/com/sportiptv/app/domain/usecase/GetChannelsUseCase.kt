package com.sportiptv.app.domain.usecase

import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.repository.ChannelRepository
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetChannelsUseCase @Inject constructor(
    private val channelRepository: ChannelRepository
) {
    operator fun invoke(categoryId: Long? = null, packageId: Long? = null): Flow<List<Channel>> {
        return channelRepository.getChannels(categoryId, packageId)
    }
}
