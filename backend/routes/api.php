<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// All API routes require X-API-Key verification
Route::middleware(['api.key', 'security.headers'])->group(function () {

    // Public / Authentication routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/license/activate', [LicenseController::class, 'activate']);
    Route::get('/config', [\App\Http\Controllers\Api\ConfigController::class, 'index']);
    Route::post('/analytics/heartbeat', [\App\Http\Controllers\Api\AnalyticsController::class, 'heartbeat']);

    // Protected user routes (requires JWT Token, kept for compatibility)
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/license/validate', [LicenseController::class, 'validateLicense']);
        Route::get('/subscription', [SubscriptionController::class, 'show']);
    });

    // Public IPTV Content routes (Only requires X-API-Key verification)
    // Channels
    Route::get('/channels', [ChannelController::class, 'index']);
    Route::get('/channels/{id}', [ChannelController::class, 'show']);
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    
    // Packages
    Route::get('/packages', [\App\Http\Controllers\Api\PackageController::class, 'index']);
    
    // Favorites (uses fallback user when unauthenticated)
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{channelId}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{channelId}', [FavoriteController::class, 'destroy']);

    // Tournaments
    Route::get('/tournaments', [\App\Http\Controllers\Api\TournamentController::class, 'index']);

    // Live Matches
    Route::get('/matches', [\App\Http\Controllers\Api\MatchController::class, 'index']);

    // World Cup AI Content
    Route::get('/worldcup/ai-content', [\App\Http\Controllers\Api\WorldCupAiController::class, 'index']);

    // Movies & Series
    Route::get('/movies', [\App\Http\Controllers\Api\MovieController::class, 'index']);
});

// ─── Scores Proxy (no API-Key required — public data) ───────────────────────
Route::prefix('scores')->middleware(['security.headers'])->group(function () {
    $sc = \App\Http\Controllers\Api\ScoresProxyController::class;

    Route::get('/today',                  [$sc, 'byDate']);
    Route::get('/date/{date}',            [$sc, 'byDate']);
    Route::get('/worldcup',               [$sc, 'worldCup']);
    Route::get('/worldcup/{date}',        [$sc, 'worldCup']);
    Route::get('/match/{gameId}',         [$sc, 'matchDetail']);
    Route::get('/stats/{gameId}',         [$sc, 'matchStats']);
    Route::get('/lineup/{gameId}',        [$sc, 'matchLineup']);
    Route::get('/standings/{competitionId}', [$sc, 'standings']);
    Route::get('/topscorers/{competitionId}', [$sc, 'topScorers']);
    Route::get('/search',                 [$sc, 'search']);
    Route::get('/h2h/{gameId}',           [$sc, 'headToHead']);
    Route::get('/competitor/{competitorId}', [$sc, 'competitorDetail']);
    Route::get('/competitor/{competitorId}/squad', [$sc, 'competitorSquad']);
    Route::get('/competitor/games/{competitorId}', [$sc, 'competitorGames']);
    Route::get('/player/{athleteId}', [$sc, 'playerDetail']);
    Route::get('/news', [$sc, 'news']);
    Route::get('/news/article', [$sc, 'newsArticle']);
});

