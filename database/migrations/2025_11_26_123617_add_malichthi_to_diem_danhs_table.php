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
        Schema::table('diem_danhs', function (Blueprint $table) {
            $table->string('malichthi')->after('madiemdanh')->nullable();
            $table->foreign('malichthi')
                    ->references('malichthi')
                    ->on('lich_this')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diem_danhs', function (Blueprint $table) {
           $table->dropForeign(['malichthi']);
            $table->dropColumn('malichthi');
        });
    }
};
