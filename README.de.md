# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## Überblick

**Laravel Docs Reader** ist ein OpenClaw Agent Skill, der Agenten (und Entwicklern) sofortigen Zugang zu präziser, versionsabhängiger Laravel-Offizialdokumentation bietet. Keine Vermutungen mehr — der Agent konsultiert die Offizialdokumentation, bevor er auch nur eine einzige Codezeile schreibt.

### Hauptfunktionen

- 🔍 **Natürliche Sprach-CLI-Suche** — `php laradoc.php search "how to create a middleware"` — funktioniert, als würde man die Dokumentation direkt befragen
- 📦 **Lokaler Dokumentations-Cache** — Dokumentation im Skill gebündelt, offline verfügbar, sofortiger Zugriff
- 🔄 **Automatische Versionserkennung** — erkennt die Laravel-Version Ihres Projekts (10/11/12) und liefert die richtige Dokumentation
- 📖 **Vollständige Dokumentationsabdeckung** — Routing, Controller, Models, Queues, Mail, Auth, Events, Broadcasting, Tests und mehr
- 🏭 **Codegenerierung** — generiert PSR-12-konforme Laravel-Code-Gerüste
- 📊 **Versionsunterschiede** — hebt hervor, was sich zwischen Laravel 10 / 11 / 12 geändert hat
- 📋 **PSR-12-Schnellreferenz** — eingebaut: `php laradoc.php psr`, `psr arrays`, `psr naming`
- 🤖 **Automatische Aktualisierung per PR** — GitHub Actions überwacht neue Laravel-Releases und erstellt automatisch einen PR zur Aktualisierung dieses Skills
- 🔗 **Laravel Package Search-Integration** — nach jedem Suchergebnis schlägt der Agent `laravel-package-search` zur Paketfindung vor

---

## CLI — 14 Befehle

```bash
# ── Hauptsuche ─────────────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # Natürliche Sprachsuche
php laradoc.php search "how to send a notification"
# ← Zeigt Package Search-Vorschlag nach jedem Ergebnis

# ── Version ─────────────────────────────────────────────────────
php laradoc.php version                   # Aktuelles Projekt automatisch erkennen
php laradoc.php version /path/to/project  # spezifisches Projekt
php laradoc.php current                 # Standardversion anzeigen (Laravel 12)

# ── Config & Facades ─────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & Diff ──────────────────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── Codegenerierung ──────────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Blade-Direktiven ──────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── PSR-12-Schnellreferenz ──────────────────────────────────
php laradoc.php psr                   # Vollständige PSR-12-Regeltabelle
php laradoc.php psr arrays          # Array-Regeln
php laradoc.php psr naming          # Namenskonventionen

# ── Cache & Aktualisierung ──────────────────────────────────
php laradoc.php cache                  # Lokalen Cache-Status anzeigen
php laradoc.php update                 # Zwangsaktualisierung von GitHub
php laradoc.php subscribe              # Abonnement-/Auto-Update-Status anzeigen
```

---

## Automatische Versionserkennung

Der CLI erkennt automatisch die Laravel-Version des Projekts:

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

Wenn kein Projekt gefunden wird, ist die Standardversion **Laravel 12**.

---

## Automatische Aktualisierung (GitHub Actions PR)

So bleibt der Skill aktuell:

- **Wöchentliche Prüfung**: GitHub Actions läuft jeden Sonntag um 00:00 UTC
- **Neue Versionserkennung**: vergleicht mit der neuesten Version von `laravel/framework` auf Packagist
- **Automatischer PR**: wenn eine neue Version erkannt wird, wird ein PR erstellt, der SKILL.md, version-detection.md und version-diff.md aktualisiert

Benutzer dieses Skills können den automatisch generierten PR prüfen, bevor sie ihn fusionieren.

---

## Dokumentationsabdeckung

| Kategorie | Themen |
|----------|--------|
| Routing | Basic routes, route groups, resource routes, benannte Routen, middleware |
| Controller | CRUD, REST, API, Single-Action, Abhängigkeitsinjektion |
| Models | Eloquent, alle 12 Beziehungstypen, Scopes, Casts |
| Migrations | Schema-Builder, Fremdschlüssel, Indizes |
| Validation | Form Requests, Inline, Custom Rules |
| Auth | Breeze, Sanctum, Gates, Policies |
| Queues | Jobs, Dispatch, Fehlerbehandlung, Laravel Horizon |
| Cache | Store API, Redis Tags, atomare Locks |
| Mail | Markdown, Anhänge, Queued Mail |
| Notifications | Multi-Channel, Datenbank-Notifications |
| Testing | Pest, Factories, Fakes, HTTP-Tests |
| Events | Listener, Broadcasting |
| Storage | Local/S3, signierte URLs, Uploads |
| Scheduling | Cron, Overlap-Schutz |
| Service Container | Bindings, Singletons |
| Facades | 30+ Facade-Methodensignaturen |
| Broadcasting | Private/Public Channels |

---

## Schnellstart

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## Dateistruktur

```
laravel-docs-reader/
├── SKILL.md                         # Skill-Spezifikation
├── README.md                         # Englische Version
├── README.zh-CN.md                  # Chinesische Version
├── .github/workflows/
│   └── update-docs.yml              # Wöchentlicher Auto-Update-PR
├── .cache/                          # Lokaler Dokumentationscache
├── references/
│   ├── version-detection.md          # Versionserkennungslogik
│   ├── version-diff.md              # Laravel 10/11/12-Diff-Tabelle
│   ├── psr-12.md                    # PSR-12-Schnellreferenz
│   ├── api-index.md                # Vollständiger API-Index
│   ├── artisan-commands.md          # Artisan-Befehlsreferenz
│   ├── facades.md                  # Facade-Methodensignaturen
│   ├── blade-directives.md          # Vollständige Blade-Direktivenliste
│   ├── config-ref.md              # config/-Referenz
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                  # CLI (14 Befehle)
```

---

## Lizenz

MIT License
