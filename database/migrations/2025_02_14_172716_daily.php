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
            $table->foreignId('collaborator_id');
            $table->foreign('collaborator_id')->references('id')->on('collaborators');
            $table->foreignId('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('category');
            $table->date('date');
            $table->timestamp('start');
            $table->timestamp('end');
            $table->time('total_time');
            $table->float('hourly_rate');
            $table->float('costs');
            $table->text('costs_description');
            $table->float('addition');
            $table->text('addition_description');
            $table->float('total');
            $table->string('pix-key');
            $table->text('observation');
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
