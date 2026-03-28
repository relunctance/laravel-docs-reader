# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## Vue d'ensemble

**Laravel Docs Reader** est un skill OpenClaw Agent qui donne aux agents (et développeurs) un accès instantané à la documentation Laravel officielle, précise et adaptée à la version. Plus de devinette — l'agent consulte la documentation officielle avant d'écrire une seule ligne de code.

### Fonctionnalités principales

- 🔍 **Recherche CLI en langage naturel** — `php laradoc.php search "how to create a middleware"` — fonctionne comme si vous interrogeiez directement la documentation
- 📦 **Cache de documentation local** — documentation incluse dans le skill, disponible hors ligne, instantané
- 🔄 **Détection automatique de version** — détecte la version Laravel de votre projet (10/11/12) et fournit la documentation correspondante
- 📖 **Couverture complète de la documentation** — routage, contrôleurs, modèles, files d'attente, mail, auth, événements, broadcast, tests, et plus
- 🏭 **Génération de code** — génère des squelettes de code Laravel conformes à PSR-12
- 📊 **Différences de version** — met en évidence les changements entre Laravel 10 / 11 / 12
- 📋 **Référence rapide PSR-12** — intégrée : `php laradoc.php psr`, `psr arrays`, `psr naming`
- 🤖 **Mise à jour automatique par PR** — GitHub Actions vérifie les nouvelles versions Laravel et crée automatiquement une PR pour mettre à jour ce skill
- 🔗 **Intégration Laravel Package Search** — après chaque résultat de recherche, l'agent suggère `laravel-package-search` pour découvrir des packages tiers

---

## CLI — 14 commandes

```bash
# ── Recherche principale ──────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # Recherche en langage naturel
php laradoc.php search "how to send a notification"
# ← Affiche une suggestion Package Search après chaque résultat

# ── Version ─────────────────────────────────────────────────────
php laradoc.php version                   # Détecte automatiquement le projet actuel
php laradoc.php version /path/to/project  # projet spécifique
php laradoc.php current                  # Affiche la version par défaut (Laravel 12)

# ── Config & Facades ────────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & Diff ─────────────────────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── Génération de code ─────────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Directives Blade ───────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── Référence PSR-12 ────────────────────────────────────────────
php laradoc.php psr                   # Tableau PSR-12 complet
php laradoc.php psr arrays          # Règles sur les tableaux
php laradoc.php psr naming           # Conventions de nommage

# ── Cache & Mise à jour ────────────────────────────────────────
php laradoc.php cache                  # Afficher l'état du cache local
php laradoc.php update                 # Forcer la mise à jour depuis GitHub
php laradoc.php subscribe              # Afficher l'état d'abonnement/mise à jour auto
```

---

## Détection automatique de version

Le CLI détecte automatiquement la version Laravel du projet :

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

Si aucun projet n'est trouvé, la version par défaut est **Laravel 12**.

---

## Mise à jour automatique (PR GitHub Actions)

Comment le skill reste à jour :

- **Vérification hebdomadaire** : GitHub Actions s'exécute chaque dimanche à 00h00 UTC
- **Détection de nouvelle version** : compare avec la dernière version `laravel/framework` sur Packagist
- **PR automatique** : si une nouvelle version est détectée, crée une PR mettant à jour SKILL.md, version-detection.md, version-diff.md

Les utilisateurs de ce skill peuvent examiner la PR générée automatiquement avant de la fusionner.

---

## Couverture documentaire

| Catégorie | Sujets |
|----------|--------|
| Routage | routes basiques, groupes de routes, routes resource, routes nommées, middleware |
| Contrôleurs | CRUD, REST, API, contrôleur à action unique, injection de dépendances |
| Modèles | Eloquent, les 12 types de relations, scopes, casts |
| Migrations | constructeur de schéma, clés étrangères, index |
| Validation | Form Requests, validation inline, règles personnalisées |
| Auth | Breeze, Sanctum, Gates, Policies |
| Files d'attente | Jobs, dispatch, gestion des échecs, Laravel Horizon |
| Cache | Store API, tags Redis, locks atomiques |
| Mail | Markdown, pièces jointes, mail en file d'attente |
| Notifications | multi-canal, notifications en base de données |
| Tests | Pest, factories, fakes, tests HTTP |
| Événements | listeners, broadcast |
| Storage | Local/S3, URLs signées, uploads |
| Scheduling | Cron, prévention du chevauchement |
| Container de services | bindings, singletons |
| Facades | signatures de méthodes de 30+ facades |
| Broadcast | canaux privés/publics |

---

## Démarrage rapide

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## Structure des fichiers

```
laravel-docs-reader/
├── SKILL.md                         # Spécification du skill
├── README.md                         # Version anglaise
├── README.zh-CN.md                  # Version chinoise
├── .github/workflows/
│   └── update-docs.yml              # PR de mise à jour automatique (hebdomadaire)
├── .cache/                          # Cache de documentation local
├── references/
│   ├── version-detection.md          # Logique de détection de version
│   ├── version-diff.md              # Tableau des différences Laravel 10/11/12
│   ├── psr-12.md                   # Référence rapide PSR-12
│   ├── api-index.md                # Index API complet
│   ├── artisan-commands.md          # Référence commandes Artisan
│   ├── facades.md                  # Signatures de méthodes Facade
│   ├── blade-directives.md          # Liste complète des directives Blade
│   ├── config-ref.md              # Référence config/
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                # CLI (14 commandes)
```

---

## Licence

MIT License
