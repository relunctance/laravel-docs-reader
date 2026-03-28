# Laravel Docs Reader

**English** | [中文](README.zh-CN.md)

---

## Overview

**Laravel Docs Reader** is an OpenClaw Agent Skill that gives agents (and developers) instant access to accurate, version-aware Laravel documentation. No more guessing — the agent looks up the official docs before writing a single line of code.

### Key Features

- 🔍 **Natural Language CLI Search** — `php laradoc.php search "how to create a queue job"` — works like asking the docs directly
- 🔄 **Auto Version Detection** — detects your project's Laravel version (10/11/12) and serves the right docs
- 📖 **Full Documentation Coverage** — routing, controllers, models, queues, mail, auth, events, broadcasting, testing, and more
- 🏭 **Code Generation** — generates PSR-12 compliant Laravel code skeletons
- 📊 **Version Diff** — highlights what changed across Laravel 10 / 11 / 12
- 🤖 **Auto-Update via PR** — a GitHub Actions workflow watches for new Laravel releases and auto-creates a PR to update this skill

---

## CLI Tool — 10 Commands

```bash
# Natural language search — ask the docs anything
php laradoc.php search "how to create a middleware"
php laradoc.php search "how to send a notification"
php laradoc.php search "queue job with retry and failure handling"
php laradoc.php search "redis cache with tags"

# Version detection — auto-detects your project's Laravel version
php laradoc.php version                     # current directory
php laradoc.php version /path/to/project    # specific project
php laradoc.php current                    # show default version

# Config reference
php laradoc.php config database
php laradoc.php config cache
php laradoc.php config mail

# Facade methods
php laradoc.php facade Cache
php laradoc.php facade DB
php laradoc.php facade Route

# Artisan commands
php laradoc.php artisan make:controller
php laradoc.php artisan migrate

# Version differences
php laradoc.php diff auth         # auth: Laravel 10 vs 11 vs 12
php laradoc.php diff routing
php laradoc.php diff middleware
php laradoc.php diff exception

# Code generation
php laradoc.php generate controller UserController
php laradoc.php generate model    Post
php laradoc.php generate job      ProcessUpload
php laradoc.php generate request  StorePostRequest
php laradoc.php generate notification InvoicePaid

# Blade directive lookup
php laradoc.php lang "loop"
php laradoc.php lang "csrf"
php laradoc.php lang "auth"
```

---

## Version Detection

The CLI auto-detects your project's Laravel version:

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

If no project found, defaults to **Laravel 12**.

---

## Auto-Update via GitHub Actions

This skill stays current automatically:

- **Weekly check**: A GitHub Actions workflow runs every Sunday at 00:00 UTC
- **New version detection**: Compares Packagist's `laravel/framework` latest version
- **Auto-PR**: If a new Laravel version is detected, the workflow creates a PR updating the skill's references

```yaml
# .github/workflows/update-docs.yml
on:
  schedule:
    - cron: '0 0 * * 0'  # Weekly on Sunday
  workflow_dispatch:         # Manual trigger
```

Anyone using this skill can review the auto-generated PR before merging — no surprises.

---

## Coverage

| Category | Topics |
|----------|--------|
| Routing | Basic routes, groups, resource, named, middleware |
| Controllers | CRUD, REST, API, single-action, DI |
| Models | Eloquent, all 12 relationship types, scopes, casts |
| Migrations | Schema builder, foreign keys, indexes |
| Validation | Form requests, inline, custom rules |
| Auth | Breeze, Sanctum, Gates, Policies |
| Queues | Jobs, dispatching, Horizon |
| Cache | Store API, Redis tags, locks |
| Mail | Markdown, attachments, queuing |
| Notifications | Multi-channel, database |
| Testing | Pest, factories, fakes, HTTP tests |
| Events | Listeners, broadcasting |
| Storage | Local/S3, signed URLs |
| Scheduling | Cron, overlapping prevention |
| Container | Binding, singletons |
| Facades | 30+ facade method signatures |
| Broadcasting | Private/public channels |

---

## Quick Start

### Agent (OpenClaw)

When activated, the skill:

1. Detects project Laravel version (or uses default 12)
2. Maps your request to the correct doc section
3. Returns: summary + code example + version notes + best practices

### Developer (CLI)

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## Installation

This is an OpenClaw Agent Skill.

```bash
clawhub install laravel-docs-reader
# or
clawhub login --token <YOUR_TOKEN> && clawhub publish laravel-docs-reader
```

---

## File Structure

```
laravel-docs-reader/
├── SKILL.md                         # Skill specification
├── README.md                         # English (this file)
├── README.zh-CN.md                  # Chinese
├── .github/workflows/
│   └── update-docs.yml               # Auto-update PR (weekly)
├── references/
│   ├── version-detection.md          # Version detection logic
│   ├── version-diff.md             # Laravel 10/11/12 diff
│   ├── api-index.md                # Full API reference
│   ├── artisan-commands.md         # All artisan commands
│   ├── facades.md                 # Facade method signatures
│   ├── blade-directives.md         # All Blade directives
│   ├── config-ref.md              # config/ reference
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                # CLI tool (10 commands)
```

---

## Contributing

Found outdated content or missing docs?
- Open an issue: https://github.com/relunctance/laravel-docs-reader/issues
- Or submit a PR directly

---

## License

MIT License
