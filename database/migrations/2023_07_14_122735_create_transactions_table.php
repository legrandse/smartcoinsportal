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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('device_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('status')->nullable();
            $table->float('amount')->nullable();
			$table->float('inserted_amount')->nullable();
            $table->string('reference')->nullable();
			$table->string('debtor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
