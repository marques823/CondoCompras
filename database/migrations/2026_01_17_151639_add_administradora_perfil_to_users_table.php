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
        // Para SQLite, não podemos alterar ENUM diretamente
        // A validação será feita via código no model
        if (DB::getDriverName() !== 'sqlite') {
            // Para MySQL e outros bancos que suportam ENUM
            DB::statement("ALTER TABLE users MODIFY COLUMN perfil ENUM('admin', 'administradora', 'usuario', 'zelador') DEFAULT 'usuario'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN perfil ENUM('admin', 'usuario', 'zelador') DEFAULT 'usuario'");
        }
    }
};
