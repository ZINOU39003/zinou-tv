package com.sportiptv.app.domain.usecase

import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.repository.ChannelRepository
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import javax.inject.Inject

class SearchChannelsUseCase @Inject constructor(
    private val channelRepository: ChannelRepository
) {
    operator fun invoke(query: String): Flow<List<Channel>> {
        if (query.trim().length < 2) {
            // Emit empty list for short inputs to prevent overloading database searches
            return flow { emit(emptyList()) }
        }
        return channelRepository.searchChannels(query.trim())
    }
}
