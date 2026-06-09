<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->cascadeOnDelete();
            $table->string('team_one_name');
            $table->string('team_one_name_ar')->nullable();
            $table->string('team_one_flag')->nullable();
            $table->string('team_two_name');
            $table->string('team_two_name_ar')->nullable();
            $table->string('team_two_flag')->nullable();
            $table->integer('team_one_score')->default(0);
            $table->integer('team_two_score')->default(0);
            $table->string('match_time');
            $table->date('match_date')->nullable();
            $table->boolean('is_live')->default(false);
            $table->boolean('is_world_cup')->default(false);
            $table->text('stream_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('tournament_id');
            $table->index('is_live');
            $table->index('is_world_cup');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index('match_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
