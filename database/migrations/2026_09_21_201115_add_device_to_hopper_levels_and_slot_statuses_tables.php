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
        Schema::table('hopper_levels', function (Blueprint $table) {
            $table->string('device')->nullable();
        });

       
    }

    public function down(): void
    {
        Schema::table('hopper_levels', function (Blueprint $table) {
            $table->dropColumn('device');
        });

       
    }
};
