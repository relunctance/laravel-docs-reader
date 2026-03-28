# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## Panoramica

**Laravel Docs Reader** è uno skill OpenClaw Agent che fornisce agli agenti (e sviluppatori) accesso immediato a documentazione Laravel ufficiale accurata e version-aware. Niente più supposizioni — l'agente consulta la documentazione ufficiale prima di scrivere una sola riga di codice.

### Funzionalità principali

- 🔍 **Ricerca CLI in linguaggio naturale** — `php laradoc.php search "how to create a middleware"` — funziona come chiedere direttamente alla documentazione
- 📦 **Cache documentazione locale** — documentazione in bundle nello skill, disponibile offline, accesso immediato
- 🔄 **Rilevamento automatico versione** — rileva la versione Laravel del tuo progetto (10/11/12) e fornisce la documentazione corretta
- 📖 **Copertura completa della documentazione** — routing, controller, modelli, code, mail, auth, eventi, broadcasting, test e altro
- 🏭 **Generazione codice** — genera scheletri di codice Laravel conformi a PSR-12
- 📊 **Differenze di versione** — evidenzia cosa è cambiato tra Laravel 10 / 11 / 12
- 📋 **Riferimento rapido PSR-12** — integrato: `php laradoc.php psr`, `psr arrays`, `psr naming`
- 🤖 **Aggiornamento automatico via PR** — GitHub Actions controlla nuove release Laravel e crea automaticamente un PR per aggiornare questo skill
- 🔗 **Integrazione con Laravel Package Search** — dopo ogni risultato di ricerca, l'agente suggerisce `laravel-package-search` per scoprire package di terze parti

---

## CLI — 14 comandi

```bash
# ── Ricerca principale ───────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # Ricerca in linguaggio naturale
php laradoc.php search "how to send a notification"
# ← Mostra suggerimento Package Search dopo ogni risultato

# ── Versione ───────────────────────────────────────────────────
php laradoc.php version                   # Rileva automaticamente il progetto attuale
php laradoc.php version /path/to/project  # progetto specifico
php laradoc.php current                 # Mostra versione predefinita (Laravel 12)

# ── Config & Facades ──────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & Diff ───────────────────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── Generazione codice ───────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Direttive Blade ──────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── Riferimento PSR-12 ─────────────────────────────────────
php laradoc.php psr                   # Tabella completa regole PSR-12
php laradoc.php psr arrays          # Regole array
php laradoc.php psr naming          # Convenzioni di denominazione

# ── Cache & Aggiornamento ──────────────────────────────────
php laradoc.php cache                  # Mostra stato cache locale
php laradoc.php update                 # Forza aggiornamento da GitHub
php laradoc.php subscribe              # Mostra stato iscrizione/aggiornamento auto
```

---

## Rilevamento automatico versione

Il CLI rileva automaticamente la versione Laravel del progetto:

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

Se non viene trovato nessun progetto, il valore predefinito è **Laravel 12**.

---

## Aggiornamento automatico (PR GitHub Actions)

Come lo skill resta aggiornato:

- **Verifica settimanale**: GitHub Actions viene eseguito ogni domenica alle 00:00 UTC
- **Rilevamento nuova versione**: confronta con l'ultima versione di `laravel/framework` su Packagist
- **PR automatico**: se viene rilevata una nuova versione, crea un PR che aggiorna SKILL.md, version-detection.md, version-diff.md

Gli utenti di questo skill possono esaminare il PR generato automaticamente prima di unirlo.

---

## Copertura documentazione

| Categoria | Argomenti |
|----------|-----------|
| Routing | route basic, gruppi, resource, nominate, middleware |
| Controller | CRUD, REST, API, single-action, dependency injection |
| Modelli | Eloquent, tutti i 12 tipi di relazione, scopes, cast |
| Migrazioni | costruttore schema, chiavi esterne, indici |
| Validazione | Form Requests, inline, regole personalizzate |
| Auth | Breeze, Sanctum, Gates, Policies |
| Code | Jobs, dispatch, gestione errori, Laravel Horizon |
| Cache | Store API, tag Redis, lock atomici |
| Mail | Markdown, allegati, mail in coda |
| Notifiche | multi-canale, notifiche database |
| Testing | Pest, factories, fakes, test HTTP |
| Eventi | listener, broadcasting |
| Storage | Local/S3, URL firmate, upload |
| Scheduling | Cron, prevenzione overlap |
| Container di servizio | binding, singleton |
| Facade | 30+ firme di metodi Facade |
| Broadcasting | canali privati/pubblici |

---

## Avvio rapido

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## Struttura dei file

```
laravel-docs-reader/
├── SKILL.md                         # Specifica dello skill
├── README.md                         # Versione inglese
├── README.zh-CN.md                  # Versione cinese
├── .github/workflows/
│   └── update-docs.yml              # PR aggiornamento automatico settimanale
├── .cache/                          # Cache documentazione locale
├── references/
│   ├── version-detection.md          # Logica rilevamento versione
│   ├── version-diff.md              # Tabella differenze Laravel 10/11/12
│   ├── psr-12.md                    # Riferimento rapido PSR-12
│   ├── api-index.md                # Indice API completo
│   ├── artisan-commands.md          # Riferimento comandi Artisan
│   ├── facades.md                  # Firme metodi Facade
│   ├── blade-directives.md          # Elenco completo direttive Blade
│   ├── config-ref.md              # Riferimento config/
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                  # CLI (14 comandi)
```

---

## Licenza

MIT License
