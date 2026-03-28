# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## 概述

**Laravel Docs Reader** 是一個 OpenClaw Agent 技能，讓 Agent（和開發者）即時存取準確、版本感知的 Laravel 官方文檔。無需猜測——Agent 在寫程式前先查官方文檔。

### 核心功能

- 🔍 **自然語言 CLI 檢索** — `php laradoc.php search "how to create a middleware"` — 像在問官方文檔
- 📦 **本地文檔緩存** — 文檔內置在 skill 中，離線可用，首次使用後無需互聯網
- 🔄 **自動版本檢測** — 檢測專案 Laravel 版本（10/11/12）並提供對應文檔
- 📖 **完整文檔覆蓋** — 路由、控制器、模型、隊列、郵件、認證、事件、廣播、測試等
- 🏭 **代碼生成** — 生成符合 PSR-12 標準的 Laravel 代碼骨架
- 📊 **版本差異** — 標注 Laravel 10 / 11 / 12 各版本差異
- 📋 **PSR-12 速查** — 內置：`php laradoc.php psr`、`psr arrays`、`psr naming`
- 🤖 **自動更新 PR** — GitHub Actions 每週檢測新版本，自動創建 PR 更新文檔
- 🔗 **Laravel Package Search 聯動** — 每次搜索結果後，Agent 建議使用 `laravel-package-search` 發現第三方包

---

## CLI 工具 — 14 個命令

```bash
# ── 核心搜索 ──────────────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # 自然語言檢索
php laradoc.php search "how to send a notification"
# ← 搜索結果後自動顯示 Package Search 提示

# ── 版本 ──────────────────────────────────────────────────────
php laradoc.php version                   # 自動檢測當前專案版本
php laradoc.php version /path/to/project  # 指定專案
php laradoc.php current                  # 顯示預設版本 (Laravel 12)

# ── 配置 & Facade ─────────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & 版本差異 ────────────────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── 代碼生成 ──────────────────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Blade 指令 ────────────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── PSR-12 速查 ──────────────────────────────────────────────
php laradoc.php psr                   # 完整 PSR-12 規則表
php laradoc.php psr arrays           # 數組格式規則
php laradoc.php psr naming           # 命名規範

# ── 緩存 & 更新 ─────────────────────────────────────────────
php laradoc.php cache                  # 查看本地緩存狀態
php laradoc.php update                 # 強制從 GitHub 拉取最新文檔
php laradoc.php subscribe              # 查看訂閱 / 自動更新狀態
```

---

## 版本自動檢測

CLI 自動檢測專案 Laravel 版本：

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

未檢測到專案時，預設為 **Laravel 12**。

---

## 自動更新機制（GitHub Actions PR）

Skill 保持最新的方式：

- **每週檢查**：GitHub Actions 每週日 00:00 UTC 運行一次
- **新版本檢測**：對比 Packagist 上的 `laravel/framework` 最新版本
- **自動 PR**：檢測到新版本後，自動創建 PR 更新 skill 的文檔引用

使用此 skill 的用戶可以審核自動生成的 PR，確認無誤後再合併。

---

## 文檔覆蓋範圍

| 分類 | 覆蓋主題 |
|------|---------|
| 路由 | 基礎路由、路由組、資源路由、命名路由、中間件 |
| 控制器 | CRUD、REST、API、單動作、依賴注入 |
| 模型 | Eloquent、12 種關聯、作用域、類型轉換 |
| 遷移 | Schema 構建器、外鍵、索引、欄位修飾符 |
| 驗證 | Form Request、內聯驗證、自訂規則 |
| 認證 | Breeze、Sanctum、Gates、Policies |
| 隊列 | Job、派發、失敗處理、Laravel Horizon |
| 緩存 | Store API、Redis 標籤、原子鎖 |
| 郵件 | Markdown、附件、隊列郵件 |
| 通知 | 多通道、數據庫通知 |
| 測試 | Pest、工廠、Fakes、HTTP 測試 |
| 事件 | 監聽器、廣播、可隊列化事件 |
| 存儲 | Local/S3、簽名 URL、上傳 |
| 定時任務 | Cron、防重疊 |
| 服務容器 | 綁定、單例、上下文綁定 |
| Facades | 30+ Facade 方法簽名 |
| 廣播 | 私有/公開頻道 |

---

## 快速開始

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## 檔案結構

```
laravel-docs-reader/
├── SKILL.md                         # Skill 規範文檔
├── README.md                         # 英文版
├── README.zh-CN.md                  # 簡體中文版
├── .github/workflows/
│   └── update-docs.yml              # 每週自動更新 PR
├── .cache/                          # 本地文檔緩存
├── references/
│   ├── version-detection.md          # 版本檢測邏輯
│   ├── version-diff.md              # Laravel 10/11/12 差異表
│   ├── psr-12.md                   # PSR-12 速查參考
│   ├── api-index.md                # 完整 API 索引
│   ├── artisan-commands.md          # Artisan 命令參考
│   ├── facades.md                  # Facade 方法簽名
│   ├── blade-directives.md          # Blade 指令完整列表
│   ├── config-ref.md              # config/ 配置參考
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                # CLI 工具（14 個命令）
```

---

## 參與貢獻

發現內容過時或缺少文檔？
- 🐛 [提交 Issue](https://github.com/relunctance/laravel-docs-reader/issues)
- 🔧 [提交 PR](https://github.com/relunctance/laravel-docs-reader/pulls)

---

## 許可證

MIT License
