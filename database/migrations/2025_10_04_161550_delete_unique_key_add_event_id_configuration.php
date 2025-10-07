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
        //
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropUnique('configurations_key_unique');
            $table->foreignId('event_id')->constrained('events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('configurations', function (Blueprint $table) {
            $table->unique('key');
            $table->dropForeign(['event_id']);
        });
    }
};
