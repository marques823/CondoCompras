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
        Schema::table('links_demanda_publico', function (Blueprint $table) {
            $table->string('nome_prestador', 255)->nullable()->after('cpf_cnpj_autorizado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links_demanda_publico', function (Blueprint $table) {
            $table->dropColumn('nome_prestador');
        });
    }
};
