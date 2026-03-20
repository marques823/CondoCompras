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
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('administradora_id')->nullable()->after('id');
            $table->foreign('administradora_id')->references('id')->on('administradoras')->onDelete('cascade');
            
            // Removendo o índice único antigo de 'key' se existir, para permitir a mesma chave em empresas diferentes
            // Nota: Se você não criou um índice único nominal, o Laravel pode ter criado um automático.
            // Vou garantir que a combinação (key, administradora_id) seja única.
            $table->unique(['key', 'administradora_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropForeign(['administradora_id']);
            $table->dropUnique(['key', 'administradora_id']);
            $table->dropColumn('administradora_id');
        });
    }
};
