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
        Schema::create("company_has_section", function (Blueprint $table) {
            $table->id();
            
            
            $table->foreignId('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            
            $table->foreignId('section_id')->nullable();
            $table->foreign('section_id')->references('id')->on('sections');

            $table->float('earned')->nullable();

            $table->float('employeePay')->nullable();
            $table->float('extra')->nullable();
            $table->float('leaderPay')->nullable();

            $table->float('inss_paid')->nullable();
            $table->float('tax_paid')->nullable();

            $table->float('leaderComission')->default(8.0);
            $table->boolean('perHour')->default(false);
            $table->boolean('active')->default(true);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
