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
        Schema::create('city_has_collaborator', function(Blueprint $table){
            $table->id();
            
            $table->foreignId('city_id')
                  ->constrained('city')
                  ->onDelete('cascade');
            $table->foreignId('collaborator_id')
                  ->constrained('collaborators')
                  ->onDelete('cascade');

            $table->boolean('is_active');
            $table->timestamps();
        });
    }
            
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_has_collaborator');
    }
};
