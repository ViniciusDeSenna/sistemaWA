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
        Schema::table("daily_rate", function (Blueprint $table) {
            $table->decimal("quebra_caixa", 10, 2)->default(0.00)->after("profit");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("daily_rate", function (Blueprint $table) {
            $table->dropColumn("quebra_caixa");
        });
    }
};
