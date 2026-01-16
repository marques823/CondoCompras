# 📚 Documentação Técnica - Sistema de Gestão para Administradoras de Condomínios

## 📋 Índice

1. [Arquitetura do Sistema](#arquitetura-do-sistema)
2. [Schema do Banco de Dados](#schema-do-banco-de-dados)
3. [Estrutura de Pastas](#estrutura-de-pastas)
4. [Models e Relacionamentos](#models-e-relacionamentos)
5. [API REST - Endpoints](#api-rest---endpoints)
6. [Segurança](#segurança)
7. [Multi-Tenancy](#multi-tenancy)
8. [Links Únicos para Prestadores](#links-únicos-para-prestadores)
9. [Auditoria](#auditoria)

---

## 🏗️ Arquitetura do Sistema

### Tecnologias Utilizadas

- **Backend**: Laravel 12.x
- **Banco de Dados**: PostgreSQL/MySQL
- **Autenticação Web**: Laravel Breeze (Blade + Tailwind)
- **Autenticação API**: Laravel Sanctum
- **Armazenamento**: Laravel Storage (local/S3)

### Padrões Arquiteturais

- **MVC (Model-View-Controller)**
- **Repository Pattern** (opcional para futuras expansões)
- **Service Layer** (para lógica de negócio complexa)
- **Multi-Tenancy** (isolamento por empresa)

---

## 🗄️ Schema do Banco de Dados

### Diagrama de Relacionamentos

```
empresas (1) ──< (N) users
empresas (1) ──< (N) condominios
empresas (1) ──< (N) prestadores
empresas (1) ──< (N) demandas
empresas (1) ──< (N) documentos
empresas (1) ──< (N) auditoria

condominios (1) ──< (N) demandas
condominios (1) ──< (N) documentos

prestadores (N) ──< (N) categorias_servicos (pivot: prestador_categoria)
prestadores (N) ──< (N) regioes (pivot: prestador_regiao)
prestadores (1) ──< (N) links_prestador
prestadores (1) ──< (N) orcamentos
prestadores (1) ──< (N) documentos

demandas (1) ──< (N) links_prestador
demandas (1) ──< (N) orcamentos
demandas (1) ──< (N) documentos
demandas (N) ──< (N) prestadores (pivot: demanda_prestador)

orcamentos (1) ──< (N) documentos
```

### Tabelas Principais

#### `empresas`
- `id` (PK)
- `nome`
- `razao_social`
- `cnpj` (unique)
- `email` (unique)
- `telefone`
- `endereco`
- `ativo` (boolean)
- `created_at`, `updated_at`, `deleted_at`

#### `users`
- `id` (PK)
- `empresa_id` (FK → empresas)
- `name`
- `email` (unique)
- `password`
- `perfil` (enum: 'admin', 'usuario')
- `email_verified_at`
- `remember_token`
- `created_at`, `updated_at`

#### `condominios`
- `id` (PK)
- `empresa_id` (FK → empresas)
- `nome`
- `cnpj`
- `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `cep`
- `sindico_nome`, `sindico_telefone`, `sindico_email`
- `observacoes`
- `ativo` (boolean)
- `created_at`, `updated_at`, `deleted_at`

#### `prestadores`
- `id` (PK)
- `empresa_id` (FK → empresas)
- `nome_razao_social`
- `tipo` (enum: 'fisica', 'juridica')
- `cpf_cnpj`
- `email`, `telefone`, `celular`
- `endereco`
- `documentos_obrigatorios` (JSON)
- `observacoes`
- `ativo` (boolean)
- `created_at`, `updated_at`, `deleted_at`

#### `demandas`
- `id` (PK)
- `empresa_id` (FK → empresas)
- `condominio_id` (FK → condominios)
- `categoria_servico_id` (FK → categorias_servicos, nullable)
- `usuario_id` (FK → users)
- `titulo`
- `descricao`
- `status` (enum: 'aberta', 'em_andamento', 'aguardando_orcamento', 'concluida', 'cancelada')
- `prazo_limite` (date, nullable)
- `observacoes`
- `created_at`, `updated_at`, `deleted_at`

#### `links_prestador`
- `id` (PK)
- `demanda_id` (FK → demandas)
- `prestador_id` (FK → prestadores)
- `token` (string, unique, 64 chars)
- `expira_em` (datetime, nullable)
- `usado` (boolean)
- `usado_em` (datetime, nullable)
- `acessos` (integer)
- `created_at`, `updated_at`

#### `orcamentos`
- `id` (PK)
- `demanda_id` (FK → demandas)
- `prestador_id` (FK → prestadores)
- `link_prestador_id` (FK → links_prestador, nullable)
- `valor` (decimal 15,2)
- `descricao`
- `validade` (date, nullable)
- `status` (enum: 'recebido', 'aprovado', 'rejeitado')
- `observacoes`
- `motivo_rejeicao`
- `aprovado_por` (FK → users, nullable)
- `aprovado_em` (datetime, nullable)
- `created_at`, `updated_at`, `deleted_at`

#### `documentos`
- `id` (PK)
- `empresa_id` (FK → empresas)
- `condominio_id` (FK → condominios, nullable)
- `demanda_id` (FK → demandas, nullable)
- `orcamento_id` (FK → orcamentos, nullable)
- `prestador_id` (FK → prestadores, nullable)
- `tipo` (enum: 'nota_fiscal', 'boleto', 'comprovante', 'orcamento_pdf', 'outro')
- `nome_original`
- `nome_arquivo`
- `caminho`
- `mime_type`
- `tamanho` (bytes)
- `data_documento` (date, nullable)
- `observacoes`
- `created_at`, `updated_at`, `deleted_at`

#### `auditoria`
- `id` (PK)
- `empresa_id` (FK → empresas, nullable)
- `usuario_id` (FK → users, nullable)
- `modelo` (string) - Nome do modelo (ex: 'Demanda')
- `modelo_id` (bigint, nullable)
- `acao` (string) - 'created', 'updated', 'deleted', 'viewed'
- `dados_anteriores` (JSON, nullable)
- `dados_novos` (JSON, nullable)
- `ip_address` (string, nullable)
- `user_agent` (text, nullable)
- `observacoes`
- `created_at`, `updated_at`

### Tabelas Pivot

- `prestador_categoria`: `prestador_id`, `categoria_servico_id`
- `prestador_regiao`: `prestador_id`, `regiao_id`
- `demanda_prestador`: `demanda_id`, `prestador_id`, `status`, `visualizado_em`

---

## 📁 Estrutura de Pastas

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── DemandaApiController.php
│   │   │   ├── PrestadorApiController.php
│   │   │   ├── OrcamentoApiController.php
│   │   │   └── DocumentoApiController.php
│   │   ├── EmpresaController.php
│   │   ├── CondominioController.php
│   │   ├── PrestadorController.php
│   │   ├── DemandaController.php
│   │   ├── OrcamentoController.php
│   │   ├── DocumentoController.php
│   │   └── LinkPrestadorController.php
│   └── Middleware/
│       ├── EnsureUserBelongsToEmpresa.php
│       └── EnsureApiToken.php
├── Models/
│   ├── Empresa.php
│   ├── User.php
│   ├── Condominio.php
│   ├── Prestador.php
│   ├── CategoriaServico.php
│   ├── Regiao.php
│   ├── Demanda.php
│   ├── LinkPrestador.php
│   ├── Orcamento.php
│   ├── Documento.php
│   └── Auditoria.php
└── Traits/
    └── Auditavel.php

database/
└── migrations/
    ├── 2026_01_16_192644_create_empresas_table.php
    ├── 2026_01_16_192645_add_empresa_id_to_users_table.php
    ├── 2026_01_16_192645_create_condominios_table.php
    ├── 2026_01_16_192645_create_prestadores_table.php
    ├── 2026_01_16_192645_create_categorias_servicos_table.php
    ├── 2026_01_16_192645_create_regioes_table.php
    ├── 2026_01_16_192811_create_prestador_categoria_table.php
    ├── 2026_01_16_192811_create_prestador_regiao_table.php
    ├── 2026_01_16_192646_create_demandas_table.php
    ├── 2026_01_16_192646_create_demanda_prestador_table.php
    ├── 2026_01_16_192646_create_links_prestador_table.php
    ├── 2026_01_16_192646_create_orcamentos_table.php
    ├── 2026_01_16_192646_create_documentos_table.php
    └── 2026_01_16_192647_create_auditoria_table.php

routes/
├── web.php
└── api.php
```

---

## 🔗 Models e Relacionamentos

### Empresa
- `hasMany`: User, Condominio, Prestador, Demanda, Documento, Auditoria

### User
- `belongsTo`: Empresa
- Métodos: `isAdmin()`, `scopeDaEmpresa()`

### Condominio
- `belongsTo`: Empresa
- `hasMany`: Demanda, Documento
- Scopes: `ativos()`, `daEmpresa()`

### Prestador
- `belongsTo`: Empresa
- `belongsToMany`: CategoriaServico, Regiao, Demanda
- `hasMany`: LinkPrestador, Orcamento, Documento
- Scopes: `ativos()`, `daEmpresa()`

### Demanda
- `belongsTo`: Empresa, Condominio, CategoriaServico, User
- `belongsToMany`: Prestador
- `hasMany`: LinkPrestador, Orcamento, Documento
- Scopes: `daEmpresa()`, `porStatus()`

### LinkPrestador
- `belongsTo`: Demanda, Prestador
- Métodos: `gerarToken()`, `isValido()`, `marcarComoUsado()`, `incrementarAcesso()`
- Scopes: `validos()`

### Orcamento
- `belongsTo`: Demanda, Prestador, LinkPrestador, User (aprovadoPor)
- `hasMany`: Documento
- Métodos: `aprovar()`, `rejeitar()`
- Scopes: `porStatus()`, `aprovados()`

### Documento
- `belongsTo`: Empresa, Condominio, Demanda, Orcamento, Prestador
- Accessor: `tamanho_formatado`
- Scopes: `porTipo()`, `daEmpresa()`

### Auditoria
- `belongsTo`: Empresa, User
- Scopes: `daEmpresa()`, `doModelo()`, `porAcao()`

---

## 🌐 API REST - Endpoints

### Autenticação

A API utiliza **Laravel Sanctum** para autenticação via token.

**Obter Token:**
```http
POST /api/auth/token
Content-Type: application/json

{
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

**Resposta:**
```json
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "usuario@exemplo.com",
    "empresa_id": 1
  }
}
```

### Headers Obrigatórios

```http
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

### Endpoints Disponíveis

#### Demandas

**Listar Demandas**
```http
GET /api/demandas
GET /api/demandas?status=aberta
GET /api/demandas?condominio_id=1
GET /api/demandas?per_page=20
```

**Criar Demanda**
```http
POST /api/demandas
Content-Type: application/json

{
  "condominio_id": 1,
  "categoria_servico_id": 2,
  "titulo": "Reparo no elevador",
  "descricao": "Elevador parado no 5º andar",
  "prazo_limite": "2026-02-15",
  "prestadores": [1, 2, 3]
}
```

**Visualizar Demanda**
```http
GET /api/demandas/{id}
```

**Atualizar Demanda**
```http
PUT /api/demandas/{id}
Content-Type: application/json

{
  "status": "em_andamento",
  "observacoes": "Prestador designado"
}
```

**Remover Demanda**
```http
DELETE /api/demandas/{id}
```

#### Prestadores

**Listar Prestadores**
```http
GET /api/prestadores
GET /api/prestadores?categoria_id=1
GET /api/prestadores?regiao_id=2
```

**Visualizar Prestador**
```http
GET /api/prestadores/{id}
```

#### Orçamentos

**Listar Orçamentos**
```http
GET /api/orcamentos
GET /api/orcamentos?status=recebido
```

**Criar Orçamento**
```http
POST /api/orcamentos
Content-Type: application/json

{
  "demanda_id": 1,
  "prestador_id": 1,
  "valor": 1500.00,
  "descricao": "Orçamento completo",
  "validade": "2026-02-20"
}
```

**Aprovar Orçamento**
```http
POST /api/orcamentos/{id}/aprovar
```

**Rejeitar Orçamento**
```http
POST /api/orcamentos/{id}/rejeitar
Content-Type: application/json

{
  "motivo": "Valor acima do orçado"
}
```

#### Documentos

**Upload de Documento**
```http
POST /api/documentos
Content-Type: multipart/form-data

{
  "tipo": "nota_fiscal",
  "arquivo": {file},
  "condominio_id": 1,
  "data_documento": "2026-01-15"
}
```

**Listar Documentos**
```http
GET /api/documentos
GET /api/documentos?tipo=nota_fiscal
GET /api/documentos?condominio_id=1
```

**Visualizar Documento**
```http
GET /api/documentos/{id}
```

### Endpoints Públicos (Prestadores)

**Visualizar Demanda via Link**
```http
GET /api/prestador/link/{token}
```

**Enviar Orçamento via Link**
```http
POST /api/prestador/link/{token}/orcamento
Content-Type: multipart/form-data

{
  "valor": 1500.00,
  "descricao": "Orçamento detalhado",
  "validade": "2026-02-20",
  "arquivo": {file} // PDF opcional
}
```

---

## 🔒 Segurança

### Implementações de Segurança

1. **Autenticação**
   - Hash de senhas (bcrypt)
   - Tokens Sanctum para API
   - Sessões seguras para web

2. **Autorização**
   - Middleware `EnsureUserBelongsToEmpresa` garante isolamento por empresa
   - Scopes nos models para filtrar por empresa
   - Validação de propriedade em todas as operações

3. **Validação**
   - Validação rigorosa de uploads (tipo, tamanho)
   - Sanitização de inputs
   - Validação de relacionamentos (verifica se pertence à empresa)

4. **Proteções**
   - CSRF protection (web)
   - Rate limiting (API)
   - SQL Injection (Eloquent ORM)
   - XSS (Blade escaping)

5. **Criptografia**
   - Dados sensíveis podem ser criptografados usando `encrypted` cast nos models

6. **Logs de Auditoria**
   - Todas as ações são registradas na tabela `auditoria`
   - Registra IP, user agent, dados anteriores e novos

---

## 🏢 Multi-Tenancy

### Estratégia

O sistema utiliza **multi-tenancy por empresa** através de:

1. **Campo `empresa_id`** em todas as tabelas principais
2. **Middleware** `EnsureUserBelongsToEmpresa` que:
   - Verifica se o usuário tem empresa associada
   - Adiciona `empresa_id` ao request
3. **Scopes nos Models**:
   - `scopeDaEmpresa()` - Filtra automaticamente por empresa
4. **Validações**:
   - Todas as operações verificam se o recurso pertence à empresa do usuário

### Exemplo de Uso

```php
// No controller
$demandas = Demanda::daEmpresa($user->empresa_id)->get();

// Ou usando o request
$demandas = Demanda::daEmpresa($request->empresa_id)->get();
```

---

## 🔗 Links Únicos para Prestadores

### Funcionamento

1. **Geração**: Quando uma demanda é criada com prestadores, são gerados links únicos
2. **Token**: Token aleatório de 64 caracteres, único no banco
3. **Validade**: Links expiram em 30 dias (configurável)
4. **Uso Único**: Após envio de orçamento, link é marcado como usado
5. **Rastreamento**: Registra acessos e visualizações

### Fluxo

1. Admin cria demanda e seleciona prestadores
2. Sistema gera links únicos para cada prestador
3. Links são enviados aos prestadores (via n8n/email/WhatsApp)
4. Prestador acessa link e visualiza demanda
5. Prestador envia orçamento pelo link
6. Link é marcado como usado

### Segurança

- Token único e não previsível
- Validação de expiração
- Validação de uso único
- Registro de IP e user agent

---

## 📊 Auditoria

### Sistema de Auditoria

O sistema registra automaticamente:

- **Criações** (`created`)
- **Atualizações** (`updated`)
- **Exclusões** (`deleted`)
- **Visualizações** (`viewed`) - manual

### Dados Registrados

- Empresa
- Usuário
- Modelo e ID do registro
- Ação realizada
- Dados anteriores (JSON)
- Dados novos (JSON)
- IP address
- User agent
- Timestamp

### Uso do Trait Auditavel

```php
use App\Traits\Auditavel;

class Demanda extends Model
{
    use Auditavel;
    // ...
}
```

### Consultas de Auditoria

```php
// Todas as ações de uma empresa
Auditoria::daEmpresa($empresaId)->get();

// Ações de um modelo específico
Auditoria::doModelo('Demanda', $demandaId)->get();

// Ações de um usuário
Auditoria::where('usuario_id', $userId)->get();
```

---

## 🚀 Próximos Passos

1. **Implementar Views (Blade)**
2. **Configurar Storage para arquivos**
3. **Implementar notificações (email/WhatsApp)**
4. **Criar testes automatizados**
5. **Configurar CI/CD**
6. **Implementar dashboard com métricas**
7. **Adicionar relatórios**
8. **Integração completa com n8n**

---

## 📝 Notas Importantes

- Todas as rotas de API requerem autenticação via Sanctum (exceto links de prestadores)
- Todos os recursos são filtrados automaticamente por empresa
- Links de prestadores são públicos mas validados por token único
- Uploads de arquivos devem ser validados e armazenados com segurança
- Logs de auditoria são criados automaticamente para modelos que usam o trait `Auditavel`

---

**Desenvolvido com Laravel 12.x** 🚀
