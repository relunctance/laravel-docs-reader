# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## Visão geral

**Laravel Docs Reader** é um skill OpenClaw Agent que dá a agentes (e desenvolvedores) acesso instantâneo à documentação oficial Laravel precisa e com versão. Sem mais adivinhação — o agente consulta a documentação oficial antes de escrever qualquer linha de código.

### Funcionalidades principais

- 🔍 **Busca CLI em linguagem natural** — `php laradoc.php search "how to create a middleware"` — funciona como perguntar diretamente à documentação
- 📦 **Cache de documentação local** — documentação incluída no skill, disponível offline, acesso instantâneo
- 🔄 **Detecção automática de versão** — detecta a versão Laravel do seu projeto (10/11/12) e serve a documentação correta
- 📖 **Cobertura completa da documentação** — routing, controllers, models, filas, mail, auth, eventos, broadcasting, tests e mais
- 🏭 **Geração de código** — gera esqueletos de código Laravel compatíveis com PSR-12
- 📊 **Diferenças de versão** — destaca o que mudou entre Laravel 10 / 11 / 12
- 📋 **Referência rápida PSR-12** — integrada: `php laradoc.php psr`, `psr arrays`, `psr naming`
- 🤖 **Atualização automática via PR** — GitHub Actions vigia novos lançamentos Laravel e cria automaticamente um PR para atualizar este skill
- 🔗 **Integração com Laravel Package Search** — após cada resultado de busca, o agente sugere `laravel-package-search` para descobrir packages de terceiros

---

## CLI — 14 comandos

```bash
# ── Busca principal ─────────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # Busca em linguagem natural
php laradoc.php search "how to send a notification"
# ← Mostra sugestão do Package Search após cada resultado

# ── Versão ─────────────────────────────────────────────────────
php laradoc.php version                   # Detecta automaticamente o projeto atual
php laradoc.php version /path/to/project  # projeto específico
php laradoc.php current                 # Mostra versão padrão (Laravel 12)

# ── Config & Facades ───────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & Diff ─────────────────────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── Geração de código ─────────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Diretivas Blade ────────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── Referência PSR-12 ─────────────────────────────────────────
php laradoc.php psr                   # Tabela completa de regras PSR-12
php laradoc.php psr arrays          # Regras de arrays
php laradoc.php psr naming          # Convenções de nomenclatura

# ── Cache & Atualização ────────────────────────────────────────
php laradoc.php cache                  # Mostrar estado do cache local
php laradoc.php update                 # Forçar atualização do GitHub
php laradoc.php subscribe              # Mostrar estado de inscrição/atualização auto
```

---

## Detecção automática de versão

O CLI detecta automaticamente a versão Laravel do projeto:

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

Se nenhum projeto for encontrado, o padrão é **Laravel 12**.

---

## Atualização automática (PR GitHub Actions)

Como o skill se mantém atualizado:

- **Verificação semanal**: GitHub Actions executa todo domingo às 00:00 UTC
- **Detecção de nova versão**: compara com a última versão de `laravel/framework` no Packagist
- **PR automático**: se uma nova versão for detectada, cria um PR atualizando SKILL.md, version-detection.md e version-diff.md

Usuários deste skill podem examinar o PR gerado automaticamente antes de mesclá-lo.

---

## Cobertura documental

| Categoria | Tópicos |
|----------|---------|
| Routing | Basic routes, route groups, resource routes, named routes, middleware |
| Controllers | CRUD, REST, API, single-action, injeção de dependências |
| Models | Eloquent, todos os 12 tipos de relacionamento, scopes, casts |
| Migrations | construtor de schema, chaves estrangeiras, índices |
| Validação | Form Requests, inline, regras customizadas |
| Auth | Breeze, Sanctum, Gates, Policies |
| Filas | Jobs, dispatch, tratamento de falhas, Laravel Horizon |
| Cache | Store API, tags Redis, locks atômicos |
| Mail | Markdown, anexos, mail em fila |
| Notificações | multi-canal, notificações em banco |
| Testing | Pest, factories, fakes, testes HTTP |
| Eventos | listeners, broadcasting |
| Storage | Local/S3, URLs assinadas, uploads |
| Scheduling | Cron, prevenção de sobreposição |
| Container de serviço | bindings, singletons |
| Facades | 30+ assinaturas de métodos de facades |
| Broadcasting | canais privados/públicos |

---

## Início rápido

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## Estrutura de arquivos

```
laravel-docs-reader/
├── SKILL.md                         # Especificação do skill
├── README.md                         # Versão em inglês
├── README.zh-CN.md                  # Versão em chinês
├── .github/workflows/
│   └── update-docs.yml              # PR de atualização automática (semanal)
├── .cache/                          # Cache de documentação local
├── references/
│   ├── version-detection.md          # Lógica de detecção de versão
│   ├── version-diff.md              # Tabela de diferenças Laravel 10/11/12
│   ├── psr-12.md                    # Referência rápida PSR-12
│   ├── api-index.md                # Índice API completo
│   ├── artisan-commands.md          # Referência de comandos Artisan
│   ├── facades.md                  # Assinaturas de métodos Facade
│   ├── blade-directives.md          # Lista completa de diretivas Blade
│   ├── config-ref.md              # Referência de config/
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                  # CLI (14 comandos)
```

---

## Licença

MIT License
