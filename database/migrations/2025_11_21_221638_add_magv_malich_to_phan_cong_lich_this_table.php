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
        Schema::table('phan_cong_lich_this', function (Blueprint $table) {
            $table->string('magv', 100);
            $table->string('malichthi', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phan_cong_lich_this', function (Blueprint $table) {
            $table->dropColumn('magv');
            $table->dropColumn('malichthi');
        });
    }
};
