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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nome');
            $table->string('cor', 7)->default('#3B82F6'); // Cor em hexadecimal (ex: #3B82F6)
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['prestador', 'condominio', 'ambos'])->default('ambos');
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0); // Para ordenação personalizada
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('empresa_id');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
