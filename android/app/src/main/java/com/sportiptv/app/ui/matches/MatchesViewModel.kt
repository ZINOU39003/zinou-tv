package com.sportiptv.app.ui.matches

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.data.cache.ScoresGameCache
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.dto.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class MatchesViewModel @Inject constructor(
    private val sportApi: SportApi
) : ViewModel() {

    private val _scoresResponse = MutableStateFlow<ScoresResponse?>(null)
    val scoresResponse: StateFlow<ScoresResponse?> = _scoresResponse.asStateFlow()

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading.asStateFlow()

    private val _errorMessage = MutableStateFlow<String?>(null)
    val errorMessage: StateFlow<String?> = _errorMessage.asStateFlow()

    private val _selectedCompetitionId = MutableStateFlow<Long?>(null)
    val selectedCompetitionId: StateFlow<Long?> = _selectedCompetitionId.asStateFlow()

    private val _filter = MutableStateFlow<String?>(null) // null = all, "live" = live only
    val filter: StateFlow<String?> = _filter.asStateFlow()

    private val _selectedDate = MutableStateFlow<String?>(null) // yyyy-MM-dd
    val selectedDate: StateFlow<String?> = _selectedDate.asStateFlow()

    // Match details state flows
    private val _matchDetail = MutableStateFlow<ScoresGameDto?>(null)
    val matchDetail: StateFlow<ScoresGameDto?> = _matchDetail.asStateFlow()

    private val _matchDetailLoading = MutableStateFlow(false)
    val matchDetailLoading: StateFlow<Boolean> = _matchDetailLoading.asStateFlow()

    private val _matchStats = MutableStateFlow<List<ScoresStatItemDto>>(emptyList())
    val matchStats: StateFlow<List<ScoresStatItemDto>> = _matchStats.asStateFlow()

    private val _matchStatsLoading = MutableStateFlow(false)
    val matchStatsLoading: StateFlow<Boolean> = _matchStatsLoading.asStateFlow()

    private val _matchLineup = MutableStateFlow<ScoresGameDto?>(null)
    val matchLineup: StateFlow<ScoresGameDto?> = _matchLineup.asStateFlow()

    private val _matchLineupLoading = MutableStateFlow(false)
    val matchLineupLoading: StateFlow<Boolean> = _matchLineupLoading.asStateFlow()

    private val _standings = MutableStateFlow<List<ScoresStandingRowDto>>(emptyList())
    val standings: StateFlow<List<ScoresStandingRowDto>> = _standings.asStateFlow()

    private val _standingsLoading = MutableStateFlow(false)
    val standingsLoading: StateFlow<Boolean> = _standingsLoading.asStateFlow()

    private val _h2h = MutableStateFlow<List<ScoresGameDto>>(emptyList())
    val h2h: StateFlow<List<ScoresGameDto>> = _h2h.asStateFlow()

    private val _h2hLoading = MutableStateFlow(false)
    val h2hLoading: StateFlow<Boolean> = _h2hLoading.asStateFlow()

    init {
        fetchScores()
    }

    fun setFilter(filterType: String?) {
        _filter.value = filterType
    }

    fun setCompetitionFilter(competitionId: Long?) {
        _selectedCompetitionId.value = competitionId
    }

    fun setDateFilter(date: String?) {
        _selectedDate.value = date
        fetchScores()
    }

    fun fetchScores() {
        viewModelScope.launch {
            _isLoading.value = true
            _errorMessage.value = null
            try {
                val dateVal = _selectedDate.value
                val response = if (dateVal.isNullOrEmpty()) {
                    sportApi.getScoresToday()
                } else {
                    // Convert yyyy-MM-dd to dd-MM-yyyy
                    val parts = dateVal.split("-")
                    val formattedDate = if (parts.size == 3 && parts[0].length == 4) {
                        "${parts[2]}-${parts[1]}-${parts[0]}"
                    } else {
                        dateVal
                    }
                    sportApi.getScoresByDate(formattedDate)
                }

                if (response.isSuccessful) {
                    val body = response.body()
                    _scoresResponse.value = body
                    body?.games?.let { ScoresGameCache.putAll(it) }
                } else {
                    _errorMessage.value = "Server error: ${response.code()}"
                }
            } catch (e: Exception) {
                _errorMessage.value = e.localizedMessage ?: "Unknown error"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun fetchMatchDetail(gameId: Long) {
        viewModelScope.launch {
            _matchDetailLoading.value = true
            val cached = ScoresGameCache.get(gameId)
            if (cached != null) _matchDetail.value = cached

            try {
                val response = sportApi.getScoresMatchDetail(gameId)
                if (response.isSuccessful) {
                    val detail = response.body()?.game
                    if (detail != null) {
                        _matchDetail.value = mergeGameData(cached, detail, _matchLineup.value)
                        ScoresGameCache.put(_matchDetail.value!!)
                    }
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _matchDetailLoading.value = false
            }
        }
    }

    private fun mergeGameData(
        cached: ScoresGameDto?,
        detail: ScoresGameDto,
        lineup: ScoresGameDto?
    ): ScoresGameDto {
        return detail.copy(
            homeCompetitor = detail.homeCompetitor ?: cached?.homeCompetitor,
            awayCompetitor = detail.awayCompetitor ?: cached?.awayCompetitor,
            events = detail.events ?: cached?.events,
            members = detail.members ?: lineup?.members ?: cached?.members,
            tvNetworks = detail.tvNetworks ?: cached?.tvNetworks,
            venue = detail.venue ?: cached?.venue,
            statusGroup = detail.statusGroup ?: cached?.statusGroup,
            gameMinute = detail.gameMinute ?: cached?.gameMinute,
            startTime = detail.startTime ?: cached?.startTime
        )
    }

    fun fetchMatchStats(gameId: Long) {
        viewModelScope.launch {
            _matchStatsLoading.value = true
            _matchStats.value = emptyList()
            try {
                val response = sportApi.getScoresMatchStats(gameId)
                if (response.isSuccessful) {
                    _matchStats.value = response.body()?.stats ?: emptyList()
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _matchStatsLoading.value = false
            }
        }
    }

    fun fetchMatchLineup(gameId: Long) {
        viewModelScope.launch {
            _matchLineupLoading.value = true
            try {
                val response = sportApi.getScoresMatchLineup(gameId)
                if (response.isSuccessful) {
                    val lineupGame = response.body()?.game
                    _matchLineup.value = lineupGame
                    val current = _matchDetail.value
                    if (lineupGame != null && current != null) {
                        val merged = mergeGameData(current, current, lineupGame)
                        _matchDetail.value = merged
                    }
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _matchLineupLoading.value = false
            }
        }
    }

    fun fetchStandings(competitionId: Long) {
        viewModelScope.launch {
            _standingsLoading.value = true
            _standings.value = emptyList()
            try {
                val response = sportApi.getScoresStandings(competitionId)
                if (response.isSuccessful) {
                    val body = response.body()
                    val rows = body?.standing?.rows ?: body?.standings?.firstOrNull()?.rows ?: emptyList()
                    _standings.value = rows
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _standingsLoading.value = false
            }
        }
    }

    fun fetchH2H(gameId: Long) {
        viewModelScope.launch {
            _h2hLoading.value = true
            _h2h.value = emptyList()
            try {
                val response = sportApi.getScoresH2H(gameId)
                if (response.isSuccessful) {
                    _h2h.value = response.body()?.headToHead?.games ?: emptyList()
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _h2hLoading.value = false
            }
        }
    }

    fun refresh() {
        fetchScores()
    }
}
