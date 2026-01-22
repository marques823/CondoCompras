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
            $table->boolean('concluido')->default(false)->after('aprovado_em');
            $table->timestamp('concluido_em')->nullable()->after('concluido');
            $table->text('observacoes_conclusao')->nullable()->after('concluido_em');
            $table->string('banco_nome')->nullable()->after('observacoes_conclusao');
            $table->string('banco_agencia')->nullable()->after('banco_nome');
            $table->string('banco_conta')->nullable()->after('banco_agencia');
            $table->string('banco_tipo_conta')->nullable()->after('banco_conta'); // poupanca, corrente
            $table->string('banco_pix')->nullable()->after('banco_tipo_conta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orcamentos', function (Blueprint $table) {
            $table->dropColumn([
                'concluido',
                'concluido_em',
                'observacoes_conclusao',
                'banco_nome',
                'banco_agencia',
                'banco_conta',
                'banco_tipo_conta',
                'banco_pix',
            ]);
        });
    }
};
