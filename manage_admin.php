<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Gerenciar Usuário Admin ===\n\n";

// Verificar admin existente
$admin = User::where('perfil', 'admin')->first();

if ($admin) {
    echo "Usuário Admin encontrado:\n";
    echo "Nome: {$admin->name}\n";
    echo "Email: {$admin->email}\n";
    echo "ID: {$admin->id}\n\n";
    
    echo "Opções:\n";
    echo "1. Redefinir senha do admin existente\n";
    echo "2. Criar novo usuário admin\n";
    echo "3. Sair\n\n";
    
    $opcao = readline("Escolha uma opção (1-3): ");
    
    if ($opcao == '1') {
        $novaSenha = readline("Digite a nova senha: ");
        if (strlen($novaSenha) < 8) {
            echo "ERRO: A senha deve ter no mínimo 8 caracteres!\n";
            exit(1);
        }
        
        $admin->password = Hash::make($novaSenha);
        $admin->save();
        
        echo "\n✓ Senha redefinida com sucesso!\n";
        echo "Email: {$admin->email}\n";
        echo "Nova senha: {$novaSenha}\n\n";
    } elseif ($opcao == '2') {
        $name = readline("Nome do novo admin: ");
        $email = readline("Email do novo admin: ");
        $password = readline("Senha do novo admin: ");
        
        if (strlen($password) < 8) {
            echo "ERRO: A senha deve ter no mínimo 8 caracteres!\n";
            exit(1);
        }
        
        if (User::where('email', $email)->exists()) {
            echo "ERRO: Este email já está cadastrado!\n";
            exit(1);
        }
        
        $newAdmin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'perfil' => 'admin',
            'empresa_id' => null,
            'condominio_id' => null,
        ]);
        
        echo "\n✓ Novo usuário admin criado com sucesso!\n";
        echo "Nome: {$newAdmin->name}\n";
        echo "Email: {$newAdmin->email}\n";
        echo "Senha: {$password}\n\n";
    }
} else {
    echo "Nenhum admin encontrado. Criando primeiro admin...\n\n";
    
    $name = readline("Nome do admin: ");
    $email = readline("Email do admin: ");
    $password = readline("Senha do admin: ");
    
    if (strlen($password) < 8) {
        echo "ERRO: A senha deve ter no mínimo 8 caracteres!\n";
        exit(1);
    }
    
    $admin = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'perfil' => 'admin',
        'empresa_id' => null,
        'condominio_id' => null,
    ]);
    
    echo "\n✓ Usuário admin criado com sucesso!\n";
    echo "Email: {$admin->email}\n";
    echo "Senha: {$password}\n\n";
}

echo "⚠️  IMPORTANTE: Altere a senha após o primeiro login!\n";
