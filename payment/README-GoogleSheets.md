# Google Sheets 連携セットアップ手順（最新版）

決済成功後に自動でGoogleスプレッドシートに注文情報を記録する機能のセットアップ手順です。

## 重要な変更点

⚠️ **Google Sheets APIの制限により、APIキーだけでは書き込み操作ができません**
📝 **解決方法：Google Apps Script Web Appを使用して書き込み操作を代行します**

## 現在のファイル構成

- `google-sheets-complete.php` - 全機能統合ファイル（設定・機能・テスト・手順がすべて含まれています）
- `authorize-credit-card.php` - 決済処理ファイル（上記ファイルを読み込み済み）

## セットアップ手順

### ステップ1: Google Apps Script Web Appの作成

1. **Google Apps Script にアクセス**
   - https://script.google.com/ にアクセス
   - Googleアカウントでログイン

2. **新しいプロジェクトを作成**
   - 「新しいプロジェクト」をクリック
   - プロジェクト名を「Order Data Handler」に変更

3. **コードを貼り付け**
   - デフォルトのコードを削除し、以下をコピー＆ペースト：

```javascript
function doPost(e) {
  try {
    const data = JSON.parse(e.postData.contents);
    const spreadsheetId = data.spreadsheetId;
    const range = data.range;
    const values = data.values;
    
    const spreadsheet = SpreadsheetApp.openById(spreadsheetId);
    const sheet = spreadsheet.getSheetByName('Orders') || spreadsheet.getActiveSheet();
    
    for (let i = 0; i < values.length; i++) {
      sheet.appendRow(values[i]);
    }
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Data successfully added to spreadsheet',
        rowsAdded: values.length
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        error: error.toString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({
      message: 'Google Apps Script Web App is running',
      timestamp: new Date().toISOString()
    }))
    .setMimeType(ContentService.MimeType.JSON);
}
```

4. **Web Appとしてデプロイ**
   - 「デプロイ」→「新しいデプロイ」をクリック
   - 種類として「ウェブアプリ」を選択
   - 説明: 「Order Data Handler」
   - 実行者: 「自分」
   - アクセスできるユーザー: 「全員」
   - 「デプロイ」をクリック
   - **📋 Web App URLをコピー**（重要！）

### ステップ2: PHPの設定を更新

1. **google-sheets-complete.php を編集**
   - ファイルの上部にある以下の行を見つける：
   ```php
   define('GOOGLE_APPS_SCRIPT_URL', 'YOUR_APPS_SCRIPT_URL_HERE');
   ```
   
   - コピーしたWeb App URLに置き換える：
   ```php
   define('GOOGLE_APPS_SCRIPT_URL', 'https://script.google.com/macros/s/AKfycbxxxxxxxxxxxxxxxxxxxxx/exec');
   ```

### ステップ3: スプレッドシートの準備

1. **スプレッドシートを開く**
   - https://docs.google.com/spreadsheets/d/11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs/edit

2. **シートの名前を「Orders」に変更**
   - 左下のシートタブを右クリック
   - 「名前を変更」→「Orders」

3. **ヘッダー行を設定（推奨）**
   - A1セルから以下のヘッダーを入力：

| A1 | B1 | C1 | D1 | E1 | F1 | G1 | H1 |
|---|---|---|---|---|---|---|---|
| 注文日時 | 取引ID | メールアドレス | 名 | 姓 | 会社名 | 住所（区・町名） | 住所（丁目・番地・号） |

| I1 | J1 | K1 | L1 | M1 | N1 | O1 | P1 |
|---|---|---|---|---|---|---|---|
| 市区町村 | 都道府県 | 郵便番号 | 国 | 電話番号 | 配送先名 | 配送先姓 | 配送先会社名 |

| Q1 | R1 | S1 | T1 | U1 | V1 | W1 | X1 |
|---|---|---|---|---|---|---|---|
| 配送先住所（区・町名） | 配送先住所（丁目・番地・号） | 配送先市区町村 | 配送先都道府県 | 配送先郵便番号 | 配送先国 | 配送先電話番号 | 金額 |

### ステップ4: テスト

