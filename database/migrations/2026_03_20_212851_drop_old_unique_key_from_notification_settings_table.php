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
            // Removendo o índice único antigo de 'key' que impede múltiplos registros da mesma chave para empresas diferentes
            if (Schema::hasTable('notification_settings')) {
                // No SQLite, o Laravel às vezes nomeia o índice como o nome da tabela + coluna + _unique
                // A verificação via PRAGMA confirmou que o nome é notification_settings_key_unique
                $table->dropUnique(['key']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->unique('key');
        });
    }
};
