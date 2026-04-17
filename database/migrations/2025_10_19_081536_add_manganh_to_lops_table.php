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
        Schema::table('lops', function (Blueprint $table) {
              $table->string('manganh');
            $table->foreign('manganh')
                    ->references('manganh')
                    ->on('nganhs')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lops', function (Blueprint $table) {
            $table->dropForeign(['manganh']);
            $table->dropColumn('manganh');
        });
    }
};
