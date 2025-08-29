<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_branches', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('phone', 30);
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_branches');
    }
};
