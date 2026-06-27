<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('channel_servers', function (Blueprint $table) {
            $table->timestamp('last_check_time')->nullable()->after('is_active');
            $table->enum('status', ['online', 'offline', 'untested'])->default('untested')->after('last_check_time');
            $table->integer('response_time_ms')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('channel_servers', function (Blueprint $table) {
            $table->dropColumn(['last_check_time', 'status', 'response_time_ms']);
        });
    }
};
