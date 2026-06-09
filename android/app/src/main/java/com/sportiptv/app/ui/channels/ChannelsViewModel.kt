package com.sportiptv.app.ui.channels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.domain.model.Category
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.model.Resource
import com.sportiptv.app.domain.repository.ChannelRepository
import com.sportiptv.app.domain.model.Package
import com.sportiptv.app.domain.usecase.GetCategoriesUseCase
import com.sportiptv.app.domain.usecase.GetPackagesUseCase
import com.sportiptv.app.domain.usecase.GetChannelsUseCase
import com.sportiptv.app.domain.usecase.ToggleFavoriteUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ChannelsViewModel @Inject constructor(
    private val channelRepository: ChannelRepository,
    private val getChannelsUseCase: GetChannelsUseCase,
    private val getPackagesUseCase: GetPackagesUseCase,
    private val getCategoriesUseCase: GetCategoriesUseCase,
    private val toggleFavoriteUseCase: ToggleFavoriteUseCase
) : ViewModel() {

    private val _syncState = MutableStateFlow<Resource<Unit>>(Resource.Idle)
    val syncState: StateFlow<Resource<Unit>> = _syncState.asStateFlow()

    private val _selectedCategoryId = MutableStateFlow<Long?>(null)
    val selectedCategoryId: StateFlow<Long?> = _selectedCategoryId.asStateFlow()

    private val _selectedPackageId = MutableStateFlow<Long?>(null)
    val selectedPackageId: StateFlow<Long?> = _selectedPackageId.asStateFlow()

    val categories: StateFlow<List<Category>> = getCategoriesUseCase()
        .stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    @OptIn(ExperimentalCoroutinesApi::class)
    val packages: StateFlow<List<Package>> = _selectedCategoryId
        .flatMapLatest { categoryId -> 
            if (categoryId != null) getPackagesUseCase(categoryId) else flowOf(emptyList()) 
        }
        .stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    @OptIn(ExperimentalCoroutinesApi::class)
    val channels: StateFlow<List<Channel>> = combine(_selectedCategoryId, _selectedPackageId) { catId, pkgId ->
        Pair(catId, pkgId)
    }.flatMapLatest { (catId, pkgId) ->
        when {
            pkgId != null && catId != null -> getChannelsUseCase(categoryId = catId, packageId = pkgId)
            pkgId != null -> getChannelsUseCase(categoryId = null, packageId = pkgId)
            catId != null -> getChannelsUseCase(categoryId = catId, packageId = null)
            else -> getChannelsUseCase(null, null)
        }
    }.stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    init {
        syncContent()
    }

    fun syncContent() {
        viewModelScope.launch {
            channelRepository.syncContent().collect { _syncState.value = it }
        }
    }

    fun retrySync() = syncContent()

    fun selectCategory(categoryId: Long?) {
        _selectedCategoryId.value = categoryId
        _selectedPackageId.value = null // Reset package selection when category changes
    }

    fun selectPackage(packageId: Long?) {
        _selectedPackageId.value = packageId
    }

    fun toggleFavorite(channelId: Long, isFavorite: Boolean) {
        viewModelScope.launch {
            toggleFavoriteUseCase(channelId, isFavorite).collect {}
        }
    }
}
