# ⚙️ Configuração do Sistema

## 📝 Configurações Necessárias

### 1. Variáveis de Ambiente (.env)

Adicione ou verifique as seguintes variáveis no arquivo `.env`:

```env
# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=condocompras
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Aplicação
APP_NAME="Sistema de Gestão de Condomínios"
APP_ENV=local
APP_KEY=base64:... (gerado automaticamente)
APP_DEBUG=true
APP_URL=http://localhost:8000

# Sanctum (API)
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,127.0.0.1:8000

# Storage
FILESYSTEM_DISK=local
# Para produção, use:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=
# AWS_BUCKET=
```

### 2. Storage de Arquivos

O sistema armazena documentos em `storage/app/public/documentos/`.

Para que os arquivos sejam acessíveis publicamente:

```bash
php artisan storage:link
```

### 3. Permissões

Certifique-se de que as pastas têm as permissões corretas:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Middlewares

Os middlewares já estão configurados nas rotas. Se necessário registrar globalmente, edite `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'empresa' => \App\Http\Middleware\EnsureUserBelongsToEmpresa::class,
        'api.token' => \App\Http\Middleware\EnsureApiToken::class,
    ]);
})
```

### 5. Sanctum

O Sanctum já está configurado. Para criar tokens de API:

```php
$user = User::find(1);
$token = $user->createToken('n8n-integration')->plainTextToken;
```

### 6. Primeiro Usuário

Para criar o primeiro usuário e empresa, você pode usar um seeder ou criar manualmente:

```php
php artisan tinker

$empresa = \App\Models\Empresa::create([
    'nome' => 'Minha Administradora',
    'email' => 'admin@exemplo.com',
    'cnpj' => '12345678000190',
    'ativo' => true,
]);

$user = \App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@exemplo.com',
    'password' => bcrypt('senha123'),
    'empresa_id' => $empresa->id,
    'perfil' => 'admin',
]);
```

### 7. Categorias e Regiões Iniciais

Crie categorias de serviços e regiões através do sistema ou via seeder:

```php
php artisan make:seeder CategoriasServicosSeeder
php artisan make:seeder RegioesSeeder
```

## 🔧 Configurações Avançadas

### Rate Limiting

Configure rate limiting para a API em `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

### Validação de Uploads

Os uploads são validados nos controllers. Para ajustar tamanhos máximos, edite `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
```

### Expiração de Links

A expiração padrão dos links de prestadores é de 30 dias. Para alterar, edite o método `gerarLinksParaDemanda` em `LinkPrestadorController`:

```php
'expira_em' => now()->addDays(30), // Altere aqui
```

## 🚀 Produção

### Checklist de Produção

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Configurar HTTPS
- [ ] Configurar storage em S3 ou similar
- [ ] Configurar fila para processamento assíncrono
- [ ] Configurar backups automáticos do banco
- [ ] Configurar monitoramento e logs
- [ ] Configurar rate limiting adequado
- [ ] Revisar permissões de arquivos
- [ ] Configurar CORS adequadamente

### Otimizações

```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Otimizar autoloader
composer install --optimize-autoloader --no-dev
```

## 📧 Notificações

Para configurar notificações por email, configure o mailer no `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@exemplo.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🔐 Segurança Adicional

1. **2FA (Opcional)**: Implemente autenticação de dois fatores usando Laravel Fortify
2. **Criptografia de Dados**: Use `encrypted` cast nos models para dados sensíveis
3. **HTTPS**: Sempre use HTTPS em produção
4. **Headers de Segurança**: Configure headers adequados no servidor web

---

**Última atualização**: Janeiro 2026
