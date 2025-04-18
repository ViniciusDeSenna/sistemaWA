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
        Schema::create('collaborators', function($table){
            $table->id();
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('city')->nullable();
            $table->boolean('intermittent_contract')->default(false);
            $table->boolean('is_leader')->default(false);
            $table->boolean('is_extra')->default(false);
            $table->text('observation')->nullable();
            $table->string('pix_key')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
