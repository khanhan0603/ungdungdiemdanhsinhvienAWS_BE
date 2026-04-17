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
        Schema::create('lich_this', function (Blueprint $table) {
           $table->string('malichthi')->primary();
            $table->string('monthi')->nullable();
            $table->date('ngaythi')->nullable();
            $table->time('giobatdau')->nullable();
            $table->time('gioketthuc')->nullable();
            $table->string('phongthi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_this');
    }
};
