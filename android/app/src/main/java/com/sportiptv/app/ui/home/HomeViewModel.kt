package com.sportiptv.app.ui.home

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Category
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.ChannelRepository
import com.sportiptv.app.domain.usecase.GetCategoriesUseCase
import com.sportiptv.app.domain.usecase.GetChannelsUseCase
import com.sportiptv.app.domain.usecase.ToggleFavoriteUseCase
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.dto.MatchDto
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class HomeViewModel @Inject constructor(
    private val channelRepository: ChannelRepository,
    private val getChannelsUseCase: GetChannelsUseCase,
    private val getCategoriesUseCase: GetCategoriesUseCase,
    private val toggleFavoriteUseCase: ToggleFavoriteUseCase,
    private val sportApi: SportApi
) : ViewModel() {

    private val _syncState = MutableStateFlow<Resource<Unit>>(Resource.Idle)
    val syncState: StateFlow<Resource<Unit>> = _syncState.asStateFlow()

    val categories: StateFlow<List<Category>> = getCategoriesUseCase()
        .stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    val channels: StateFlow<List<Channel>> = getChannelsUseCase()
        .stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    val featuredChannels: StateFlow<List<Channel>> = getChannelsUseCase()
        .map { list -> list.take(5) } // Fetch first 5 channels as featured list
        .stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    private val _liveMatches = MutableStateFlow<List<MatchDto>>(emptyList())
    val liveMatches: StateFlow<List<MatchDto>> = _liveMatches.asStateFlow()

    init {
        syncData()
        fetchLiveMatches()
    }

    fun fetchLiveMatches() {
        viewModelScope.launch {
            try {
                val response = sportApi.getMatches(isLive = 1, isWorldCup = null, date = null)
                if (response.isSuccessful) {
                    val body = response.body()
                    if (body != null && body.success) {
                        _liveMatches.value = body.data ?: emptyList()
                    }
                }
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
    }

    fun syncData() {
        viewModelScope.launch {
            channelRepository.syncContent().collect { result ->
                _syncState.value = result
            }
        }
    }

    fun toggleFavorite(channelId: Long, isFavorite: Boolean) {
        viewModelScope.launch {
            toggleFavoriteUseCase(channelId, isFavorite).collect {
                // UI will automatically update because it observes mapped flow
            }
        }
    }
}
