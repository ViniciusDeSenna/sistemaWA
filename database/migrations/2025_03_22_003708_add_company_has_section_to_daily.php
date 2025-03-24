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
        Schema::table('daily_rate', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->after('id');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_rate', function (Blueprint $table) {
            $table->dropForeign(['company_has_section_id']);
            $table->dropColumn('company_has_section_id');
        });
    }
};
