# 本番サーバーアップロードチェックリスト

## 必須ファイル

本番サーバー (`/home/csac2023/dha-team.com/public_html/sdk-php-master/`) に以下のファイルをアップロードしてください：

### 🔥 最重要ファイル
- ✅ `autoload.php` - SDKのオートローダー
- ✅ `classmap.php` - クラスマップファイル  
- ✅ `config.php` - API設定ファイル
- ✅ `SampleCodeConstants.php` - 認証情報
- ✅ `authorize-credit-card.php` - 決済処理ファイル
- ✅ `google-sheets-complete.php` - Google Sheets統合

### 📁 libディレクトリ全体
```
lib/
├── net/
│   └── authorize/
│       ├── api/
│       │   ├── constants/
│       │   │   └── ANetEnvironment.php
│       │   ├── contract/
│       │   │   └── v1/ (全ファイル)
│       │   ├── controller/ (全ファイル)
│       │   └── yml/ (全ディレクトリ)
│       └── util/ (全ファイル)
└── ssl/
    └── cert.pem
```

### 🎨 スタイルファイル
- ✅ `stely-top.css`
- ✅ `stely.css`

### 📋 設定ファイル
- ✅ `config.php` (存在する場合)
- ✅ `composer.json` (存在する場合)

## アップロード手順

### 方法1: FTPクライアント使用
1. FileZilla等のFTPクライアントでサーバーに接続
2. ローカルの `sdk-php-master ver4` フォルダの内容を選択
3. サーバーの `/home/csac2023/dha-team.com/public_html/sdk-php-master/` にアップロード

### 方法2: cPanelファイルマネージャー使用
1. cPanelにログイン
2. ファイルマネージャーを開く
3. `public_html/sdk-php-master/` に移動
4. ファイルをアップロード

### 方法3: ZIP圧縮してアップロード
1. ローカルで `sdk-php-master ver4` フォルダをZIP圧縮
2. サーバーにZIPファイルをアップロード
3. サーバー上で解凍

## 確認方法

アップロード後、ブラウザで以下にアクセスして確認：

1. **基本確認**
   ```
   https://dha-team.com/sdk-php-master/authorize-credit-card.php
   ```
   - エラーメッセージではなく、決済フォームが表示されるか確認

2. **Google Sheets テスト**
   ```
   https://dha-team.com/sdk-php-master/google-sheets-complete.php?action=test
   ```
   - 設定テストが実行されるか確認

3. **デバッグテスト**
   ```
   https://dha-team.com/sdk-php-master/google-sheets-complete.php?action=debug
   ```
   - API通信テストが実行されるか確認

## よくある問題

### ❌ `autoload.php not found`
- **原因**: `autoload.php` がアップロードされていない
- **解決**: ファイルをアップロード

### ❌ `classmap.php not found`  
- **原因**: `classmap.php` がアップロードされていない
- **解決**: ファイルをアップロード

### ❌ `Class not found` エラー
- **原因**: `lib/` ディレクトリがアップロードされていない
- **解決**: `lib/` ディレクトリ全体をアップロード

### ❌ SSL/TLS エラー
- **原因**: `lib/ssl/cert.pem` がアップロードされていない
- **解決**: SSL証明書ファイルをアップロード

## ファイル権限

アップロード後、以下のファイルの権限を確認：

```bash
chmod 644 *.php
chmod 644 *.css
chmod -R 644 lib/
chmod 755 (ディレクトリ)
```

## セキュリティ

本番環境では以下に注意：

1. **不要ファイルの削除**
   - `README.md`
   - `doc/` ディレクトリ
   - `tests/` ディレクトリ
   - `upload-checklist.md` (このファイル)

2. **設定ファイルの保護**
   ```apache
   # .htaccess に追加
   <Files "SampleCodeConstants.php">
       Deny from all
   </Files>
   <Files "google-sheets-complete.php">
       <RequireAll>
           Require ip あなたのIPアドレス
       </RequireAll>
   </Files>
   ```

## 確認コマンド

SSH接続できる場合、以下で確認：

```bash
# ファイルの存在確認
ls -la /home/csac2023/dha-team.com/public_html/sdk-php-master/

# 権限確認
ls -la /home/csac2023/dha-team.com/public_html/sdk-php-master/*.php

# ディレクトリ構造確認
find /home/csac2023/dha-team.com/public_html/sdk-php-master/ -type d
```