<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stream_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();
            $table->foreignId('channel_id')->constrained('channels')->cascadeOnDelete();
            $table->string('device_name')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamp('last_seen_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stream_sessions');
    }
};
