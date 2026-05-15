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
	    Schema::table('users', function (Blueprint $table) {
	        $table->string('firstname')->after('name')->nullable();
	        $table->string('street')->after('password')->nullable();
	        $table->string('postcode')->after('street')->nullable();
	        $table->string('city')->after('postcode')->nullable();
	        $table->string('country')->after('city')->nullable();
	        $table->string('company')->after('country')->nullable();
	        $table->string('VAT')->after('company')->nullable();
	        
	    });
	}

	public function down(): void
	{
	    Schema::table('users', function (Blueprint $table) {
	        $table->dropColumn(['firstname', 'street', 'postcode', 'city', 'country', 'VAT', 'company', 'is_admin']);
	    });
	}
};
