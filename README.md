# 🏢 Sistema de Gestão para Administradoras de Condomínios

Sistema SaaS desenvolvido em Laravel para gerenciar demandas, prestadores, orçamentos e documentos de condomínios.

## 📋 Características

- ✅ Multi-tenancy (isolamento por empresa)
- ✅ Gestão de condomínios
- ✅ Gestão de prestadores de serviço
- ✅ Sistema de demandas
- ✅ Links únicos seguros para prestadores
- ✅ Recebimento de orçamentos
- ✅ Upload e organização de documentos
- ✅ API REST completa para integração com n8n
- ✅ Sistema de auditoria completo
- ✅ Autenticação segura (Breeze + Sanctum)

## 🚀 Instalação

### Pré-requisitos

- PHP >= 8.2
- Composer
- Node.js >= 20.19 ou >= 22.12
- PostgreSQL ou MySQL
- NPM
- Git

### Passos de Instalação

1. **Clone o repositório**
```bash
git clone <repository-url>
cd condocompras
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Instale as dependências Node**
```bash
npm install
```

4. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados no `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=condocompras
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

6. **Execute as migrations**
```bash
php artisan migrate
```

7. **Crie o link simbólico para storage**
```bash
php artisan storage:link
```

8. **Configure as permissões**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

9. **Compile os assets**
```bash
npm run build
```

10. **Inicie o servidor (desenvolvimento)**
```bash
php artisan serve
```

Acesse: `http://localhost:8000`

## 🏭 Configuração para Produção

### 1. Configurar Variáveis de Ambiente

Edite o arquivo `.env` e configure:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
LOG_LEVEL=error

# Configure outras variáveis conforme necessário
SESSION_DRIVER=database
CACHE_DRIVER=file
```

### 2. Otimizar para Produção

```bash
# Limpar caches antigos
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Criar caches de produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Configurar Servidor como Serviço Systemd

#### 3.1. Criar arquivo de serviço

Crie o arquivo `/etc/systemd/system/condocompras.service`:

```ini
[Unit]
Description=CondoCompras Laravel Application
After=network.target

[Service]
Type=simple
User=seu_usuario
WorkingDirectory=/caminho/para/condocompras
ExecStart=/usr/bin/php artisan serve --host=0.0.0.0 --port=PORTA_DESEJADA
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal
SyslogIdentifier=condocompras

[Install]
WantedBy=multi-user.target
```

**Importante**: Substitua:
- `seu_usuario`: pelo usuário do sistema que executará o serviço
- `/caminho/para/condocompras`: pelo caminho absoluto do projeto
- `PORTA_DESEJADA`: pela porta desejada (ex: 8000, 7899, etc.)

#### 3.2. Configurar e iniciar o serviço

```bash
# Recarregar configurações do systemd
sudo systemctl daemon-reload

# Habilitar para iniciar automaticamente no boot
sudo systemctl enable condocompras.service

# Iniciar o serviço
sudo systemctl start condocompras.service

# Verificar status
sudo systemctl status condocompras.service
```

#### 3.3. Comandos úteis para gerenciar o serviço

```bash
# Ver status
sudo systemctl status condocompras.service

# Parar o serviço
sudo systemctl stop condocompras.service

# Iniciar o serviço
sudo systemctl start condocompras.service

# Reiniciar o serviço
sudo systemctl restart condocompras.service

# Ver logs em tempo real
sudo journalctl -u condocompras.service -f

# Ver últimas 100 linhas dos logs
sudo journalctl -u condocompras.service -n 100
```

### 4. Configurar Proxy Reverso (HTTPS)

Se estiver usando um proxy reverso (como Nginx, Apache ou Zero Trust), configure o Laravel para confiar nos proxies:

Edite `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    // Confiar em proxies para detectar HTTPS corretamente
    $middleware->trustProxies(at: '*');
})
```

Isso garante que:
- URLs geradas sejam HTTPS
- Cookies sejam seguros
- Redirecionamentos funcionem corretamente

### 5. Verificar Porta em Uso

Para verificar se a porta está escutando corretamente:

```bash
# Linux
netstat -tlnp | grep PORTA_DESEJADA
# ou
ss -tlnp | grep PORTA_DESEJADA
```

### 6. Configurar Firewall

Se necessário, libere a porta no firewall:

```bash
# UFW (Ubuntu)
sudo ufw allow PORTA_DESEJADA/tcp

# Firewalld (CentOS/RHEL)
sudo firewall-cmd --permanent --add-port=PORTA_DESEJADA/tcp
sudo firewall-cmd --reload
```

### 7. Configurar Fila de Processamento (Queue Worker)

Para que as notificações (WhatsApp/Email) sejam enviadas em segundo plano sem travar o sistema:

1. No arquivo `.env`, certifique-se que:
   `QUEUE_CONNECTION=database`

2. Crie o arquivo de serviço `/etc/systemd/system/condocompras-worker.service`:
   (Você pode usar o modelo `condocompras-worker.service` disponível na raiz do projeto)

```ini
[Unit]
Description=CondoCompras Queue Worker
After=network.target

[Service]
Type=simple
User=seu_usuario
WorkingDirectory=/caminho/para/condocompras
ExecStart=/usr/bin/php artisan queue:work --tries=3 --timeout=90
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal
SyslogIdentifier=condocompras-worker

[Install]
WantedBy=multi-user.target
```

