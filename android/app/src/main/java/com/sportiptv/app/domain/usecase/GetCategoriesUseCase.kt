package com.sportiptv.app.domain.usecase

import com.sportiptv.app.domain.model.Category
import com.sportiptv.app.domain.repository.ChannelRepository
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetCategoriesUseCase @Inject constructor(
    private val channelRepository: ChannelRepository
) {
    operator fun invoke(): Flow<List<Category>> {
        return channelRepository.getCategories()
    }
}
