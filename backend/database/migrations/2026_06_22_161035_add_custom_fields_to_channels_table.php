<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->boolean('hidden_by_filter')->default(false)->after('is_active');
            $table->string('custom_name')->nullable()->after('name_ar');
            $table->string('custom_logo')->nullable()->after('logo_url');
            $table->unsignedBigInteger('custom_category_id')->nullable()->after('category_id');
            
            $table->foreign('custom_category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropForeign(['custom_category_id']);
            $table->dropColumn([
                'hidden_by_filter',
                'custom_name',
                'custom_logo',
                'custom_category_id'
            ]);
        });
    }
};
