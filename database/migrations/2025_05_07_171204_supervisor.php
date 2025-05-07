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
        Schema::table("company_has_section", function (Blueprint $table) {
            $table->decimal("supervisorPay")->default(0.00)->after("leaderComission");
        });

        Schema::table("collaborators", function (Blueprint $table) {
            $table->boolean("is_supervisor")->default(false)->after("is_leader");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("company_has_section", function (Blueprint $table) {
            $table->dropColumn("supervisorPay");
        });

        Schema::table("collaborators", function (Blueprint $table) {
            $table->dropColumn("is_supervisor");
        });
    }
};
