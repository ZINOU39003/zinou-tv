package com.sportiptv.app.data.cache

import com.sportiptv.app.data.remote.dto.ScoresGameDto

object ScoresGameCache {
    private val games = mutableMapOf<Long, ScoresGameDto>()

    fun putAll(list: List<ScoresGameDto>) {
        list.forEach { games[it.id] = it }
    }

    fun put(game: ScoresGameDto) {
        games[game.id] = game
    }

    fun get(gameId: Long): ScoresGameDto? = games[gameId]
}
