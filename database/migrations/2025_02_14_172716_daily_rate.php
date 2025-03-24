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

            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->time('total_time')->nullable();

            $table->decimal('hourly_rate', 8, 2)->default(0)->nullable();

            $table->decimal('pay_amount', 8, 2)->default(0)->nullable();
            $table->decimal('transportation', 8, 2)->default(0)->nullable();
            $table->decimal('feeding', 8, 2)->default(0)->nullable();
            $table->decimal('leader_comission', 8, 2)->default(0)->nullable();

            $table->decimal('addition', 8, 2)->default(0)->nullable();
            
            $table->float('inss_paid')->nullable();
            $table->float('tax_paid')->nullable();
            
            $table->decimal('earned', 8, 2)->default(0)->nullable();
            $table->decimal('profit', 8, 2)->default(0)->nullable();

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
