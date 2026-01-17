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
        Schema::create('links_condominio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominio_id')->constrained('condominios')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->string('titulo')->nullable(); // Título/descrição do link
            $table->boolean('ativo')->default(true);
            $table->integer('usos')->default(0); // Contador de quantas demandas foram criadas via este link
            $table->timestamp('expira_em')->nullable(); // Data de expiração (opcional)
            $table->timestamps();
            
            $table->index(['token', 'ativo']);
            $table->index(['condominio_id', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links_condominio');
    }
};
