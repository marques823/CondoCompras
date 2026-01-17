<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Redefinir Senha do Admin ===\n\n";

// Buscar admin existente
$admin = User::where('perfil', 'admin')->first();

if (!$admin) {
    echo "ERRO: Nenhum usuário admin encontrado!\n";
    exit(1);
}

echo "Admin encontrado:\n";
echo "Nome: {$admin->name}\n";
echo "Email: {$admin->email}\n\n";

// Nova senha (você pode alterar aqui ou passar como argumento)
$novaSenha = $argv[1] ?? 'admin123456';

if (strlen($novaSenha) < 8) {
    echo "ERRO: A senha deve ter no mínimo 8 caracteres!\n";
    exit(1);
}

$admin->password = Hash::make($novaSenha);
$admin->save();

echo "✓ Senha redefinida com sucesso!\n\n";
echo "Dados de acesso:\n";
echo "Email: {$admin->email}\n";
echo "Nova senha: {$novaSenha}\n\n";
echo "⚠️  IMPORTANTE: Altere a senha após o primeiro login!\n";
echo "\nPara usar uma senha personalizada, execute:\n";
echo "php reset_admin_password.php 'sua_senha_aqui'\n";
