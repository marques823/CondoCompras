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
            $table->string('whatsapp')->nullable()->after('nome_prestador');
            $table->string('cpf_cnpj_autorizado')->nullable()->change();
            $table->string('token_acesso')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links_demanda_publico', function (Blueprint $table) {
            //
        });
    }
};
