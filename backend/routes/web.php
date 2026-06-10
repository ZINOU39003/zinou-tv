<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChannelController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivationCodeController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\HarAnalyzerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProActivationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\DeploySetupController;
use App\Http\Controllers\ScoresWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// One-time DB setup for Render free tier (no Shell). Remove DEPLOY_SETUP_KEY after use.
Route::get('/deploy/setup/{token}', DeploySetupController::class);

// Public Scores Page
Route::get('/scores', [ScoresWebController::class, 'index'])->name('scores.index');

// Redirect root to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Redirect /admin to admin login
Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

// Fallback login route to prevent RouteNotFoundException when auth middleware executes redirect
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Admin Authentication Routes (Guest)
Route::middleware(['web', 'security.headers'])->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login']);
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

// Admin Control Panel Routes (Protected by session auth & role checks)
Route::middleware(['web', 'auth', 'admin', 'security.headers'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users & Device Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/devices/{device}/toggle-block', [UserController::class, 'toggleBlockDevice'])->name('devices.toggle-block');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Channel Categories Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // Packages (الباقات) Management
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
    Route::post('/packages/reorder', [PackageController::class, 'reorder'])->name('packages.reorder');
    Route::get('/packages/by-category', [PackageController::class, 'getByCategory'])->name('packages.by-category');

    // Live Streaming Channels Management
    Route::get('/channels', [ChannelController::class, 'index'])->name('channels.index');
    Route::get('/channels/create', [ChannelController::class, 'create'])->name('channels.create');
    Route::get('/channels/import', [ChannelController::class, 'showImport'])->name('channels.import');
    Route::post('/channels/import', [ChannelController::class, 'import'])->name('channels.import.store');
    Route::delete('/channels/destroy-all', [ChannelController::class, 'destroyAll'])->name('channels.destroy-all');
    Route::post('/channels', [ChannelController::class, 'store'])->name('channels.store');
    Route::get('/channels/{channel}/edit', [ChannelController::class, 'edit'])->name('channels.edit');
    Route::put('/channels/{channel}', [ChannelController::class, 'update'])->name('channels.update');
    Route::delete('/channels/{channel}', [ChannelController::class, 'destroy'])->name('channels.destroy');
    Route::post('/channels/reorder', [ChannelController::class, 'reorder'])->name('channels.reorder');

    // License Activation Codes Management
    Route::get('/codes', [ActivationCodeController::class, 'index'])->name('codes.index');
    Route::get('/codes/generate', [ActivationCodeController::class, 'create'])->name('codes.create');
    Route::post('/codes/generate', [ActivationCodeController::class, 'store'])->name('codes.store');
    Route::post('/codes/{id}/reset-device', [ActivationCodeController::class, 'resetDevice'])->name('codes.reset-device');
    Route::post('/codes/{id}/revoke', [ActivationCodeController::class, 'revoke'])->name('codes.revoke');
    Route::post('/codes/{id}/extend', [ActivationCodeController::class, 'extend'])->name('codes.extend');

    // Active & Expired Subscriptions Management
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/{id}/extend', [SubscriptionController::class, 'extend'])->name('subscriptions.extend');
    Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    // Tournament Management
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
    Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('/tournaments/{tournament}/edit', [TournamentController::class, 'edit'])->name('tournaments.edit');
    Route::put('/tournaments/{tournament}', [TournamentController::class, 'update'])->name('tournaments.update');
    Route::delete('/tournaments/{tournament}', [TournamentController::class, 'destroy'])->name('tournaments.destroy');

    // Live Matches Management
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/create', [MatchController::class, 'create'])->name('matches.create');
    Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
    Route::delete('/matches/destroy-all', [MatchController::class, 'destroyAll'])->name('matches.destroy-all');
    Route::get('/matches/{match}/edit', [MatchController::class, 'edit'])->name('matches.edit');
    Route::put('/matches/{match}', [MatchController::class, 'update'])->name('matches.update');
    Route::delete('/matches/{match}', [MatchController::class, 'destroy'])->name('matches.destroy');

    // Movies & Series Management
    Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
    Route::get('/movies/create', [MovieController::class, 'create'])->name('movies.create');
    Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');
    Route::get('/movies/{movie}/edit', [MovieController::class, 'edit'])->name('movies.edit');
    Route::put('/movies/{movie}', [MovieController::class, 'update'])->name('movies.update');
    Route::delete('/movies/{movie}', [MovieController::class, 'destroy'])->name('movies.destroy');

    // HAR Analyzer
    Route::get('/har-analyzer', [HarAnalyzerController::class, 'index'])->name('har.index');
    Route::post('/har-analyzer/upload', [HarAnalyzerController::class, 'analyze'])->name('har.analyze');
    Route::post('/har-analyzer/distribute', [HarAnalyzerController::class, 'distribute'])->name('har.distribute');
    Route::get('/har-analyzer/check-link', [HarAnalyzerController::class, 'checkLink'])->name('har.check-link');
    Route::get('/har-analyzer/player', [HarAnalyzerController::class, 'player'])->name('har.player');
    Route::get('/har-analyzer/stream-proxy', [HarAnalyzerController::class, 'streamProxy'])->name('har.stream-proxy');
    Route::post('/har-analyzer/quick-distribute', [HarAnalyzerController::class, 'quickDistribute'])->name('har.quick-distribute');
    Route::post('/har-analyzer/create-channel-ajax', [HarAnalyzerController::class, 'createChannelAjax'])->name('har.create-channel-ajax');

    // Generic Bulk Delete Route
    Route::post('/bulk-delete', [DashboardController::class, 'bulkDelete'])->name('bulk-delete');

    // Packages & WhatsApp Settings
    Route::get('/settings/packages', [SettingController::class, 'packages'])->name('settings.packages');
    Route::post('/settings/packages', [SettingController::class, 'updatePackages'])->name('settings.packages.update');

    // Ads Settings
    Route::get('/settings/ads', [SettingController::class, 'ads'])->name('settings.ads');
    Route::post('/settings/ads', [SettingController::class, 'updateAds'])->name('settings.ads.update');

    Route::get('/settings/app', [SettingController::class, 'app'])->name('settings.app');
    Route::post('/settings/app', [SettingController::class, 'updateApp'])->name('settings.app.update');

    // PRO Activation
    Route::get('/pro-activation', [ProActivationController::class, 'index'])->name('pro-activation.index');
    Route::post('/pro-activation/activate', [ProActivationController::class, 'activate'])->name('pro-activation.activate');

});

