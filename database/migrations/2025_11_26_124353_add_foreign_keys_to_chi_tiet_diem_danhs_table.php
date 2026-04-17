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
        Schema::table('chi_tiet_diem_danhs', function (Blueprint $table) {
            $table->foreign('masv')
              ->references('masv')->on('sinh_viens')
              ->onDelete('cascade');

            $table->foreign('madiemdanh')
              ->references('madiemdanh')->on('diem_danhs')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_diem_danhs', function (Blueprint $table) {
            $table->dropForeign(['masv']);
            $table->dropForeign(['madiemdanh']);
        });
    }
};
