<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('poster_url')->nullable();
            $table->string('type')->default('movie');
            $table->text('stream_url')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->integer('year')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->boolean('is_latest')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('is_latest');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
