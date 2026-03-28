# Laravel Docs Reader

**English** | [中文](README.zh-CN.md) | [繁體中文](README.zh-TW.md) | [日本語](README.ja.md) | [한국어](README.ko.md) | [Français](README.fr.md) | [Español](README.es.md) | [Deutsch](README.de.md) | [Italiano](README.it.md) | [Русский](README.ru.md) | [Português (Brasil)](README.pt-BR.md)

---

## 概要

**Laravel Docs Reader** は、Agent（と開発者）に正確でバージョン対応の Laravel 公式ドキュメントへの即時アクセスを提供する OpenClaw Agent スキルです。推測なし——Agent はコードを書く前に公式ドキュメントを調べます。

### 主な機能

- 🔍 **自然言語 CLI 検索** — `php laradoc.php search "how to create a middleware"` — まるでドキュメントに尋ねるように
- 📦 **ローカルドキュメントキャッシュ** — スキルにバンドルされ、オフラインでも即時アクセス
- 🔄 **自動バージョン検出** — プロジェクトの Laravel バージョン（10/11/12）を検出し適切なドキュメントを提供
- 📖 **完全なドキュメント範囲** — ルーティング、コントローラー、モデル、キュー、メール、認証、イベント、放送、テストなど
- 🏭 **コード生成** — PSR-12 準拠の Laravel コード骨架を生成
- 📊 **バージョン差分** — Laravel 10 / 11 / 12 の変更点をハイライト
- 📋 **PSR-12 クイックリファレンス** — 内蔵: `php laradoc.php psr`、`psr arrays`、`psr naming`
- 🤖 **自動更新 PR** — GitHub Actions が Laravel の新リリースを監視し、PR を自動作成
- 🔗 **Laravel Package Search 連携** — 検索結果마다 agent が `laravel-package-search` を提案

---

## CLI ツール — 14 コマンド

```bash
# ── コア検索 ──────────────────────────────────────────────────
php laradoc.php search "how to create a middleware"   # 自然言語検索
php laradoc.php search "how to send a notification"
# ← 検索結果後に Package Search 提案を表示

# ── バージョン ──────────────────────────────────────────────────
php laradoc.php version                   # 現在のプロジェクトを自動検出
php laradoc.php version /path/to/project  # 特定プロジェクト
php laradoc.php current                  # デフォルトバージョンを表示 (Laravel 12)

# ── 設定 & Facade ─────────────────────────────────────────────
php laradoc.php config database
php laradoc.php config cache
php laradoc.php facade Cache

# ── Artisan & バージョン差分 ─────────────────────────────────
php laradoc.php artisan make:controller
php laradoc.php diff auth              # Laravel 10 vs 11 vs 12

# ── コード生成 ────────────────────────────────────────────────
php laradoc.php generate controller UserController
php laradoc.php generate model         Post
php laradoc.php generate job          ProcessUpload

# ── Blade  директив ───────────────────────────────────────────
php laradoc.php lang "loop"
php laradoc.php lang "csrf"

# ── PSR-12 クイックリファレンス ────────────────────────────────
php laradoc.php psr                   # 完全 PSR-12 ルール表
php laradoc.php psr arrays           # 配列ルール
php laradoc.php psr naming           # 命名規則

# ── キャッシュ & 更新 ─────────────────────────────────────────
php laradoc.php cache                  # ローカルキャッシュ状態を表示
php laradoc.php update                 # GitHub から強制取得
php laradoc.php subscribe              # サブスクリプション / 自動更新状態を表示
```

---

## 自動バージョン検出

CLI はプロジェクトの Laravel バージョンを自動検出：

1. `composer.json` → `laravel/framework`
2. `artisan --version`
3. `vendor/laravel/framework/.../Application.php` → `VERSION`

プロジェクトが見つからない場合は **Laravel 12** がデフォルト。

---

## 自動更新（GitHub Actions PR）

スキルを最新に保つ仕組み：

- **毎週チェック**: GitHub Actions が毎週日曜 00:00 UTC に実行
- **新バージョン検出**: Packagist の `laravel/framework` 最新バージョンと比較
- **自動 PR**: 新バージョン検出時に SKILL.md、version-detection.md、version-diff.md を更新する PR を自動作成

このスキルを使用しているユーザーは自動生成された PR をレビューし、確認後にマージできます。

---

## ドキュメント範囲

| カテゴリ | トピック |
|----------|---------|
| ルーティング | .Basic routes, route groups, resource routes, named routes, middleware |
| コントローラー | CRUD, REST, API, single-action, 依存性注入 |
| モデル | Eloquent, 全12種関連, scopes, casts |
| マイグレーション | Schema builder, 外部キー, インデックス |
| バリデーション | Form requests, インライン, カスタムルール |
| 認証 | Breeze, Sanctum, Gates, Policies |
| キュー | Jobs, ディスパッチ, 失敗処理, Laravel Horizon |
| キャッシュ | Store API, Redis タグ, atomic locks |
| メール | Markdown, 添付, キュー mail |
| 通知 | マルチチャンネル, データベース通知 |
| テスト | Pest, factories, fakes, HTTP テスト |
| イベント | リスナー, 放送 |
| ストレージ | Local/S3, signed URLs, uploads |
| スケジューリング | Cron, 重複防止 |
| サービスコンテナ | バインディング, singletons |
| Facades | 30+ Facade メソッド署名 |
| 放送 | プライベート/パブリックチャンネル |

---

## クイックスタート

```bash
git clone https://github.com/relunctance/laravel-docs-reader.git
cd laravel-docs-reader
php scripts/laradoc.php search "how to create a middleware"
```

---

## ファイル構造

```
laravel-docs-reader/
├── SKILL.md                         # スキル仕様
├── README.md                         # 英語版
├── README.zh-CN.md                  # 中国語簡体字版
├── .github/workflows/
│   └── update-docs.yml              # 毎週自動更新 PR
├── .cache/                          # ローカルドキュメントキャッシュ
├── references/
│   ├── version-detection.md          # バージョン検出ロジック
│   ├── version-diff.md              # Laravel 10/11/12 差分表
│   ├── psr-12.md                   # PSR-12 クイックリファレンス
│   ├── api-index.md                # 完全 API インデックス
│   ├── artisan-commands.md          # Artisan コマンドリファレンス
│   ├── facades.md                  # Facade メソッド署名
│   ├── blade-directives.md         # 全 Blade  директив
│   ├── config-ref.md               # config/ リファレンス
│   └── examples/
│       ├── controller.md
│       ├── model.md
│       ├── migration.md
│       ├── middleware.md
│       ├── queue-job.md
│       ├── notification.md
│       └── testing.md
└── scripts/
    └── laradoc.php                # CLI ツール（14 コマンド）
```

---

## コントリビュート

コンテンツが古くなっている거나 문서가 부족しますか？
- 🐛 [Issue を開く](https://github.com/relunctance/laravel-docs-reader/issues)
- 🔧 [PR を送信](https://github.com/relunctance/laravel-docs-reader/issues)

---

## ライセンス

MIT License