3. Ative o serviço:
```bash
sudo systemctl daemon-reload
sudo systemctl enable condocompras-worker
sudo systemctl start condocompras-worker
```

## 📚 Documentação

Consulte a [Documentação Técnica](./DOCUMENTACAO_TECNICA.md) para:

- Schema do banco de dados
- Estrutura de pastas
- Models e relacionamentos
- Endpoints da API
- Segurança
- Multi-tenancy
- Links únicos para prestadores
- Sistema de auditoria

## 🔄 Modos de Execução

### Desenvolvimento

Para desenvolvimento local, use:

```bash
php artisan serve
```

O servidor iniciará em `http://localhost:8000` por padrão.

Para usar uma porta específica:

```bash
php artisan serve --port=PORTA_DESEJADA
```

### Produção

Para produção, recomenda-se:

1. **Usar um servidor web** (Nginx ou Apache) como proxy reverso
2. **Configurar como serviço systemd** (veja seção acima)
3. **Usar HTTPS** através de certificado SSL/TLS
4. **Otimizar caches** (config, routes, views)
5. **Configurar logs** adequadamente

**Importante**: Nunca use `php artisan serve` diretamente em produção sem um proxy reverso. Use um servidor web profissional como Nginx ou Apache.

## 🔐 Autenticação

### Web

O sistema utiliza Laravel Breeze para autenticação web. Registre-se ou faça login através da interface.

### API

Para usar a API, você precisa obter um token Sanctum:

```bash
POST /api/auth/token
{
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

Use o token no header:
```
Authorization: Bearer {token}
```

## 🌐 Endpoints da API

### Principais Endpoints

- `GET /api/demandas` - Lista demandas
- `POST /api/demandas` - Cria demanda
- `GET /api/prestadores` - Lista prestadores
- `POST /api/orcamentos` - Cria orçamento
- `POST /api/documentos` - Upload de documento

### Links Públicos para Prestadores

- `GET /api/prestador/link/{token}` - Visualizar demanda
- `POST /api/prestador/link/{token}/orcamento` - Enviar orçamento

Consulte a [Documentação Técnica](./DOCUMENTACAO_TECNICA.md) para a lista completa de endpoints.

## 🏗️ Estrutura do Projeto

```
app/
├── Http/Controllers/     # Controllers web e API
├── Models/               # Models Eloquent
├── Traits/               # Traits reutilizáveis
└── ...

database/
└── migrations/           # Migrations do banco

routes/
├── web.php              # Rotas web
└── api.php              # Rotas API
```

## 🔒 Segurança

### Recursos de Segurança Implementados

- Multi-tenancy com isolamento por empresa
- Validação rigorosa de uploads
- Proteção CSRF
- Rate limiting na API
- Logs de auditoria
- Tokens únicos para links de prestadores
- Autenticação segura (Breeze + Sanctum)
- Middleware de autorização por perfil
- Isolamento de dados por empresa

### Recomendações para Produção

1. **Sempre use HTTPS** em produção
2. **Configure headers de segurança** no servidor web
3. **Mantenha `APP_DEBUG=false`** em produção
4. **Use `LOG_LEVEL=error`** em produção
5. **Configure backup automático** do banco de dados
6. **Mantenha dependências atualizadas**: `composer update` e `npm update`
7. **Use variáveis de ambiente** para dados sensíveis
8. **Configure rate limiting** adequado para sua aplicação
9. **Monitore logs** regularmente
10. **Use firewall** para proteger o servidor

## 🔧 Troubleshooting

### Problemas Comuns

#### Serviço não inicia

```bash
# Verificar logs do serviço
sudo journalctl -u condocompras.service -n 50

# Verificar se a porta está em uso
sudo lsof -i :PORTA_DESEJADA
# ou
sudo netstat -tlnp | grep PORTA_DESEJADA
```

#### Erro de permissões

```bash
# Corrigir permissões
sudo chown -R seu_usuario:seu_usuario /caminho/para/condocompras
chmod -R 775 storage bootstrap/cache
```

#### Cache não atualiza

```bash
# Limpar todos os caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Recriar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Erro 500 em produção

1. Verifique os logs: `storage/logs/laravel.log`
2. Verifique se `APP_DEBUG=false` no `.env`
3. Verifique permissões de arquivos
4. Verifique se todas as migrations foram executadas
5. Verifique se o link simbólico do storage existe: `php artisan storage:link`

#### URLs geradas como HTTP em vez de HTTPS

1. Verifique se `APP_URL` está configurado com HTTPS no `.env`
2. Verifique se `trustProxies` está configurado no `bootstrap/app.php`
3. Limpe o cache de configuração: `php artisan config:clear && php artisan config:cache`

#### Serviço para após reiniciar o servidor

```bash
# Verificar se o serviço está habilitado
sudo systemctl is-enabled condocompras.service

# Se não estiver, habilite
sudo systemctl enable condocompras.service
```

## 📝 Licença

Este projeto é proprietário.

## 👥 Desenvolvimento

Desenvolvido seguindo as melhores práticas do Laravel e padrões de segurança.

---

**Versão**: 1.0.0  
**Laravel**: 12.x  
**PHP**: >= 8.2
