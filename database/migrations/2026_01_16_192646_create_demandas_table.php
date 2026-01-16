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
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('condominio_id')->constrained('condominios')->onDelete('cascade');
            $table->foreignId('categoria_servico_id')->nullable()->constrained('categorias_servicos')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('status', ['aberta', 'em_andamento', 'aguardando_orcamento', 'concluida', 'cancelada'])->default('aberta');
            $table->date('prazo_limite')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'condominio_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandas');
    }
};
