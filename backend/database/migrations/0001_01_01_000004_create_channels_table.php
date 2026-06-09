<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('logo_url')->nullable();
            $table->text('stream_url');
            $table->string('stream_type')->default('m3u8');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('quality')->default('HD');
            $table->text('backup_url')->nullable();
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index('quality');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
