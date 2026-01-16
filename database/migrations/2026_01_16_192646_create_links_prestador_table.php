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
        Schema::create('links_prestador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demanda_id')->constrained('demandas')->onDelete('cascade');
            $table->foreignId('prestador_id')->constrained('prestadores')->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->timestamp('expira_em')->nullable();
            $table->boolean('usado')->default(false);
            $table->timestamp('usado_em')->nullable();
            $table->integer('acessos')->default(0);
            $table->timestamps();
            
            $table->index('token');
            $table->index(['demanda_id', 'prestador_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links_prestador');
    }
};
