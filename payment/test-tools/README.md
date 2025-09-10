# テストツール統合ディレクトリ

このディレクトリには、決済システムとGoogleスプレッドシート連携のテストツールがまとめられています。

## 📁 ディレクトリ構成

```
test-tools/
├── index.php                    # メイン統合ポータル
├── sheets/                      # Googleスプレッドシート関連テスト
├── payment/                     # 決済システム関連テスト
├── misc/                        # その他のテストツール
└── README.md                    # このファイル
```

## 🚀 使用方法

### 1. メインポータルにアクセス
```
https://your-domain.com/ver1/test-tools/
```

### 2. 各テストスイートの利用
- **Googleスプレッドシートテスト**: `test-tools/sheets/`
- **決済システムテスト**: `test-tools/payment/`

## 📊 Googleスプレッドシート関連ツール (`sheets/`)

### メインツール
- **`sheets-test-suite.php`** - 統合テストスイート
  - 設定検証
  - 読み取りテスト
  - データ送信テスト（単発・バルク）
  - Apps Script連携テスト

### 補助ツール
- **`test-data-sender.php`** - データ送信ツール
- **`data-export-sample.php`** - データフォーマット例
- **`debug-apps-script.php`** - Apps Scriptデバッグ

### 機能
- プリセット/カスタムデータでのテスト
- バルクデータ送信
- リアルタイム設定状態確認
- スプレッドシートへの直接リンク

## 💳 決済システム関連ツール (`payment/`)

### メインツール
- **`payment-test-suite.php`** - 統合テストスイート
  - API認証テスト
  - 環境設定確認
  - クレジットカード番号検証
  - CLIENT_KEY管理

### 補助ツール
- **`check-card-types.php`** - カードタイプ確認
- **`debug-auth.php`** - 認証情報デバッグ
- **`debug-config.php`** - 設定確認
- **`validate-client-key.php`** - CLIENT_KEY検証

### 機能
- テストカード番号一覧（SANDBOX環境）
- Luhnアルゴリズムによるカード番号検証
- API認証状態の確認
- 環境別設定の表示

## 🔧 その他のツール (`misc/`)

### 開発・デバッグツール
- **`debug-form-data.php`** - フォームデータのデバッグ
- **`simple-test.php`** - シンプルテスト
- **`test-translation.php`** - 翻訳テスト
- **`test-sample-codes.sh`** - サンプルコードテスト用スクリプト

### ドキュメント
- **`upload-checklist.md`** - アップロード確認リスト
- **`tests/`** - 旧テストディレクトリ

## ⚙️ 設定要件

### 必須設定（config.php）
```php
// Authorize.Net設定
define('AUTHORIZENET_ENVIRONMENT', 'PRODUCTION'); // または 'SANDBOX'
define('AUTHORIZENET_LOGIN_ID', 'your-login-id');
define('AUTHORIZENET_TRANSACTION_KEY', 'your-transaction-key');
define('AUTHORIZENET_CLIENT_KEY', 'your-80-char-client-key');

// Google Sheets設定
define('GOOGLE_SHEETS_API_KEY', 'your-api-key');
define('GOOGLE_SPREADSHEET_ID', 'your-spreadsheet-id');
define('GOOGLE_APPS_SCRIPT_URL', 'your-apps-script-url');
define('GOOGLE_SHEETS_SHEET_NAME', 'Orders');
```

### Googleスプレッドシート準備
1. 新しいスプレッドシートを作成
2. シート名を「Orders」に変更
3. A1行にヘッダーを設定：
   ```
   注文日時,取引ID,メールアドレス,名,姓,会社名,住所（区・町名）,住所（丁目・番地・号）,市区町村,都道府県,郵便番号,国,電話番号,配送先名,配送先姓,配送先会社名,配送先住所（区・町名）,配送先住所（丁目・番地・号）,配送先市区町村,配送先都道府県,配送先郵便番号,配送先国,配送先電話番号,金額
   ```

### Apps Script設定
1. Google Apps Scriptで新規プロジェクト作成
2. `google-sheets-complete.php`のコードを参考にスクリプト作成
3. ウェブアプリとしてデプロイ
4. 生成されたURLを`GOOGLE_APPS_SCRIPT_URL`に設定

## 🚨 セキュリティ注意事項

### 本番環境
- **テストツールは本番環境からアクセス制限を設定**
- `.htaccess`でのアクセス制限を推奨
- 本番環境ではテストカードは使用不可

### テスト環境
- SANDBOX環境でのみテストカード使用可能
- 実際のクレジットカード情報は入力しない

## 📋 テスト手順

### 1. 初回セットアップ
1. config.phpで各種APIキーを設定
2. Googleスプレッドシートを準備
3. Apps Scriptをデプロイ
4. テストポータルで設定状態を確認

### 2. 段階的テスト
1. **設定確認**: 各テストスイートで設定状態をチェック
2. **認証テスト**: API接続と認証をテスト
3. **データテスト**: 小規模なデータ送信をテスト
4. **統合テスト**: 決済フォームから実際のフローをテスト

### 3. 本番移行
1. すべてのテストが正常に完了
2. 本番環境設定に切り替え
3. 本番環境での最終確認
4. 運用開始

## 🔗 関連ドキュメント

- [Authorize.Net API Documentation](https://developer.authorize.net/)
- [Google Sheets API Documentation](https://developers.google.com/sheets/api)
- [Google Apps Script Documentation](https://developers.google.com/apps-script)

## 📞 サポート

テストツールに関する問題や質問がある場合は、各ファイル内のコメントを参照するか、設定内容を確認してください。