package com.sportiptv.app.ui.team

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.dto.ScoresCompetitorDto
import com.sportiptv.app.data.remote.dto.ScoresGameDto
import com.sportiptv.app.data.remote.dto.ScoresMemberDto
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class TeamDetailsViewModel @Inject constructor(
    private val sportApi: SportApi
) : ViewModel() {

    private val _competitor = MutableStateFlow<ScoresCompetitorDto?>(null)
    val competitor: StateFlow<ScoresCompetitorDto?> = _competitor.asStateFlow()

    private val _recentGames = MutableStateFlow<List<ScoresGameDto>>(emptyList())
    val recentGames: StateFlow<List<ScoresGameDto>> = _recentGames.asStateFlow()

    private val _squad = MutableStateFlow<List<ScoresMemberDto>>(emptyList())
    val squad: StateFlow<List<ScoresMemberDto>> = _squad.asStateFlow()

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading.asStateFlow()

    fun load(competitorId: Long) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                val detail = sportApi.getCompetitorDetail(competitorId)
                if (detail.isSuccessful) {
                    _competitor.value = detail.body()?.competitors?.firstOrNull()
                }

                val games = sportApi.getCompetitorGames(competitorId)
                if (games.isSuccessful) {
                    _recentGames.value = games.body()?.games?.take(15) ?: emptyList()
                }

                val squadResp = sportApi.getCompetitorSquad(competitorId)
                if (squadResp.isSuccessful) {
                    _squad.value = squadResp.body()?.members ?: emptyList()
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _isLoading.value = false
            }
        }
    }
}
