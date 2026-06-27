<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('channel_servers', function (Blueprint $table) {
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete()->after('channel_id');
        });
    }

    public function down(): void
    {
        Schema::table('channel_servers', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropColumn('provider_id');
        });
    }
};
