<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_filters', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique();
            $table->enum('action', ['hide', 'delete', 'ignore'])->default('hide');
            $table->enum('type', ['channel', 'movie', 'series', 'all'])->default('all');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_filters');
    }
};
