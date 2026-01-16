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
        Schema::table('condominios', function (Blueprint $table) {
            // Campos já existem, apenas adicionamos índices para melhor performance nas buscas
            $table->index('bairro');
            $table->index('cidade');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            $table->dropIndex(['bairro']);
            $table->dropIndex(['cidade']);
            $table->dropIndex(['estado']);
        });
    }
};
