# Laravel Docs Reader — Skill Specification

## Overview

**Skill Name**: Laravel Docs Reader
**Type**: Development Reference Skill
**Target**: OpenClaw Agent and Laravel developers
**Version**: 1.0.0

---

## Core Objectives

1. Provide instant, accurate access to official Laravel documentation during coding
2. Auto-detect the local Laravel version and serve the correct docs
3. Generate standard Laravel code that conforms to Laravel best practices and PSR-12
4. Highlight version differences across Laravel 10 / 11 / 12
5. Keep references up to date via GitHub Actions auto-PR

---

## Version Support

| Version | Status | Notes |
|---------|--------|-------|
| Laravel 12 | ✅ Default | Latest stable |
| Laravel 11 | ✅ Supported | Full reference |
| Laravel 10 | ✅ Supported | Full reference |

---

## Version Detection (Auto-Switch)

When the agent runs on a project, it auto-detects Laravel version:

1. `composer.json` → `"laravel/framework": "^12.x"`
2. `artisan --version`
3. `vendor/laravel/framework/src/Illuminate/Foundation/Application.php` → `VERSION` constant

Detection path: `references/version-detection.md`

---

## CLI Tool

```bash
php laradoc.php <command> [args]
```

| Command | Args | Description |
|---------|------|-------------|
| `search` | `<query>` | Natural language search |
| `version` | `[path]` | Detect local Laravel version |
| `current` | — | Show default version |
| `config` | `<file>` | Config reference (database/cache/mail/...) |
| `facade` | `<name>` | Facade method signatures |
| `artisan` | `<cmd>` | Artisan command help |
| `diff` | `<feature>` | Version diff (auth/routing/middleware/exception) |
| `generate` | `<type> <name>` | Code skeleton (controller/model/job/middleware) |
| `lang` | `<query>` | Blade directive lookup |

---

## Search Coverage

| Category | Topics |
|----------|--------|
| Routing | Basic routes, route groups, resource routes, named routes, middleware |
| Controllers | CRUD, REST, API, single-action, dependency injection |
| Models | Eloquent, relationships (12 types), mutators, scopes |
| Migrations | Schema builder, foreign keys, indexes, modifiers |
| Validation | Form requests, inline validation, custom rules |
| Auth | Breeze, Sanctum, Gates, Policies, JWT |
| Queues | Jobs, dispatching, failed job handling, Laravel Horizon |
| Cache | Store API, tags, atomic locks |
| Mail | Markdown, attachments, queuing |
| Notifications | Multi-channel, database notifications |
| Testing | Feature tests, unit tests, Pest, factories |
| Events | Listeners, broadcasting, queueable events |
| Storage | Local/S3/FTP, temporary URLs, uploads |
| Scheduling | Cron, recurring jobs, prevention of overlap |
| Service Container | Binding, singletons, contextual binding |
| Facades | All 30+ facades with method signatures |
| Broadcasting | Private/public channels, presence channels |
| Configuration | database, cache, mail, queue, auth, session |

---

## Code Generation

The `generate` command outputs standard Laravel code for:

- `controller` — RESTful API controller
- `model` — Eloquent model with fillable/casts/relationships
- `job` — Queueable job with failed handler
- `middleware` — HTTP middleware
- `request` — Form Request validation
- `notification` — Multi-channel notification
- `factory` — Model factory for testing

All output follows PSR-12 and Laravel conventions.

---

## Version Diff

| Feature | Laravel 10 | Laravel 11 | Laravel 12 |
|---------|-----------|-----------|-----------|
| Auth scaffolding | `laravel new --auth` | Breeze | Breeze (minimal) |
| Middleware registration | `Kernel.php` | `bootstrap/app.php` | `bootstrap/app.php` |
| Route registration | `RouteServiceProvider` | `bootstrap/app.php` | `bootstrap/app.php` |
| Exception handling | `app/Exceptions/Handler` | `bootstrap/app.php` | `bootstrap/app.php` |
| Cycle tasks | `app/Console/Kernel.php` | `routes/console.php` | `routes/console.php` |
| Broadcast channels | `routes/channels.php` | `routes/channels.php` | `routes/channels.php` |

---

## Auto-Update Mechanism

A GitHub Actions workflow runs weekly to:

1. Fetch latest `laravel/framework` version from Packagist
2. Compare against the skill's default version
3. If new version detected → auto-create a PR with updated references

```
.github/workflows/update-docs.yml
  ├── Schedule: Every Sunday 00:00 UTC
  └── Creates PR: updates SKILL.md + version-detection.md
```

Anyone using this skill can review the auto-PR and merge after verification.

---

## File Structure

```
laravel-docs-reader/
├── SKILL.md                          # This file
├── README.md                          # English (default)
├── README.zh-CN.md                    # Chinese
├── .github/
│   └── workflows/
│       └── update-docs.yml            # Auto-update PR workflow
├── references/
│   ├── version-detection.md           # Version detection logic
│   ├── version-diff.md                # Version diff table (10/11/12)
│   ├── api-index.md                  # Full API index
│   ├── artisan-commands.md           # All artisan commands
│   ├── facades.md                   # Facade method signatures
│   ├── blade-directives.md           # All Blade directives
│   ├── config-ref.md               # Config file reference
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                  # CLI tool (10 commands)
```

---

## Usage in OpenClaw

When the agent needs to write Laravel code:

1. Auto-detect project Laravel version
2. Map the request to the correct doc section
3. Return:
   - Official documentation summary
   - Code example (PSR-12, Laravel best practice)
   - Version differences (if applicable)
   - Notes / caveats

---

## Evaluation Criteria

Each doc entry is rated on:

| Criterion | Description |
|-----------|-------------|
| Accuracy | Matches official Laravel docs exactly |
| Completeness | Covers all common use cases |
| Freshness | Updated for latest Laravel version |
| Code Quality | PSR-12 compliant, idiomatic Laravel |
| Version Coverage | Covers 10 / 11 / 12 differences |

---

## Publishing

```bash
clawhub login --token <TOKEN>
clawhub publish laravel-docs-reader
```

Or submit at https://clawhub.com
