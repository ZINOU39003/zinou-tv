<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedBigInteger('scores_game_id')->nullable()->after('external_id');
            $table->unsignedBigInteger('channel_id')->nullable()->after('stream_url');

            $table->index('scores_game_id');
            $table->index('channel_id');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['scores_game_id']);
            $table->dropIndex(['channel_id']);
            $table->dropColumn(['scores_game_id', 'channel_id']);
        });
    }
};
