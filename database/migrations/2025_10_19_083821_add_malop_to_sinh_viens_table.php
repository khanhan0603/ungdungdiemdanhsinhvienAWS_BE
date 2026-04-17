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
        Schema::table('sinh_viens', function (Blueprint $table) {
             $table->string('malop')->after('ngaysinh')->nullable();
            $table->foreign('malop')
                    ->references('malop')
                    ->on('lops')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinh_viens', function (Blueprint $table) {
            $table->dropForeign(['malop']);
            $table->dropColumn('malop');
        });
    }
};