1. **Apps Scriptでテスト**
   - Apps Scriptエディタで以下の「testScript」関数を追加：
   
   ```javascript
   function testScript() {
     const testData = {
       spreadsheetId: '11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs',
       range: 'Orders!A:Z',
       values: [
         [
           new Date().toISOString(),
           'TEST-' + Date.now(),
           'test@example.com',
           'テスト',
           'ユーザー',
           'テスト会社',
           'テスト住所',
           'Apt 123',
           '東京',
           '東京都',
           '100-0001',
           '日本',
           '090-1234-5678',
           'テスト',
           '配送',
           '配送会社',
           '配送住所',
           'Apt 456',
           '大阪',
           '大阪府',
           '550-0001',
           '日本',
           '090-8765-4321',
           '100.00'
         ]
       ]
     };
     
     // doPost関数をシミュレート
     const mockEvent = {
       postData: {
         contents: JSON.stringify(testData)
       }
     };
     
     const result = doPost(mockEvent);
     console.log(result.getContent());
   }
   ```
   
   - 「testScript」関数を選択して「実行」ボタンをクリック
   - 初回実行時は権限許可が必要（「許可を確認」→「許可」をクリック）
   - 実行ログに成功メッセージが表示されることを確認
   - スプレッドシートにテストデータが追加されることを確認

2. **PHPでテスト**
   - ブラウザで `google-sheets-complete.php?action=test` にアクセス
   - 設定確認とテストデータ送信を実行

3. **デバッグテスト**
   - ブラウザで `google-sheets-complete.php?action=debug` にアクセス
   - 詳細なAPI通信ログを確認

## 現在の設定状況

- ✅ **スプレッドシートID**: `11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs`
- ✅ **API Key**: 設定済み（読み取り用）
- ⚠️ **Apps Script URL**: 要設定（書き込み用）

## 使用方法

決済完了後、以下のようにデータが自動記録されます：

```php
// authorize-credit-card.php 内で自動実行
$orderData = [
    'transactionId' => $tresponse->getTransId(),
    'email' => $_POST['mell-contact'] ?? '',
    'firstName' => $_POST['FirstName'] ?? '',
    // ... 他の注文データ
];

$result = sendOrderToGoogleSheets($orderData);
```

## トラブルシューティング

### よくある問題と解決方法

**1. Apps Script URLが未設定**
- エラー: "Google Apps Script URL not configured"
- 解決: ステップ1でWeb App URLを取得し、ステップ2で設定

**2. Apps Scriptでアクセス権限エラー**
- エラー: "Exception: You do not have permission to call SpreadsheetApp.openById"
- 解決: Apps Scriptプロジェクトで初回実行時に権限許可が必要

**3. スプレッドシートにデータが追加されない**
- 原因: シート名が「Orders」になっていない
- 解決: シート名を「Orders」に変更

**4. PHPからApps Scriptへのリクエストが失敗**
- 確認: `error_log` でレスポンス詳細を確認
- 解決: Web App URLが正しく設定されているか確認

### ログの確認方法

PHPエラーログで詳細情報を確認：
```bash
tail -f /var/log/php_errors.log
```

または、ブラウザでデバッグテストを実行：
```
google-sheets-complete.php?action=debug
```

## セキュリティ対策

1. **Apps Script Web App の制限**
   - 必要に応じて「アクセスできるユーザー」を制限
   - 定期的にWeb App URLを更新

2. **スプレッドシートの共有設定**
   - 不要な共有権限を削除
   - 定期的にアクセス権限を確認

3. **APIキーの保護**
   - ファイルへの直接アクセスを制限
   - 本番環境では環境変数の使用を検討

## 注意事項

- 📋 Apps Script Web App URLは秘密情報として扱ってください
- 🔄 本番環境では必ずHTTPSを使用してください
- 📊 スプレッドシートの行数制限（最大500万行）にご注意ください
- 🔄 定期的にバックアップを取ることを推奨します

## サポート

問題が発生した場合は、以下の情報を確認してください：
1. `google-sheets-complete.php?action=debug` の結果
2. PHPエラーログの内容
3. Apps Scriptの実行ログ
4. スプレッドシートの共有設定とシート名