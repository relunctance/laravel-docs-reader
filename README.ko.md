# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## 개요

**Laravel Docs Reader**는 Agent(와 개발자)에게 정확하고 버전 인식 Laravel 공식 문서에 대한 즉각적인 접근을 제공하는 OpenClaw Agent 스킬입니다. 추측 불필요 — Agent는 코드 작성 전에 공식 문서를 조회합니다.

### 주요 기능

- 🔍 **자연어 CLI 검색** — `php laradoc.php search "how to create a middleware"` — 문서에 묻는 것처럼 동작
- 📦 **로컬 문서 캐시** — 스킬에 번들되어 오프라인에서 즉시 접근 가능
- 🔄 **자동 버전 감지** — 프로젝트 Laravel 버전(10/11/12)을 감지하고 적절한 문서를 제공
- 📖 **완전한 문서 범위** — 라우팅, 컨트롤러, 모델, 큐, 메일, 인증, 이벤트, 방송, 테스트 등
- 🏭 **코드 생성** — PSR-12 준수 Laravel 코드 스켈레톤 생성
- 📊 **버전 차이** — Laravel 10 / 11 / 12 변경 사항 하이라이트
- 📋 **PSR-12 빠른 참조** — 내장: `php laradoc.php psr`, `psr arrays`, `psr naming`
- 🤖 **자동 업데이트 PR** — GitHub Actions이 Laravel 새 버전을 감시하고 PR 자동 생성
- 🔗 **Laravel Package Search 연동** — 검색 결과마다 agent가 `laravel-package-search` 제안

---

## CLI 도구 — 14개 명령

```bash
# ── 핵심 검색 ──────────────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # 자연어 검색
php laradoc.php search "how to send a notification"
# ← 검색 결과 후 Package Search 제안 표시

# ── 버전 ─────────────────────────────────────────────────────
php laradoc.php version                   # 현재 프로젝트 자동 감지
php laradoc.php version /path/to/project  # 특정 프로젝트
php laradoc.php current                  # 기본 버전 표시 (Laravel 12)

# ── 설정 & Facade ────────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & 버전 차이 ────────────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── 코드 생성 ──────────────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Blade 지시자 ─────────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── PSR-12 빠른 참조 ─────────────────────────────────────────
php laradoc.php psr                   # 완전한 PSR-12 규칙 표
php laradoc.php psr arrays           # 배열 규칙
php laradoc.php psr naming           # 명명 규칙

# ── 캐시 & 업데이트 ─────────────────────────────────────────
php laradoc.php cache                  # 로컬 캐시 상태 표시
php laradoc.php update                 # GitHub에서 강제 가져오기
php laradoc.php subscribe              # 구독 / 자동 업데이트 상태 표시
```

---

## 자동 버전 감지

CLI는 프로젝트 Laravel 버전을 자동으로 감지합니다:

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

프로젝트를 찾을 수 없으면 기본값은 **Laravel 12**입니다.

---

## 자동 업데이트(GitHub Actions PR)

스킬을 최신 상태로 유지하는 방법:

- **매주 확인**: GitHub Actions가 매주 일요일 00:00 UTC에 실행
- **새 버전 감지**: Packagist의 `laravel/framework` 최신 버전과 비교
- **자동 PR**: 새 버전 감지 시 SKILL.md, version-detection.md, version-diff.md를 업데이트하는 PR 자동 생성

이 스킬을 사용하는 사용자는 자동 생성된 PR을 검토하고 확인 후 병합할 수 있습니다.

---

## 문서 범위

| 카테고리 | 주제 |
|----------|------|
| 라우팅 | Basic routes, route groups, resource routes, named routes, middleware |
| 컨트롤러 | CRUD, REST, API, single-action, 의존성 주입 |
| 모델 | Eloquent, 전12관계, scopes, casts |
| 마이그레이션 | Schema builder, 외래 키, 인덱스 |
| 검증 | Form requests, 인라인, 커스텀 rules |
| 인증 | Breeze, Sanctum, Gates, Policies |
| 큐 | Jobs, 디스패치, 실패 처리, Laravel Horizon |
| 캐시 | Store API, Redis 태그, atomic locks |
| 메일 | Markdown, 첨부, 큐 mail |
| 알림 | 멀티채널, 데이터베이스 알림 |
| 테스트 | Pest, factories, fakes, HTTP 테스트 |
| 이벤트 | 리스너, 방송 |
| 스토리지 | Local/S3, signed URLs, uploads |
| 스케줄링 | Cron, overlap 방지 |
| 서비스 컨테이너 | 바인딩, singletons |
| Facades | 30+ Facade 메서드 시그니처 |
| 방송 | 프라이빗/퍼블릭 채널 |

---

## 빠른 시작

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## 라이선스

MIT License
