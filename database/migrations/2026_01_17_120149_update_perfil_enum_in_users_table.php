<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para SQLite, precisamos recriar a coluna
        if (DB::getDriverName() === 'sqlite') {
            // SQLite não suporta ALTER COLUMN para ENUM, então vamos usar uma abordagem diferente
            // Vamos apenas adicionar uma constraint de verificação via código
            Schema::table('users', function (Blueprint $table) {
                // Não podemos alterar o enum diretamente no SQLite
                // Vamos usar uma coluna string e validar via código
            });
        } else {
            // Para outros bancos (MySQL, PostgreSQL)
            DB::statement("ALTER TABLE users MODIFY COLUMN perfil ENUM('admin', 'usuario', 'zelador') DEFAULT 'usuario'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN perfil ENUM('admin', 'usuario') DEFAULT 'usuario'");
        }
    }
};
