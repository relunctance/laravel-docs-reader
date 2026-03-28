# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## Descripción general

**Laravel Docs Reader** es un skill OpenClaw Agent que da a los agentes (y desarrolladores) acceso instantáneo a documentación Laravel oficial precisa y con versión. No más adivinanzas — el agente consulta la documentación oficial antes de escribir una sola línea de código.

### Funcionalidades principales

- 🔍 **Búsqueda CLI en lenguaje natural** — `php laradoc.php search "how to create a middleware"` — funciona como preguntar directamente a la documentación
- 📦 **Caché de documentación local** — documentación incluida en el skill, disponible sin conexión, acceso instantáneo
- 🔄 **Detección automática de versión** — detecta la versión Laravel de tu proyecto (10/11/12) y sirve la documentación correcta
- 📖 **Cobertura completa de documentación** — routing, controladores, modelos, colas, mail, auth, eventos, broadcasting, tests, y más
- 🏭 **Generación de código** — genera esqueletos de código Laravel compatibles con PSR-12
- 📊 **Diferencias de versión** — destaca qué cambió entre Laravel 10 / 11 / 12
- 📋 **Referencia rápida PSR-12** — integrada: `php laradoc.php psr`, `psr arrays`, `psr naming`
- 🤖 **Actualización automática por PR** — GitHub Actions vigila nuevos lanzamientos de Laravel y crea automáticamente un PR para actualizar este skill
- 🔗 **Integración con Laravel Package Search** — después de cada resultado de búsqueda, el agente sugiere `laravel-package-search` para descubrir packages de terceros

---

## CLI — 14 comandos

```bash
# ── Búsqueda principal ────────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # Búsqueda en lenguaje natural
php laradoc.php search "how to send a notification"
# ← Muestra sugerencia de Package Search después de cada resultado

# ── Versión ─────────────────────────────────────────────────────
php laradoc.php version                   # Detecta automáticamente el proyecto actual
php laradoc.php version /path/to/project  # proyecto específico
php laradoc.php current                 # Muestra la versión por defecto (Laravel 12)

# ── Config & Facades ───────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & Diff ─────────────────────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── Generación de código ───────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Directivas Blade ──────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── Referencia PSR-12 ─────────────────────────────────────────
php laradoc.php psr                   # Tabla PSR-12 completa
php laradoc.php psr arrays          # Reglas de arrays
php laradoc.php psr naming          # Convenciones de nombres

# ── Caché & Actualización ─────────────────────────────────────
php laradoc.php cache                  # Mostrar estado de caché local
php laradoc.php update                 # Forzar actualización desde GitHub
php laradoc.php subscribe              # Mostrar estado de suscripción/actualización auto
```

---

## Detección automática de versión

El CLI detecta automáticamente la versión Laravel del proyecto:

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

Si no se encuentra ningún proyecto, el valor predeterminado es **Laravel 12**.

---

## Actualización automática (PR de GitHub Actions)

Cómo se mantiene actualizado el skill:

- **Verificación semanal**: GitHub Actions se ejecuta cada domingo a las 00:00 UTC
- **Detección de nueva versión**: compara con la última versión de `laravel/framework` en Packagist
- **PR automático**: si se detecta una nueva versión, crea un PR que actualiza SKILL.md, version-detection.md, version-diff.md

Los usuarios de este skill pueden revisar el PR generado automáticamente antes de fusionarlo.

---

## Cobertura documental

| Categoría | Temas |
|----------|--------|
| Routing | rutas básicas, grupos, resource, nombradas, middleware |
| Controladores | CRUD, REST, API, acción única, inyección de dependencias |
| Modelos | Eloquent, 12 tipos de relaciones, scopes, casts |
| Migraciones | constructor de esquema, claves ajenas, índices |
| Validación | Form Requests, inline, reglas personalizadas |
| Auth | Breeze, Sanctum, Gates, Policies |
| Colas | Jobs, dispatch, manejo de fallos, Laravel Horizon |
| Caché | Store API, tags Redis, locks atómicos |
| Mail | Markdown, adjuntos, mail en cola |
| Notificaciones | multi-canal, notificaciones en base de datos |
| Testing | Pest, factories, fakes, tests HTTP |
| Eventos | listeners, broadcast |
| Storage | Local/S3, URLs firmadas, uploads |
| Scheduling | Cron, prevención de solapamiento |
| Contenedor de servicios | bindings, singletons |
| Facades | 30+ firmas de métodos de facades |
| Broadcasting | canales privados/públicos |

---

## Inicio rápido

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## Estructura de archivos

```
laravel-docs-reader/
├── SKILL.md                         # Especificación del skill
├── README.md                         # Versión inglesa
├── README.zh-CN.md                  # Versión china
├── .github/workflows/
│   └── update-docs.yml              # PR de actualización automática (semanal)
├── .cache/                          # Caché de documentación local
├── references/
│   ├── version-detection.md          # Lógica de detección de versión
│   ├── version-diff.md              # Tabla de diferencias Laravel 10/11/12
│   ├── psr-12.md                    # Referencia rápida PSR-12
│   ├── api-index.md                # Índice API completo
│   ├── artisan-commands.md          # Referencia de comandos Artisan
│   ├── facades.md                  # Firmas de métodos de Facades
│   ├── blade-directives.md          # Lista completa de directivas Blade
│   ├── config-ref.md              # Referencia de config/
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                # CLI (14 comandos)
```

---

## Licencia

MIT License
