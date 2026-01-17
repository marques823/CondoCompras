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
        // 1. Renomear tabela empresas para administradoras
        if (Schema::hasTable('empresas') && !Schema::hasTable('administradoras')) {
            Schema::rename('empresas', 'administradoras');
        }

        // 2. Criar tabelas de Roles e Permissions (Estrutura simples para RBAC)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'admin', 'administradora', 'gerente', 'zelador'
            $table->string('label')->nullable(); // Nome amigável
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'create_user', 'manage_condos'
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'user_id']);
        });

        // 3. Renomear colunas empresa_id para administradora_id em todas as tabelas
        $tablesWithEmpresaId = [
            'users',
            'condominios',
            'demandas',
            'documentos',
            'prestadores',
            'tags',
            'links_condominio',
            'negociacoes',
            'orcamentos',
        ];

        foreach ($tablesWithEmpresaId as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Remove FK antiga se existir (o nome padrão do Laravel costuma ser tabela_coluna_foreign)
                    // Mas vamos apenas renomear a coluna e depois tratar as FKs se necessário.
                    // Em muitos casos, o SQLite ou MySQL aceitam renameColumn.
                    if (Schema::hasColumn($tableName, 'empresa_id')) {
                        $table->renameColumn('empresa_id', 'administradora_id');
                    }
                });
            }
        }

        // 4. Criar a tabela zeladores (conforme pedido, além de estarem em users)
        Schema::create('zeladores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('condominio_id')->constrained()->onDelete('cascade');
            $table->foreignId('administradora_id')->constrained()->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Adicionar gerente_id em condominios se não existir
        if (Schema::hasTable('condominios')) {
            Schema::table('condominios', function (Blueprint $table) {
                if (!Schema::hasColumn('condominios', 'gerente_id')) {
                    $table->foreignId('gerente_id')->nullable()->after('administradora_id')->constrained('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('condominios', function (Blueprint $table) {
            $table->dropForeign(['gerente_id']);
            $table->dropColumn('gerente_id');
        });

        Schema::dropIfExists('zeladores');

        $tablesWithAdministradoraId = [
            'users',
            'condominios',
            'demandas',
            'documentos',
            'prestadores',
            'tags',
            'links_condominio',
            'negociacoes',
            'orcamentos',
        ];

        foreach ($tablesWithAdministradoraId as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'administradora_id')) {
                        $table->renameColumn('administradora_id', 'empresa_id');
                    }
                });
            }
        }

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        if (Schema::hasTable('administradoras')) {
            Schema::rename('administradoras', 'empresas');
        }
    }
};
