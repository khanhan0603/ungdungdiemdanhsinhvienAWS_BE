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
               $table->foreign('magv')
              ->references('magv')->on('giang_viens')
              ->onDelete('cascade');

        $table->foreign('malichthi')
              ->references('malichthi')->on('lich_this')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phan_cong_lich_this', function (Blueprint $table) {
            $table->dropForeign(['magv']);
            $table->dropForeign(['malichthi']);
        });
    }
};
