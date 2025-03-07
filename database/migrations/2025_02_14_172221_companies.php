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
        Schema::create('companies', function($table){
            $table->id();
            $table->string('name');
            $table->string('document')->nullable();
            $table->float('time_value')->default(0)->nullable();
            $table->string('category')->default('indefinido')->nullable();
            $table->string('chain_of_stores')->default('indefinido')->nullable();
            $table->string('observation')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
