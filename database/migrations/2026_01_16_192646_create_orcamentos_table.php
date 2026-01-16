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
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demanda_id')->constrained('demandas')->onDelete('cascade');
            $table->foreignId('prestador_id')->constrained('prestadores')->onDelete('cascade');
            $table->foreignId('link_prestador_id')->nullable()->constrained('links_prestador')->onDelete('set null');
            $table->decimal('valor', 15, 2);
            $table->text('descricao')->nullable();
            $table->date('validade')->nullable();
            $table->enum('status', ['recebido', 'aprovado', 'rejeitado'])->default('recebido');
            $table->text('observacoes')->nullable();
            $table->text('motivo_rejeicao')->nullable();
            $table->foreignId('aprovado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('aprovado_em')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['demanda_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
    }
};
