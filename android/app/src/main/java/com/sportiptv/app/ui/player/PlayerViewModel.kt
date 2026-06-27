package com.sportiptv.app.ui.player

import android.content.Context
import android.os.Build
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.BuildConfig
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.dto.HeartbeatRequest
import com.sportiptv.app.domain.repository.AuthRepository
import com.sportiptv.app.util.DeviceUtils
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.Dispatchers
import com.sportiptv.app.domain.model.Category
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.ChannelRepository
import com.sportiptv.app.domain.repository.SubscriptionRepository
import com.sportiptv.app.domain.repository.EpgRepository
import com.sportiptv.app.domain.model.EpgProgram
import com.sportiptv.app.domain.usecase.ToggleFavoriteUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

import kotlinx.coroutines.ExperimentalCoroutinesApi

@OptIn(ExperimentalCoroutinesApi::class)
@HiltViewModel
class PlayerViewModel @Inject constructor(
    private val channelRepository: ChannelRepository,
    private val epgRepository: EpgRepository,
    private val toggleFavoriteUseCase: ToggleFavoriteUseCase,
    private val authRepository: AuthRepository,
    private val subscriptionRepository: SubscriptionRepository,
    private val sportApi: SportApi,
    @ApplicationContext private val context: Context,
) : ViewModel() {

    fun isPremiumUser(): Boolean = authRepository.isUserLoggedIn()

    private val _appConfigState = MutableStateFlow<Resource<com.sportiptv.app.data.remote.dto.AppConfigDto>>(Resource.Idle)
    val appConfigState: StateFlow<Resource<com.sportiptv.app.data.remote.dto.AppConfigDto>> = _appConfigState.asStateFlow()

    fun loadAppConfig() {
        viewModelScope.launch {
            subscriptionRepository.getAppConfig().collect { result ->
                _appConfigState.value = result
                if (result is Resource.Success) {
                    val epgUrl = result.data.epg_url
                    if (epgUrl.isNotBlank()) {
                        epgRepository.fetchEpg(epgUrl).collect()
                    }
                }
            }
        }
    }

    private val _channelState = MutableStateFlow<Resource<Channel>>(Resource.Idle)
    val channelState: StateFlow<Resource<Channel>> = _channelState.asStateFlow()

    private val _allChannels = MutableStateFlow<List<Channel>>(emptyList())
    val allChannels: StateFlow<List<Channel>> = _allChannels.asStateFlow()

    private val _selectedCategoryId = MutableStateFlow<Long?>(null)
    val selectedCategoryId: StateFlow<Long?> = _selectedCategoryId.asStateFlow()

    private val _searchCategoryQuery = MutableStateFlow("")
    val searchCategoryQuery: StateFlow<String> = _searchCategoryQuery.asStateFlow()

    private val _searchChannelQuery = MutableStateFlow("")
    val searchChannelQuery: StateFlow<String> = _searchChannelQuery.asStateFlow()

    val categories: StateFlow<List<Category>> = channelRepository.getCategories()
        .stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    val epgPrograms = _channelState.flatMapLatest { state ->
        if (state is Resource.Success) {
            epgRepository.getProgramsForChannel(state.data.name)
        } else {
            flowOf(emptyList())
        }
    }.stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    // Sibling channels in the category of the currently playing channel
    val siblingChannels = combine(_channelState, _allChannels) { state, all ->
        if (state is Resource.Success) {
            val currentChan = state.data
            all.filter { it.categoryId == currentChan.categoryId }
        } else {
            emptyList()
        }
    }.stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    // Categories filtered by the category search query AND channelsCount > 0
    val drawerCategories = combine(categories, _searchCategoryQuery) { list, query ->
        val nonEmptyList = list.filter { it.channelsCount > 0 }
        if (query.isBlank()) {
            nonEmptyList
        } else {
            nonEmptyList.filter {
                it.name.contains(query, ignoreCase = true) ||
                (it.nameAr ?: "").contains(query, ignoreCase = true)
            }
        }
    }.stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    // Channels filtered by the selected category and channel search query
    val drawerChannels = combine(_allChannels, _selectedCategoryId, _searchChannelQuery) { channels, categoryId, query ->
        var filtered = channels
        if (categoryId != null) {
            filtered = filtered.filter { it.categoryId == categoryId }
        }
        if (query.isNotBlank()) {
            filtered = filtered.filter {
                it.name.contains(query, ignoreCase = true) ||
                (it.nameAr ?: "").contains(query, ignoreCase = true)
            }
        }
        filtered
    }.stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    init {
        loadAllChannels()
        loadAppConfig()
    }

    private fun loadAllChannels() {
        viewModelScope.launch {
            channelRepository.getChannels(null).collect { channels ->
                _allChannels.value = channels
            }
        }
    }

    fun loadChannel(channelId: Long) {
        viewModelScope.launch {
            channelRepository.getChannelDetails(channelId).collect { result ->
                _channelState.value = result
                if (result is Resource.Success) {
                    val channel = result.data
                    // If no category is selected in the drawer, default to the current channel's category
                    if (_selectedCategoryId.value == null) {
                        _selectedCategoryId.value = channel.categoryId
                    }
                }
            }
        }
    }

    fun setMockChannel(channel: Channel) {
        _channelState.value = Resource.Success(channel)
        if (_selectedCategoryId.value == null) {
            _selectedCategoryId.value = channel.categoryId
        }
    }

    fun selectDrawerCategory(categoryId: Long?) {
        _selectedCategoryId.value = categoryId
    }

    fun setSearchCategory(query: String) {
        _searchCategoryQuery.value = query
    }

    fun setSearchChannel(query: String) {
        _searchChannelQuery.value = query
    }

    fun toggleFavorite(channelId: Long, isFavorite: Boolean) {
        viewModelScope.launch {
            toggleFavoriteUseCase(channelId, isFavorite).collect { result ->
                // Update local list favorite status if success
                if (result is Resource.Success) {
                    // Force refresh channel state and channels list to update heart icons
                    val current = _channelState.value
                    if (current is Resource.Success && current.data.id == channelId) {
                        _channelState.value = Resource.Success(current.data.copy(isFavorited = isFavorite))
                    }
                    _allChannels.value = _allChannels.value.map {
                        if (it.id == channelId) it.copy(isFavorited = isFavorite) else it
                    }
                }
            }
        }
    }

    fun playNextChannel() {
        val siblings = siblingChannels.value
        val currentState = _channelState.value
        if (siblings.isNotEmpty() && currentState is Resource.Success) {
            val currentIdx = siblings.indexOfFirst { it.id == currentState.data.id }
            if (currentIdx != -1) {
                val nextIdx = (currentIdx + 1) % siblings.size
                loadChannel(siblings[nextIdx].id)
            }
        }
    }

    fun sendViewHeartbeat(channelId: Long) {
        viewModelScope.launch(Dispatchers.IO) {
            try {
                sportApi.sendHeartbeat(
                    HeartbeatRequest(
                        device_id = DeviceUtils.getDeviceId(context),
                        channel_id = channelId,
                        device_name = Build.MODEL,
                        app_version = BuildConfig.VERSION_NAME,
                    )
                )
            } catch (_: Exception) {
            }
        }
    }

    fun playPreviousChannel() {
        val siblings = siblingChannels.value
        val currentState = _channelState.value
        if (siblings.isNotEmpty() && currentState is Resource.Success) {
            val currentIdx = siblings.indexOfFirst { it.id == currentState.data.id }
            if (currentIdx != -1) {
                val prevIdx = if (currentIdx - 1 < 0) siblings.size - 1 else currentIdx - 1
                loadChannel(siblings[prevIdx].id)
            }
        }
    }
}
