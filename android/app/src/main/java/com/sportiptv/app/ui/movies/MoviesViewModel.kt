package com.sportiptv.app.ui.movies

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.dto.MovieDto
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class MoviesViewModel @Inject constructor(
    private val sportApi: SportApi
) : ViewModel() {

    private val _movies = MutableStateFlow<List<MovieDto>>(emptyList())
    val movies: StateFlow<List<MovieDto>> = _movies.asStateFlow()

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading.asStateFlow()

    private val _errorMessage = MutableStateFlow<String?>(null)
    val errorMessage: StateFlow<String?> = _errorMessage.asStateFlow()

    // Filter: null = all, "movie" = movies only, "series" = series only
    private val _typeFilter = MutableStateFlow<String?>(null)
    val typeFilter: StateFlow<String?> = _typeFilter.asStateFlow()

    init {
        fetchMovies()
    }

    fun setTypeFilter(type: String?) {
        _typeFilter.value = type
        fetchMovies()
    }

    private fun fetchMovies() {
        viewModelScope.launch {
            _isLoading.value = true
            _errorMessage.value = null
            try {
                val response = sportApi.getMovies(
                    type = _typeFilter.value
                )
                if (response.isSuccessful) {
                    val body = response.body()
                    if (body != null && body.success) {
                        _movies.value = body.data ?: emptyList()
                    } else {
                        _errorMessage.value = body?.message ?: "Failed to load movies"
                        _movies.value = emptyList()
                    }
                } else {
                    _errorMessage.value = "Server error: ${response.code()}"
                    _movies.value = emptyList()
                }
            } catch (e: Exception) {
                _errorMessage.value = e.message ?: "Unknown error"
                _movies.value = emptyList()
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun refresh() {
        fetchMovies()
    }
}
