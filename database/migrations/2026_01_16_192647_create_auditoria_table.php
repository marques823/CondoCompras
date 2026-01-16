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
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('modelo'); // Nome do modelo (ex: 'Demanda', 'Orcamento')
            $table->unsignedBigInteger('modelo_id')->nullable(); // ID do registro
            $table->string('acao'); // 'created', 'updated', 'deleted', 'viewed'
            $table->text('dados_anteriores')->nullable(); // JSON com dados antes da alteração
            $table->text('dados_novos')->nullable(); // JSON com dados após alteração
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index(['empresa_id', 'modelo', 'modelo_id']);
            $table->index(['usuario_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
