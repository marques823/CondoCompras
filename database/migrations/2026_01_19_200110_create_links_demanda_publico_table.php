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
        Schema::create('links_demanda_publico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demanda_id')->constrained('demandas')->onDelete('cascade');
            $table->foreignId('administradora_id')->constrained('administradoras')->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->boolean('ativo')->default(true);
            $table->integer('acessos')->default(0); // Contador de acessos ao link
            $table->timestamp('expira_em')->nullable(); // Data de expiração (opcional)
            $table->timestamps();
            
            $table->index(['token', 'ativo']);
            $table->index(['demanda_id', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links_demanda_publico');
    }
};
