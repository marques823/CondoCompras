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
        Schema::table('demandas', function (Blueprint $table) {
            $table->enum('urgencia', ['baixa', 'media', 'alta', 'critica'])->nullable()->after('status');
            $table->dropColumn('prazo_limite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandas', function (Blueprint $table) {
            $table->date('prazo_limite')->nullable();
            $table->dropColumn('urgencia');
        });
    }
};