// API endpoint to dynamically update stream URLs for expiring/DRM channels
Route::post('/api/streams/update-url', function(\Illuminate\Http\Request $request) {
    $token = $request->header('X-Update-Token');
    if ($token !== 'my-super-secret-update-token-123456') {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    
    $channelId = $request->input('channel_id');
    $streamUrl = $request->input('stream_url');
    $drmLicenseUrl = $request->input('drm_license_url');
    
    if (empty($channelId) || empty($streamUrl)) {
        return response()->json(['success' => false, 'message' => 'Missing parameters'], 400);
    }
    
    $channel = \App\Models\Channel::find($channelId);
    if (!$channel) {
        $channel = \App\Models\Channel::where('name', $channelId)->first();
    }
    
    if (!$channel) {
        return response()->json(['success' => false, 'message' => 'Channel not found'], 404);
    }
    
    // Encrypt stream URL using EncryptionService (if not dummy)
    if ($streamUrl !== 'dummy') {
        $encryptionService = resolve(\App\Services\EncryptionService::class);
        $channel->stream_url = $encryptionService->encrypt($streamUrl);
    }
    
    if (!empty($drmLicenseUrl)) {
        $channel->drm_license_url = $drmLicenseUrl;
    }
    
    $channel->save();
    
    // Store the update timestamp in cache to help proxy wait during reload gap
    \Illuminate\Support\Facades\Cache::put('channel_token_time_' . $channel->id, time(), 3600);
    
    return response()->json(['success' => true, 'message' => 'Channel stream URL updated successfully']);
});

// DASH stream proxy to bypass IP locking and account concurrency limits
Route::get('/stream-proxy/{channel_id}/{any?}', [\App\Http\Controllers\StreamProxyController::class, 'proxy'])->where('any', '.*');

// Catch-all for segment requests with absolute CDN paths (e.g. /Content/Channel/.../stream_07/segment.m4s)
// ExoPlayer sometimes resolves segment paths from MPD manifest as absolute paths against the server host
Route::get('/Content/{any}', [\App\Http\Controllers\StreamProxyController::class, 'proxyCdnPath'])->where('any', '.*');
