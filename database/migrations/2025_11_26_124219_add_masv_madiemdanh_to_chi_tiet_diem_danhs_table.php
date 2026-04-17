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
           $table->string('masv', 100);
            $table->string('madiemdanh', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_diem_danhs', function (Blueprint $table) {
            $table->dropColumn('masv');
            $table->dropColumn('madiemdanh');
        });
    }
};
