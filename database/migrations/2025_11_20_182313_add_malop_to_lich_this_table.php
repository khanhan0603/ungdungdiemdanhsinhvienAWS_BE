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
        Schema::table('lich_this', function (Blueprint $table) {
             $table->string('malop')->after('phongthi')->nullable();
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
        Schema::table('lich_this', function (Blueprint $table) {
             $table->dropForeign(['malop']);
            $table->dropColumn('malop');

        });
    }
};
