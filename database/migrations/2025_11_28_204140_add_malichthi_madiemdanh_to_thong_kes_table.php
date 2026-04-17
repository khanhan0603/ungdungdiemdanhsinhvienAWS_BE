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
        Schema::table('thong_kes', function (Blueprint $table) {
            $table->string('malichthi')->nullable();
            $table->string('madiemdanh')->nullable();
            $table->foreign('malichthi')
                    ->references('malichthi')
                    ->on('lich_this')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('madiemdanh')
                    ->references('madiemdanh')
                    ->on('diem_danhs')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thong_kes', function (Blueprint $table) {
            $table->dropForeign(['malichthi']);
            $table->dropForeign(['madiemdanh']);
            $table->dropColumn('malichthi');
            $table->dropColumn('madiemdanh');
        });
    }
};
