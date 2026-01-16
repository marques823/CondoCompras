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
        Schema::create('prestador_categoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestador_id')->constrained('prestadores')->onDelete('cascade');
            $table->foreignId('categoria_servico_id')->constrained('categorias_servicos')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['prestador_id', 'categoria_servico_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestador_categoria');
    }
};
