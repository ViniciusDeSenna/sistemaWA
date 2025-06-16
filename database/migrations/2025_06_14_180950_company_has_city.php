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
        Schema::create('company_has_city', function(Blueprint $table){
            $table->id();

            $table->foreignId('city_id')
                  ->constrained('city');

            $table->foreignId('company_id')
                  ->constrained('companies');
            $table->boolean('active')->default(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_has_city');
    }
};
