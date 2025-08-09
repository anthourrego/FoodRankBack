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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_product_id')->constrained('event_products');
            $table->foreignId('event_product_branch_id')->constrained('event_product_branches');
            $table->unsignedTinyInteger('rating')->check('rating >= 1 AND rating <= 5');
            $table->string('comment');
            $table->string('latitude', 100);
            $table->string('longitude', 100);
            $table->string('ip', 100);
            $table->string('mac', 150);
            $table->string('fingerprint_device')->unique();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->unique(['event_product_id', 'event_product_branch_id', 'ip', 'mac'], 'review_unique_constraint');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
