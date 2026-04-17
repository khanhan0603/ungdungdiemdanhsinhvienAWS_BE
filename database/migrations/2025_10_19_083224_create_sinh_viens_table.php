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
        Schema::create('sinh_viens', function (Blueprint $table) {
            $table->string('masv')->primary();
            $table->string('hoten')->nullable();
            $table->string('gioitinh')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('sdt')->unique()->nullable();
            $table->date('ngaysinh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinh_viens');
    }
};
