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
        Schema::create('negociacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orcamento_id')->constrained('orcamentos')->onDelete('cascade');
            $table->foreignId('demanda_id')->constrained('demandas')->onDelete('cascade');
            $table->foreignId('prestador_id')->constrained('prestadores')->onDelete('cascade');
            $table->enum('tipo', ['desconto', 'parcelamento', 'contraproposta'])->default('desconto');
            $table->decimal('valor_original', 15, 2); // Valor original do orçamento
            $table->decimal('valor_solicitado', 15, 2)->nullable(); // Valor solicitado (desconto, contraproposta ou valor parcelado) - será definido pelo prestador para desconto/parcelamento
            $table->integer('parcelas')->nullable(); // Número de parcelas (para tipo parcelamento)
            $table->enum('status', ['pendente', 'aceita', 'recusada'])->default('pendente');
            $table->text('mensagem_solicitacao')->nullable(); // Mensagem da empresa solicitando negociação
            $table->text('mensagem_resposta')->nullable(); // Resposta do prestador
            $table->timestamp('respondido_em')->nullable();
            $table->foreignId('criado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['orcamento_id', 'status']);
            $table->index(['demanda_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negociacoes');
    }
};
