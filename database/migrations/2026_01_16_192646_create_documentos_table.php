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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('condominio_id')->nullable()->constrained('condominios')->onDelete('cascade');
            $table->foreignId('demanda_id')->nullable()->constrained('demandas')->onDelete('cascade');
            $table->foreignId('orcamento_id')->nullable()->constrained('orcamentos')->onDelete('cascade');
            $table->foreignId('prestador_id')->nullable()->constrained('prestadores')->onDelete('cascade');
            $table->enum('tipo', ['nota_fiscal', 'boleto', 'comprovante', 'orcamento_pdf', 'outro'])->default('outro');
            $table->string('nome_original');
            $table->string('nome_arquivo');
            $table->string('caminho');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable(); // em bytes
            $table->date('data_documento')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'condominio_id']);
            $table->index(['tipo', 'data_documento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
