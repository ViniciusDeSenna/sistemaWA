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
            $table->decimal("employee_discount", 10, 2)->default(0.00)->after("profit");
            $table->string("discount_description", 10, 2)->default(0.00)->after("profit");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("daily_rate", function (Blueprint $table) {
            $table->dropColumn("employee_discount");
            $table->dropColumn("discount_description");
        });
    }
};
