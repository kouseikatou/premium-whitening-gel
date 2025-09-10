# 決済システム Google Sheets連携 セットアップガイド

## 目次
1. [事前準備](#1-事前準備)
2. [Googleスプレッドシート作成](#2-googleスプレッドシート作成)
3. [Google Apps Script設定](#3-google-apps-script設定)
4. [PHP設定更新](#4-php設定更新)
5. [Authorize.Net SANDBOX設定](#5-authorizenet-sandbox設定)
6. [テスト実行](#6-テスト実行)
7. [トラブルシューティング](#7-トラブルシューティング)

---

## 1. 事前準備

### 必要なもの
- ✅ Googleアカウント
- ✅ PHP 7.4以上のWebサーバー
- ✅ インターネット接続

### 所要時間
約30分

---

## 2. Googleスプレッドシート作成

### 2-1. スプレッドシート作成
1. https://sheets.google.com にアクセス
2. **「空白のスプレッドシート」** をクリック
3. タイトルを **「注文管理」** に変更

### 2-2. ヘッダー設定
**A1からX1まで** 以下を入力:

```
A1: 注文日時    B1: 取引ID      C1: メールアドレス  D1: 姓
E1: 名         F1: 会社名      G1: 国            H1: 都道府県
I1: 市区町村    J1: 住所       K1: 番地          L1: 郵便番号
M1: 電話番号    N1: 配送先姓    O1: 配送先名       P1: 配送先会社
Q1: 配送先国    R1: 配送先都道府県 S1: 配送先市区町村  T1: 配送先住所
U1: 配送先番地   V1: 配送先郵便番号 W1: 配送先電話番号  X1: 金額
```

### 2-3. シート名変更
1. 左下のシートタブを右クリック
2. **「名前を変更」** → **「Orders」** に変更

### 2-4. スプレッドシートID取得
URLから以下の部分をコピー:
```
https://docs.google.com/spreadsheets/d/【ここがスプレッドシートID】/edit
```

**✅ メモ**: スプレッドシートIDを控えておく

---

## 3. Google Apps Script設定

### 3-1. プロジェクト作成
1. https://script.google.com にアクセス
2. **「新しいプロジェクト」** をクリック
3. プロジェクト名を **「注文データ受信」** に変更

### 3-2. コード実装
以下のコードを全て選択して **Apps Scriptエディタに貼り付け**:

```javascript
function doPost(e) {
  try {
    const data = JSON.parse(e.postData.contents);
    console.log('受信データ:', data);
    
    const spreadsheetId = data.spreadsheetId;
    const sheetName = data.sheetName || 'Orders';
    
    const spreadsheet = SpreadsheetApp.openById(spreadsheetId);
    const sheet = spreadsheet.getSheetByName(sheetName);
    
    if (!sheet) {
      throw new Error(`シート "${sheetName}" が見つかりません`);
    }
    
    const orderRow = [
      data.timestamp || new Date().toISOString(),
      data.transactionId || '',
      data.email || '',
      data.lastName || '',
      data.firstName || '',
      data.company || '',
      data.country || '',
      data.state || '',
      data.city || '',
      data.address || '',
      data.apartment || '',
      data.zip || '',
      data.phone || '',
      data.shippingLastName || '',
      data.shippingFirstName || '',
      data.shippingCompany || '',
      data.shippingCountry || '',
      data.shippingState || '',
      data.shippingCity || '',
      data.shippingAddress || '',
      data.shippingApartment || '',
      data.shippingZip || '',
      data.shippingPhone || '',
      data.amount || ''
    ];
    
    sheet.appendRow(orderRow);
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'データが正常に追加されました',
        timestamp: new Date().toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    console.error('エラー:', error);
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        error: error.toString(),
        timestamp: new Date().toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({
      message: 'Google Apps Script Web Appが正常に動作しています',
      timestamp: new Date().toISOString()
    }))
    .setMimeType(ContentService.MimeType.JSON);
}
```

### 3-3. デプロイ
1. **「デプロイ」** → **「新しいデプロイ」**
2. 種類を選択で **「ウェブアプリ」** を選択
3. 設定:
   - 説明: `注文データ受信用`
   - 実行者: `自分`
   - アクセス: `全員`
4. **「デプロイ」** をクリック
5. 認証画面で **「承認」** → **「詳細設定」** → **「安全でないページに移動」**
6. **Web App URL** をコピーして保存

**✅ メモ**: Web App URLを控えておく

---

## 4. PHP設定更新

### 4-1. config.php編集
以下の2つの値を更新:

```php
// スプレッドシートID（ステップ2-4で取得）
define('GOOGLE_SPREADSHEET_ID', 'あなたのスプレッドシートID');

// Apps Script URL（ステップ3-3で取得）
define('GOOGLE_APPS_SCRIPT_URL', 'あなたのWeb App URL');
```

### 4-2. SANDBOX環境設定
```php
// SANDBOX環境に設定（既に設定済み）
define('AUTHORIZENET_ENVIRONMENT', 'SANDBOX');
```

---

## 5. Authorize.Net SANDBOX設定

### 5-1. SANDBOXアカウント作成
1. https://developer.authorize.net/ にアクセス
2. **「Get Sandbox Account」** でアカウント作成
3. ログイン後、以下を取得:
   - API Login ID
   - Transaction Key  
   - Public Client Key

### 5-2. 認証情報更新
config.phpで以下を更新:

```php
// あなたのSANDBOX認証情報に置き換え
define('AUTHORIZENET_LOGIN_ID', 'あなたのAPI Login ID');
define('AUTHORIZENET_TRANSACTION_KEY', 'あなたのTransaction Key');  
define('AUTHORIZENET_CLIENT_KEY', 'あなたのPublic Client Key');
```

---

## 6. テスト実行

### 6-1. 設定確認テスト
ブラウザで **setup-test.php** にアクセス:
```
http://あなたのサーバー/setup-test.php
```

以下をテスト:
- ✅ 設定値確認
- ✅ Apps Script動作テスト
- ✅ テストデータ送信

### 6-2. 決済テスト
1. **index.php** にアクセス
2. テスト用クレジットカードで決済:
   - カード番号: `4111111111111111`
   - 有効期限: `12/25`（将来の日付）
   - CVV: `123`
   - 名前: `Test User`
3. 決済成功後、スプレッドシートを確認

### 6-3. 確認事項
- ✅ 決済成功画面が表示される
- ✅ スプレッドシートにデータが追加される
- ✅ 全項目が正しく入力されている

---

## 7. トラブルシューティング

### よくある問題と解決方法

#### 🚨 Apps Script認証エラー
**症状**: 「このアプリは確認されていません」
**解決**: 詳細設定 → 安全でないページに移動

#### 🚨 スプレッドシートにデータが入らない
**原因**: URLまたはIDの設定ミス
**解決**: config.phpの設定値を再確認

#### 🚨 決済認証エラー
**症状**: "User authentication failed"
**解決**: Authorize.Net SANDBOX認証情報を確認

#### 🚨 決済は成功するがシート未反映
**原因**: PHPでのGoogle Sheets送信エラー
**解決**: 
1. authorize-credit-card.php:342行目のコメントアウト確認
2. setup-test.phpでテスト実行

### デバッグ方法
1. **PHPエラーログ確認**
2. **ブラウザ開発者ツールのコンソール確認**
3. **Apps Scriptの実行ログ確認**

---

## ✅ セットアップ完了チェックリスト

- [ ] Googleスプレッドシート作成・ヘッダー設定
- [ ] Google Apps Script作成・デプロイ
- [ ] config.php設定更新
- [ ] Authorize.Net SANDBOX認証情報設定
- [ ] setup-test.phpでテスト成功
- [ ] テスト決済でデータ連携確認

**🎉 すべて完了したら本番運用可能です！**

---

## サポート情報

### 参考ファイル
- `config.php` - 設定ファイル
- `setup-test.php` - テストページ
- `google-sheets-complete.php` - Google Sheets連携機能
- `authorize-credit-card.php` - 決済処理

### お困りの場合
1. setup-test.phpでステップごとに確認
2. エラーログを確認
3. 設定値を再確認