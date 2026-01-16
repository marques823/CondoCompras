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

### Passos

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

7. **Compile os assets**
```bash
npm run build
```

8. **Inicie o servidor**
```bash
php artisan serve
```

Acesse: `http://localhost:8000`

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

- Multi-tenancy com isolamento por empresa
- Validação rigorosa de uploads
- Proteção CSRF
- Rate limiting na API
- Logs de auditoria
- Tokens únicos para links de prestadores

## 📝 Licença

Este projeto é proprietário.

## 👥 Desenvolvimento

Desenvolvido seguindo as melhores práticas do Laravel e padrões de segurança.

---

**Versão**: 1.0.0  
**Laravel**: 12.x  
**PHP**: >= 8.2
