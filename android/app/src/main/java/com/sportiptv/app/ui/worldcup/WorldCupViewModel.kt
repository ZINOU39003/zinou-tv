package com.sportiptv.app.ui.worldcup

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.data.cache.ScoresGameCache
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.dto.*
import com.sportiptv.app.domain.model.Channel
import com.sportiptv.app.domain.repository.ChannelRepository
import com.sportiptv.app.ui.matches.WORLD_CUP_COMPETITION_ID
import com.sportiptv.app.ui.matches.isLiveMatch
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.isActive
import kotlinx.coroutines.launch
import java.time.LocalDate
import java.time.format.DateTimeFormatter
import javax.inject.Inject

data class WorldCupBroadcast(
    val streamUrl: String? = null,
    val channelId: Long? = null,
    val channelName: String? = null
)

data class WorldCupMatchItem(
    val scoresGame: ScoresGameDto,
    val adminMatch: MatchDto? = null,
    val broadcast: WorldCupBroadcast? = null
)

@HiltViewModel
class WorldCupViewModel @Inject constructor(
    private val sportApi: SportApi,
    private val channelRepository: ChannelRepository
) : ViewModel() {

    private val _matches = MutableStateFlow<List<WorldCupMatchItem>>(emptyList())
    val matches: StateFlow<List<WorldCupMatchItem>> = _matches.asStateFlow()

    private val _selectedDate = MutableStateFlow(LocalDate.now())
    val selectedDate: StateFlow<LocalDate> = _selectedDate.asStateFlow()

    private val _news = MutableStateFlow<List<WorldCupNewsDto>>(emptyList())
    val news: StateFlow<List<WorldCupNewsDto>> = _news.asStateFlow()

    private val _predictions = MutableStateFlow<List<MatchPredictionDto>>(emptyList())
    val predictions: StateFlow<List<MatchPredictionDto>> = _predictions.asStateFlow()

    private val _standings = MutableStateFlow<List<GroupStandingDto>>(emptyList())
    val standings: StateFlow<List<GroupStandingDto>> = _standings.asStateFlow()

    private val _scoreStandings = MutableStateFlow<List<ScoresStandingRowDto>>(emptyList())
    val scoreStandings: StateFlow<List<ScoresStandingRowDto>> = _scoreStandings.asStateFlow()

    private val _broadcastChannels = MutableStateFlow<List<Channel>>(emptyList())
    val broadcastChannels: StateFlow<List<Channel>> = _broadcastChannels.asStateFlow()

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading.asStateFlow()

    private val _errorMessage = MutableStateFlow<String?>(null)
    val errorMessage: StateFlow<String?> = _errorMessage.asStateFlow()

    private var adminMatches: List<MatchDto> = emptyList()
    private var liveRefreshJob: Job? = null

    init {
        refresh()
        startLiveRefresh()
    }

    fun setDate(date: LocalDate) {
        _selectedDate.value = date
        loadMatchesForDate(date)
    }

    fun refresh() {
        viewModelScope.launch {
            _isLoading.value = true
            _errorMessage.value = null
            try {
                loadAdminMatches()
                loadMatchesForDate(_selectedDate.value)
                loadBroadcastChannels()
                loadAiContent()
                loadScoreStandings()
            } catch (e: Exception) {
                _errorMessage.value = e.message
            } finally {
                _isLoading.value = false
            }
        }
    }

    private fun startLiveRefresh() {
        liveRefreshJob?.cancel()
        liveRefreshJob = viewModelScope.launch {
            while (isActive) {
                delay(15_000)
                if (_matches.value.any { it.scoresGame.isLiveMatch() }) {
                    loadMatchesForDate(_selectedDate.value, silent = true)
                }
            }
        }
    }

    private fun loadMatchesForDate(date: LocalDate, silent: Boolean = false) {
        viewModelScope.launch {
            if (!silent) _isLoading.value = true
            try {
                val response = if (date == LocalDate.now()) {
                    sportApi.getWorldCupScoresToday()
                } else {
                    val formatted = date.format(DateTimeFormatter.ofPattern("dd-MM-yyyy"))
                    sportApi.getWorldCupScoresByDate(formatted)
                }

                var games = if (response.isSuccessful) {
                    response.body()?.games.orEmpty()
                } else {
                    emptyList()
                }

                if (games.isEmpty() && date == LocalDate.now()) {
                    val fallback = sportApi.getScoresToday()
                    if (fallback.isSuccessful) {
                        games = fallback.body()?.games.orEmpty().filter {
                            it.competitionId == WORLD_CUP_COMPETITION_ID ||
                                it.competitionDisplayName?.contains("كأس العالم", ignoreCase = true) == true ||
                                it.competitionDisplayName?.contains("World Cup", ignoreCase = true) == true
                        }
                    }
                }

                ScoresGameCache.putAll(games)

                val items = games.map { game ->
                    val admin = findAdminMatch(game)
                    WorldCupMatchItem(game, admin, resolveBroadcast(game, admin))
                }.sortedBy { it.scoresGame.startTime }

                _matches.value = items
            } catch (e: Exception) {
                if (!silent) _errorMessage.value = e.message
            } finally {
                if (!silent) _isLoading.value = false
            }
        }
    }

    private suspend fun loadAdminMatches() {
        val response = sportApi.getMatches(isWorldCup = 1)
        if (response.isSuccessful && response.body()?.success == true) {
            adminMatches = response.body()?.data ?: emptyList()
        }
    }

    private suspend fun loadBroadcastChannels() {
        val queries = listOf("كأس العالم", "World Cup", "bein", "بي ان", "FIFA")
        val found = mutableListOf<Channel>()
        for (q in queries) {
            try {
                channelRepository.searchChannels(q).first().forEach { ch ->
                    if (found.none { it.id == ch.id }) found.add(ch)
                }
            } catch (_: Exception) { }
        }
        _broadcastChannels.value = found.take(12)
    }

    private suspend fun loadAiContent() {
        try {
            val response = sportApi.getWorldCupAiContent()
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                _news.value = data?.news.orEmpty()
                _predictions.value = data?.predictions.orEmpty()
                if (_standings.value.isEmpty()) _standings.value = data?.standings.orEmpty()
            }
        } catch (_: Exception) { }
    }

    private suspend fun loadScoreStandings() {
        try {
            val response = sportApi.getScoresStandings(WORLD_CUP_COMPETITION_ID)
            if (response.isSuccessful) {
                val rows = response.body()?.standing?.rows
                    ?: response.body()?.standings?.firstOrNull()?.rows
                    ?: emptyList()
                if (rows.isNotEmpty()) _scoreStandings.value = rows
            }
        } catch (_: Exception) { }
    }

    private fun findAdminMatch(game: ScoresGameDto): MatchDto? {
        adminMatches.find { it.scores_game_id == game.id }?.let { return it }

        val home = game.homeCompetitor?.name.orEmpty()
        val away = game.awayCompetitor?.name.orEmpty()
        return adminMatches.find { admin ->
            (teamsMatch(admin.team_one_name, home) && teamsMatch(admin.team_two_name, away)) ||
                (teamsMatch(admin.team_one_name, away) && teamsMatch(admin.team_two_name, home)) ||
                (teamsMatch(admin.team_one_name_ar.orEmpty(), home) && teamsMatch(admin.team_two_name_ar.orEmpty(), away))
        }
    }

    private fun teamsMatch(a: String, b: String): Boolean {
        if (a.isBlank() || b.isBlank()) return false
        return a.contains(b, ignoreCase = true) || b.contains(a, ignoreCase = true)
    }

    suspend fun resolveBroadcast(game: ScoresGameDto, admin: MatchDto?): WorldCupBroadcast {
        admin?.channel_id?.let { channelId ->
            val name = admin.team_one_name
            return WorldCupBroadcast(channelId = channelId, channelName = name)
        }

        admin?.stream_url?.takeIf { it.isNotBlank() }?.let {
            return WorldCupBroadcast(streamUrl = it)
        }

        game.tvNetworks.orEmpty().forEach { network ->
            val name = network.name ?: return@forEach
            try {
                val channels = channelRepository.searchChannels(name).first()
                if (channels.isNotEmpty()) {
                    return WorldCupBroadcast(channelId = channels.first().id, channelName = channels.first().name)
                }
            } catch (_: Exception) { }
        }

        for (q in listOf("كأس العالم", "bein sport", "بي ان سبورت")) {
            try {
                val channels = channelRepository.searchChannels(q).first()
                if (channels.isNotEmpty()) {
                    return WorldCupBroadcast(channelId = channels.first().id, channelName = channels.first().name)
                }
            } catch (_: Exception) { }
        }

        return WorldCupBroadcast()
    }

    fun watchMatch(item: WorldCupMatchItem, onStreamUrl: (String) -> Unit, onChannel: (Long) -> Unit, onDetails: (Long) -> Unit) {
        viewModelScope.launch {
            ScoresGameCache.put(item.scoresGame)
            val broadcast = item.broadcast ?: resolveBroadcast(item.scoresGame, item.adminMatch)
            when {
                item.scoresGame.isLiveMatch() && broadcast.channelId != null -> onChannel(broadcast.channelId)
                item.scoresGame.isLiveMatch() && !broadcast.streamUrl.isNullOrBlank() -> onStreamUrl(broadcast.streamUrl)
                else -> onDetails(item.scoresGame.id)
            }
        }
    }

    fun openMatchDetails(item: WorldCupMatchItem, onDetails: (Long) -> Unit) {
        ScoresGameCache.put(item.scoresGame)
        onDetails(item.scoresGame.id)
    }

    override fun onCleared() {
        liveRefreshJob?.cancel()
        super.onCleared()
    }
}
