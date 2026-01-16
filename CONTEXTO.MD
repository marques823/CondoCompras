## 📌 PROMPT – DESENVOLVIMENTO DE APLICAÇÃO SAAS PARA ADMINISTRADORAS DE CONDOMÍNIOS

Quero desenvolver do zero uma aplicação SaaS em **Laravel** focada em administradoras de condomínios, com as seguintes características:

---

### 1. OBJETIVO DO SISTEMA

Criar uma plataforma web para:

* Centralizar demandas de serviços dos condomínios
* Gerenciar prestadores de serviço
* Organizar orçamentos
* Receber documentos (notas fiscais, boletos, comprovantes)
* Automatizar comunicação com fornecedores
* Integrar com automações externas via API e n8n

O sistema será multiusuário e multiempresa, voltado para administradores que gerenciam vários condomínios ao mesmo tempo.

---

## 2. TECNOLOGIAS BASE

A aplicação deve ser construída com:

* Backend: **Laravel (versão mais recente estável)**
* Banco de dados: **PostgreSQL ou MySQL**
* Autenticação: Laravel Breeze ou Jetstream
* API: Laravel Sanctum para autenticação
* Frontend: Blade + Tailwind (ou Inertia/Vue se necessário)
* Armazenamento de arquivos: Laravel Storage
* Integrações: API REST para consumo por **n8n**

---

## 3. REQUISITOS DE SEGURANÇA

A aplicação deverá priorizar fortemente segurança, incluindo:

* Autenticação segura com:

  * hash de senhas
  * controle de sessões
  * 2FA opcional
* Controle de permissões por perfil
* Criptografia de dados sensíveis no banco
* Validação rigorosa de uploads
* Logs de auditoria
* Rate limit em APIs
* Tokens JWT ou Sanctum
* Proteção contra:

  * SQL Injection
  * XSS
  * CSRF
  * acesso indevido entre contas

Cada administrador só poderá ver dados dos condomínios aos quais pertence.

---

## 4. ARQUITETURA GERAL

O sistema deverá ser dividido em módulos:

### Módulos principais:

* Autenticação e usuários
* Condomínios
* Prestadores
* Demandas
* Orçamentos
* Documentos
* API externa
* Logs e auditoria

---

## 5. FUNCIONALIDADES OBRIGATÓRIAS

### 5.1 – Gestão de Condomínios

* Cadastro de condomínios
* Endereço, síndico, contatos
* Histórico por condomínio

---

### 5.2 – Gestão de Prestadores

Cadastro completo com:

* Nome / razão social
* CNPJ/CPF
* Categorias de serviço
* Regiões atendidas
* Contatos
* Documentos obrigatórios
* Histórico de serviços

---

### 5.3 – Gestão de Demandas

O administrador deve poder:

* Criar uma demanda para um condomínio
* Descrever o problema
* Selecionar categoria
* Selecionar prestadores convidados

---

### 5.4 – Links Únicos para Prestadores

Para cada demanda:

* O sistema deve gerar um **LINK SEGURO ÚNICO** para cada prestador
* Esse link permitirá ao prestador:

  * visualizar a demanda
  * enviar orçamento
  * anexar PDF
  * enviar nota fiscal
  * enviar boleto

Sem necessidade de login.

---

### 5.5 – Orçamentos

* Recebimento de orçamentos por link
* Associação automática à demanda
* Comparação entre orçamentos
* Status:

  * recebido
  * aprovado
  * rejeitado

---

### 5.6 – Documentos

* Upload de:

  * Notas fiscais
  * Boletos
  * Comprovantes
* Organização por:

  * condomínio
  * mês
  * tipo

---

## 6. API PARA INTEGRAÇÃO COM N8N

Criar uma API REST completa para:

* Criar demandas
* Listar prestadores
* Registrar orçamentos
* Upload de documentos
* Atualizar status
* Consultar informações

Endpoints devem usar autenticação via token.

Exemplos:

```
POST /api/demandas
GET  /api/demandas/{id}
POST /api/orcamentos
POST /api/documentos
GET  /api/prestadores
```

Essa API será consumida por automações do **n8n** para:

* envio de mensagens
* processamento de PDFs
* extração de dados
* notificações

---

## 7. WORKFLOWS AUTOMATIZADOS (N8N)

O sistema deverá ser preparado para integrar com n8n para:

* Disparo automático de solicitações
* Notificações por WhatsApp/e-mail
* Extração de dados de PDFs
* Consolidação de orçamentos
* Alertas automáticos

---

## 8. MULTI-TENANCY

A aplicação deve ser:

* Multiempresa
* Multiusuário
* Cada administrador isolado em seu ambiente
* Dados totalmente segregados

---

## 9. REQUISITOS DE LOGS

Implementar:

* Logs de acesso
* Logs de alterações
* Histórico de ações
* Auditoria completa

---

## 10. MVP INICIAL

Primeira versão deve conter:

* Cadastro de usuários
* Cadastro de condomínios
* Cadastro de prestadores
* Criação de demandas
* Geração de links para prestadores
* Recebimento de orçamentos
* Upload de documentos
* API básica para n8n

---

### RESULTADO ESPERADO

Quero que seja gerada uma estrutura inicial contendo:

* diagrama de banco de dados
* models do Laravel
* migrations
* controllers
* rotas
* estrutura da API
* arquitetura sugerida

---

# 🎁 INSTRUÇÃO FINAL DO PROMPT

“Com base em todos os requisitos acima, gere um plano técnico completo e a estrutura inicial do projeto Laravel com:

* schema do banco
* models
* migrations
* controllers
* rotas
* exemplos de endpoints
* arquitetura de segurança
* estrutura de pastas recomendada”

---