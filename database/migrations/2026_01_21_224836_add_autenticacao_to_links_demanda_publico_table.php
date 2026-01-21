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
            $table->string('token_acesso', 5)->nullable()->after('token'); // Token de 5 dígitos alfanuméricos
            $table->string('cpf_cnpj_autorizado', 18)->nullable()->after('token_acesso'); // CPF/CNPJ autorizado
            $table->timestamp('token_gerado_em')->nullable()->after('cpf_cnpj_autorizado'); // Quando o token foi gerado
            $table->timestamp('autenticado_em')->nullable()->after('token_gerado_em'); // Quando foi autenticado
            $table->string('sessao_id', 64)->nullable()->after('autenticado_em'); // ID da sessão autenticada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links_demanda_publico', function (Blueprint $table) {
            $table->dropColumn(['token_acesso', 'cpf_cnpj_autorizado', 'token_gerado_em', 'autenticado_em', 'sessao_id']);
        });
    }
};
