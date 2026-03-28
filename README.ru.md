# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## Обзор

**Laravel Docs Reader** — это OpenClaw Agent навык, предоставляющий агентам (и разработчикам) мгновенный доступ к точной, версионно-ориентированной официальной документации Laravel. Никаких догадок — агент обращается к официальной документации перед написанием кода.

### Основные функции

- 🔍 **Поиск CLI на естественном языке** — `php laradoc.php search "how to create a middleware"` — работает как прямой запрос к документации
- 📦 **Локальный кэш документации** — документация встроена в навык, доступна офлайн, мгновенный доступ
- 🔄 **Автоопределение версии** — определяет версию Laravel вашего проекта (10/11/12) и предоставляет соответствующую документацию
- 📖 **Полное покрытие документации** — роутинг, контроллеры, модели, очереди, почта, аутентификация, события, broadcasting, тестирование и другое
- 🏭 **Генерация кода** — генерирует костяки кода Laravel, соответствующие PSR-12
- 📊 **Различия версий** — подсвечивает, что изменилось между Laravel 10 / 11 / 12
- 📋 **Быстрая справка по PSR-12** — встроено: `php laradoc.php psr`, `psr arrays`, `psr naming`
- 🤖 **Автообновление через PR** — GitHub Actions следит за новыми версиями Laravel и автоматически создаёт PR для обновления этого навыка
- 🔗 **Интеграция с Laravel Package Search** — после каждого результата поиска агент предлагает `laravel-package-search` для поиска сторонних пакетов

---

## CLI — 14 команд

```bash
# ── Основной поиск ──────────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # Поиск на естественном языке
php laradoc.php search "how to send a notification"
# ← После каждого результата показывает предложение Package Search

# ── Версия ────────────────────────────────────────────────────
php laradoc.php version                   # Автоопределение текущего проекта
php laradoc.php version /path/to/project  # конкретный проект
php laradoc.php current                 # Показать версию по умолчанию (Laravel 12)

# ── Конфиг & Facades ────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & Различия версий ──────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── Генерация кода ─────────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Директивы Blade ─────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── Быстрая справка PSR-12 ─────────────────────────────────
php laradoc.php psr                   # Полная таблица правил PSR-12
php laradoc.php psr arrays          # Правила массивов
php laradoc.php psr naming          # Соглашения именования

# ── Кэш & Обновление ───────────────────────────────────────
php laradoc.php cache                  # Показать состояние локального кэша
php laradoc.php update                 # Принудительно обновить с GitHub
php laradoc.php subscribe              # Показать состояние подписки/автообновления
```

---

## Автоопределение версии

CLI автоматически определяет версию Laravel проекта:

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

Если проект не найден, по умолчанию используется **Laravel 12**.

---

## Автообновление (PR через GitHub Actions)

Как навык остаётся актуальным:

- **Еженедельная проверка**: GitHub Actions выполняется каждое воскресенье в 00:00 UTC
- **Обнаружение новой версии**: сравнивает с последней версией `laravel/framework` на Packagist
- **Автоматический PR**: при обнаружении новой версии создаётся PR с обновлением SKILL.md, version-detection.md, version-diff.md

Пользователи этого навыка могут проверить автоматически сгенерированный PR перед слиянием.

---

## Покрытие документации

| Категория | Темы |
|----------|--------|
| Роуутинг | Basic routes, route groups, resource routes, named routes, middleware |
| Контроллеры | CRUD, REST, API, single-action, внедрение зависимостей |
| Модели | Eloquent, все 12 типов отношений, scopes, casts |
| Миграции | конструктор схемы, внешние ключи, индексы |
| Валидация | Form Requests, inline, кастомные правила |
| Аутентификация | Breeze, Sanctum, Gates, Policies |
| Очереди | Jobs, dispatch, обработка ошибок, Laravel Horizon |
| Кэш | Store API, Redis-теги, атомарные блокировки |
| Почта | Markdown, вложения, очередная почта |
| Уведомления | мультиканальные, уведомления в БД |
| Тестирование | Pest, factories, fakes, HTTP-тесты |
| События | listeners, broadcasting |
| Хранилище | Local/S3, подписанные URL, загрузки |
| Планировщик | Cron, предотвращение перекрытия |
| Сервис-контейнер | bindings, singletons |
| Facades | сигнатуры 30+ методов facade |
| Broadcasting | приватные/публичные каналы |

---

## Быстрый старт

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## Структура файлов

```
laravel-docs-reader/
├── SKILL.md                         # Спецификация навыка
├── README.md                         # Английская версия
├── README.zh-CN.md                  # Китайская версия
├── .github/workflows/
│   └── update-docs.yml              # PR еженедельного автообновления
├── .cache/                          # Локальный кэш документации
├── references/
│   ├── version-detection.md          # Логика определения версии
│   ├── version-diff.md              # Таблица различий Laravel 10/11/12
│   ├── psr-12.md                    # Быстрая справка PSR-12
│   ├── api-index.md                # Полный индекс API
│   ├── artisan-commands.md          # Справка по командам Artisan
│   ├── facades.md                  # Сигнатуры методов Facade
│   ├── blade-directives.md          # Полный список директив Blade
│   ├── config-ref.md              # Справка по config/
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                  # CLI (14 команд)
```

---

## Лицензия

MIT License
