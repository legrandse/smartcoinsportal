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
        Schema::create('hopper_levels', function (Blueprint $table) {
		    $table->integer('channel')->primary();
		    $table->integer('denomination_level');
		    $table->integer('value_cent');
		    $table->decimal('value_eur', 6, 2);
		    $table->string('country_code', 3);
		    $table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hopper_levels');
    }
};
