package com.sportiptv.app.data.remote.api

import com.sportiptv.app.data.remote.dto.*
import retrofit2.Response
import retrofit2.http.*

interface SportApi {

    @POST("login")
    suspend fun login(
        @Body request: LoginRequest
    ): Response<ApiResponse<LoginResponse>>

    @POST("logout")
    suspend fun logout(): Response<ApiResponse<Unit>>

    @POST("refresh")
    suspend fun refreshToken(): Response<ApiResponse<LoginResponse>>

    @GET("me")
    suspend fun me(): Response<ApiResponse<UserDto>>

    @GET("config")
    suspend fun getAppConfig(): Response<ApiResponse<AppConfigDto>>

    @POST("analytics/heartbeat")
    suspend fun sendHeartbeat(
        @Body request: HeartbeatRequest
    ): Response<ApiResponse<Unit?>>

    @POST("license/activate")
    suspend fun activateLicense(
        @Body request: ActivateRequest
    ): Response<ApiResponse<ActivateResponse>>

    @GET("license/validate")
    suspend fun validateLicense(): Response<ApiResponse<SubscriptionDto>>

    @GET("subscription")
    suspend fun getSubscription(): Response<ApiResponse<SubscriptionDto>>

    @GET("channels")
    suspend fun getChannels(
        @Query("category_id") categoryId: Long? = null,
        @Query("package_id") packageId: Long? = null,
        @Query("search") search: String? = null
    ): Response<ApiResponse<List<ChannelDto>>>

    @GET("channels/{id}")
    suspend fun getChannelDetails(
        @Path("id") id: Long
    ): Response<ApiResponse<ChannelDto>>

    @GET("categories")
    suspend fun getCategories(): Response<ApiResponse<List<CategoryDto>>>

    @GET("packages")
    suspend fun getPackages(
        @Query("category_id") categoryId: Long? = null
    ): Response<ApiResponse<List<PackageDto>>>

    @GET("favorites")
    suspend fun getFavorites(): Response<ApiResponse<List<ChannelDto>>>

    @POST("favorites/{channelId}")
    suspend fun addToFavorites(
        @Path("channelId") channelId: Long
    ): Response<ApiResponse<Unit>>

    @DELETE("favorites/{channelId}")
    suspend fun removeFromFavorites(
        @Path("channelId") channelId: Long
    ): Response<ApiResponse<Unit>>

    @GET("tournaments")
    suspend fun getTournaments(): Response<ApiResponse<List<TournamentDto>>>

    @GET("matches")
    suspend fun getMatches(
        @Query("is_live") isLive: Int? = null,
        @Query("is_world_cup") isWorldCup: Int? = null,
        @Query("tournament_id") tournamentId: Long? = null,
        @Query("date") date: String? = null
    ): Response<ApiResponse<List<MatchDto>>>

    @GET("worldcup/ai-content")
    suspend fun getWorldCupAiContent(): Response<ApiResponse<WorldCupAiDto>>

    @GET("movies")
    suspend fun getMovies(
        @Query("type") type: String? = null,
        @Query("is_latest") isLatest: Int? = null
    ): Response<ApiResponse<List<MovieDto>>>

    // ─── 365scores Proxy Endpoints ────────────────────────
    @GET("scores/today")
    suspend fun getScoresToday(): Response<ScoresResponse>

    @GET("scores/date/{date}")
    suspend fun getScoresByDate(
        @Path("date") date: String
    ): Response<ScoresResponse>

    @GET("scores/match/{gameId}")
    suspend fun getScoresMatchDetail(
        @Path("gameId") gameId: Long
    ): Response<ScoresGameDetailResponse>

    @GET("scores/stats/{gameId}")
    suspend fun getScoresMatchStats(
        @Path("gameId") gameId: Long
    ): Response<ScoresStatsResponse>

    @GET("scores/lineup/{gameId}")
    suspend fun getScoresMatchLineup(
        @Path("gameId") gameId: Long
    ): Response<ScoresLineupResponse>

    @GET("scores/standings/{competitionId}")
    suspend fun getScoresStandings(
        @Path("competitionId") competitionId: Long
    ): Response<ScoresStandingsResponse>

    @GET("scores/h2h/{gameId}")
    suspend fun getScoresH2H(
        @Path("gameId") gameId: Long
    ): Response<ScoresH2HResponse>

    @GET("scores/worldcup")
    suspend fun getWorldCupScoresToday(): Response<ScoresResponse>

    @GET("scores/worldcup/{date}")
    suspend fun getWorldCupScoresByDate(
        @Path("date") date: String
    ): Response<ScoresResponse>

    @GET("scores/competitor/{competitorId}")
    suspend fun getCompetitorDetail(
        @Path("competitorId") competitorId: Long
    ): Response<CompetitorDetailResponse>

    @GET("scores/competitor/games/{competitorId}")
    suspend fun getCompetitorGames(
        @Path("competitorId") competitorId: Long
    ): Response<CompetitorGamesResponse>

    @GET("scores/competitor/{competitorId}/squad")
    suspend fun getCompetitorSquad(
        @Path("competitorId") competitorId: Long
    ): Response<CompetitorSquadResponse>
}

