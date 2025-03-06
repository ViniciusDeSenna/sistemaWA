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
        Schema::create('daily_rate', function($table){
            $table->id();
            $table->foreignId('collaborator_id')->nullable();
            $table->foreign('collaborator_id')->references('id')->on('collaborators');
            $table->foreignId('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamp('start')->nullable();
            $table->timestamp('start_interval')->nullable();
            $table->timestamp('end_interval')->nullable();
            $table->timestamp('end')->nullable();
            $table->time('daily_total_time')->nullable();
            $table->float('hourly_rate')->nullable();
            $table->float('total_value')->nullable();
            $table->float('costs')->nullable();
            $table->text('costs_description')->nullable();
            $table->float('addition')->nullable();
            $table->text('addition_description')->nullable();
            $table->float('total')->nullable();
            $table->string('pix_key')->nullable();
            $table->text('observation')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_rate');
    }
};
