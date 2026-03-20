# Guia de instalação e configuração do ClawdBot no servidor

O **ClawdBot** (também chamado **Moltbot**) é um assistente de IA self-hosted que integra com Telegram, WhatsApp, Discord e Signal. Este guia ajuda a colocá-lo no seu servidor e configurá-lo.

## Documentação oficial

- **Site principal**: [docs.clawd.bot](https://docs.clawd.bot/)
- **Instalação**: [docs.clawd.bot/install](https://docs.clawd.bot/install)
- **Setup inicial**: [docs.clawd.bot/start/setup](https://docs.clawd.bot/start/setup)
- **Wizard (onboarding)**: [docs.clawd.bot/wizard](https://docs.clawd.bot/wizard)
- **Docker**: [docs.clawd.bot/install/docker](https://docs.clawd.bot/install/docker)
- **CLI**: [docs.clawd.bot/cli](https://docs.clawd.bot/cli)

---

## Formas de instalação no servidor

### 1. Instalador padrão (recomendado para VPS/servidor dedicado)

1. Acesse a documentação de instalação:
   - [docs.clawd.bot/install](https://docs.clawd.bot/install)

2. Siga o método **Installer** indicado na página (o comando exato pode mudar; use sempre o que estiver na doc).

3. Depois de instalar, rode o wizard de configuração:
   ```bash
   clawdbot onboard
   ```
   O wizard orienta: agente, canais (Telegram, Discord, etc.), modelo e demais opções.

4. Arquivos de configuração:
   - **Global**: `~/.config/clawdbot/config.json`
   - **Por projeto**: `clawdbot.json` na raiz do workspace

---

### 2. Docker (bom para manter isolado e atualizado)

1. Leia a documentação Docker:
   - [docs.clawd.bot/install/docker](https://docs.clawd.bot/install/docker)

2. Exemplo mínimo (ajuste conforme a doc oficial):
   ```bash
   # Criar diretório para o ClawdBot
   mkdir -p ~/clawdbot && cd ~/clawdbot

   # O docker-compose ou Dockerfile exato deve ser obtido na documentação
   # ou no repositório: https://github.com/clawdbot/clawdbot
   ```

3. A pasta `~/clawdbot` (ou a que você usar) pode guardar:
   - `config.json` (ou o que a imagem usar)
   - Dados persistentes (volumes), se necessário

4. Depois de subir os containers, use o wizard/CLI conforme a doc Docker (por exemplo, entrando no container ou usando um container “cli”).

---

### 3. Bun (se você já usa Node/Bun no servidor)

Se a documentação indicar instalação via **Bun**:

```bash
# Instalar Bun se ainda não tiver (Linux)
curl -fsSL https://bun.sh/install | bash

# O comando exato do ClawdBot via Bun deve ser verificado em:
# https://docs.clawd.bot/install
```

Use sempre o comando e a versão indicados em [docs.clawd.bot/install](https://docs.clawd.bot/install).

---

## Configuração inicial (após instalar)

### Wizard pela CLI

```bash
clawdbot onboard
```

O wizard define, em geral:

- **Agent** – nome e comportamento do assistente  
- **System prompt** – instruções globais do modelo  
- **Canais** – Telegram, Discord, WhatsApp, Signal (com tokens/credenciais)  
- **Modelo** – qual LLM usar (local ou API)  
- **Plugins/skills** – extensões opcionais  

### Configuração manual

- **Global**: `~/.config/clawdbot/config.json`
- **Por projeto**: `clawdbot.json` na raiz do projeto

A estrutura exata dos campos está em [docs.clawd.bot/configuration](https://docs.clawd.bot/configuration).

---

## Rodar como serviço no servidor (systemd)

Se você instalou pelo **instalador** (binário/script) e quer que o ClawdBot suba com o servidor e reinicie em caso de falha:

1. Descubra o comando que sobe o “gateway” ou o processo principal (veja [docs.clawd.bot/start/setup](https://docs.clawd.bot/start/setup) e [docs.clawd.bot/gateway](https://docs.clawd.bot/gateway)).

2. Crie o serviço (ajuste usuário, caminho e comando conforme o seu ambiente):

   ```bash
   sudo nano /etc/systemd/system/clawdbot.service
   ```

   Conteúdo sugerido (**substitua** `seu_usuario`, caminhos e comando pelo que a doc indicar):

   ```ini
   [Unit]
   Description=ClawdBot - Assistente de IA
   After=network.target

   [Service]
   Type=simple
   User=seu_usuario
   WorkingDirectory=/home/seu_usuario
   ExecStart=/usr/local/bin/clawdbot gateway
   Restart=always
   RestartSec=10
   StandardOutput=journal
   StandardError=journal
   SyslogIdentifier=clawdbot

   [Install]
   WantedBy=multi-user.target
   ```

3. Ativar e iniciar:

   ```bash
   sudo systemctl daemon-reload
   sudo systemctl enable clawdbot.service
   sudo systemctl start clawdbot.service
   sudo systemctl status clawdbot.service
   ```

4. Logs:

   ```bash
   journalctl -u clawdbot.service -f
   ```

O nome do executável (`clawdbot` ou `moltbot`) e o subcomando (`gateway`, `serve`, etc.) devem ser confirmados na documentação e no binário que você instalou.

---

## Se usar Docker

Para “sempre ligado” no servidor, use `docker compose` com restart:

- No `docker-compose.yml`, use `restart: unless-stopped` (ou `always`) no serviço do ClawdBot.
- Subir: `docker compose up -d`.
- Ver logs: `docker compose logs -f`.

Assim, o próprio Docker cuida de reiniciar o container; não é obrigatório criar um unit systemd para o ClawdBot nesse caso.

---

---

## Troubleshooting: "disconnected (1008): gateway auth required"

Esse erro aparece quando o **dashboard** (interface no browser) tenta conectar ao **gateway** por WebSocket e é recusado porque a autenticação é obrigatória e não foi fornecida.

### O que fazer (em ordem)

#### 1. Abrir o dashboard pelo comando da CLI (recomendado)

**Não** acesse o dashboard digitando a URL direto no browser (ex.: `http://servidor:porta`). Use o comando que gera um link já autenticado:

```bash
clawdbot dashboard
```

(O nome exato pode ser `moltbot dashboard` ou outro; confira com `clawdbot --help` ou `clawdbot dashboard --help`.)

Esse comando costuma abrir o browser (ou exibir uma URL) com um **token de acesso** na URL ou em cookie. Usando esse link, a conexão WebSocket com o gateway já vai com auth e o "gateway auth required" some.

- Doc do comando: [docs.clawd.bot/cli/dashboard](https://docs.clawd.bot/cli/dashboard)
- Control UI / Dashboard na web: [docs.clawd.bot/web/dashboard](https://docs.clawd.bot/web/dashboard) e [docs.clawd.bot/web/control-ui](https://docs.clawd.bot/web/control-ui)

#### 2. Gateway e dashboard no mesmo “contexto”

Se o **gateway** está rodando no servidor e você abre o dashboard de **outra máquina** (ou por outro host/porta), o navegador pode não ter o token que o gateway espera.

- **Opção A**: No próprio servidor, rode `clawdbot dashboard` e use o link que ele mostrar/abrir (via SSH tunnel ou acesso direto à máquina, se for o caso).
- **Opção B**: Se você *precisa* acessar de fora, leia na doc como expor o dashboard com autenticação (token na URL, reverse proxy com header de auth, etc.): [docs.clawd.bot/security](https://docs.clawd.bot/security) e [docs.clawd.bot/gateway/configuration-examples](https://docs.clawd.bot/gateway/configuration-examples).

#### 3. Configuração de segurança do gateway

O gateway pode exigir token ou outro tipo de auth. Essa configuração costuma ficar em:

- `~/.config/clawdbot/config.json` (global), ou  
- em arquivos de config do gateway indicados na documentação.

Verifique na doc de **Security** e **Gateway** como definir e onde colocar o token (ou se há “auth obrigatória” que possa ser ajustada em ambiente controlado):

- [docs.clawd.bot/security](https://docs.clawd.bot/security)  
- [docs.clawd.bot/cli/gateway](https://docs.clawd.bot/cli/gateway)

#### 4. Troubleshooting oficial do gateway

Se o erro continuar:

- [docs.clawd.bot/gateway/troubleshooting](https://docs.clawd.bot/gateway/troubleshooting)  
- [docs.clawd.bot/help/troubleshooting](https://docs.clawd.bot/help/troubleshooting)

### Resumo

| Situação | Ação |
|----------|------|
| Você abre o dashboard pela URL “na mão” | Use **`clawdbot dashboard`** e abra o link que o comando mostrar/abrir |
| Dashboard em uma máquina, gateway em outra | Garanta que o dashboard use o token/URL autenticada; veja Security + Gateway na doc |
| Não sabe onde configurar auth | Veja [docs.clawd.bot/security](https://docs.clawd.bot/security) e [docs.clawd.bot/cli/gateway](https://docs.clawd.bot/cli/gateway) |

---

## Checklist rápido

1. [ ] Ler [docs.clawd.bot/install](https://docs.clawd.bot/install) e escolher: Instalador, Docker, Bun, etc.
2. [ ] Instalar seguindo o método escolhido.
3. [ ] Rodar `clawdbot onboard` (ou equivalente na sua instalação).
4. [ ] Configurar pelo menos um canal (ex.: Telegram ou Discord) e testar.
5. [ ] Se for instalador: configurar systemd; se for Docker: usar `restart` no `docker-compose`.
6. [ ] Em caso de erro: [docs.clawd.bot/gateway/troubleshooting](https://docs.clawd.bot/gateway/troubleshooting).

---

## Referências

| Recurso       | URL |
|--------------|-----|
| Documentação | https://docs.clawd.bot/ |
| Instalação   | https://docs.clawd.bot/install |
| Setup        | https://docs.clawd.bot/start/setup |
| Wizard       | https://docs.clawd.bot/wizard |
| Configuração | https://docs.clawd.bot/configuration |
| Docker       | https://docs.clawd.bot/install/docker |
| CLI          | https://docs.clawd.bot/cli |
| Troubleshooting | https://docs.clawd.bot/gateway/troubleshooting |
| GitHub       | https://github.com/clawdbot/clawdbot |

---

*Este guia foi montado com base na documentação pública do ClawdBot/Moltbot. Sempre confira e siga as instruções atualizadas em [docs.clawd.bot](https://docs.clawd.bot).*
