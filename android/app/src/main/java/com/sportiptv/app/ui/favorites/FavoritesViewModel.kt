package com.sportiptv.app.ui.favorites

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.repository.FavoriteRepository
import com.sportiptv.app.domain.usecase.ToggleFavoriteUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.SharingStarted
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.stateIn
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class FavoritesViewModel @Inject constructor(
    private val favoriteRepository: FavoriteRepository,
    private val toggleFavoriteUseCase: ToggleFavoriteUseCase
) : ViewModel() {

    val favoriteChannels: StateFlow<List<Channel>> = favoriteRepository.getFavorites()
        .stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    fun syncFavorites() {
        viewModelScope.launch {
            favoriteRepository.syncFavorites().collect { /* merge only, keep local on failure */ }
        }
    }

    fun removeFavorite(channelId: Long) {
        viewModelScope.launch {
            toggleFavoriteUseCase(channelId, false).collect {}
        }
    }
}
