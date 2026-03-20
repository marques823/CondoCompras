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
        Schema::table('orcamentos', function (Blueprint $table) {
            // Remove campos individuais
            $table->dropColumn([
                'banco_nome',
                'banco_agencia',
                'banco_conta',
                'banco_tipo_conta',
                'banco_pix',
            ]);
            
            // Adiciona campo único de texto
            $table->text('dados_bancarios')->nullable()->after('observacoes_conclusao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orcamentos', function (Blueprint $table) {
            // Remove campo único
            $table->dropColumn('dados_bancarios');
            
            // Restaura campos individuais
            $table->string('banco_nome')->nullable()->after('observacoes_conclusao');
            $table->string('banco_agencia')->nullable()->after('banco_nome');
            $table->string('banco_conta')->nullable()->after('banco_agencia');
            $table->string('banco_tipo_conta')->nullable()->after('banco_conta');
            $table->string('banco_pix')->nullable()->after('banco_tipo_conta');
        });
    }
};
