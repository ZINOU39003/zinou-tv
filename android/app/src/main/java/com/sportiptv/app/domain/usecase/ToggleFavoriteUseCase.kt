package com.sportiptv.app.domain.usecase

import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.FavoriteRepository
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class ToggleFavoriteUseCase @Inject constructor(
    private val favoriteRepository: FavoriteRepository
) {
    operator fun invoke(channelId: Long, isFavorite: Boolean): Flow<Resource<Unit>> {
        return favoriteRepository.toggleFavorite(channelId, isFavorite)
    }
}
