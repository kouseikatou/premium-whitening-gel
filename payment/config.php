<?php
/**
 * API設定ファイル
 * すべてのAPIキーと外部サービスの認証情報をここに保存します
 * 
 * セキュリティに関する注意: 
 * - 実際のAPIキーをバージョン管理にコミットしないでください
 * - 本番環境では環境変数または別の設定ファイルを使用してください
 * - このファイルがWebブラウザからアクセスできないようにしてください（.htaccessによる保護を推奨）
 */

// Google Maps API設定
define('GOOGLE_MAPS_API_KEY', 'AIzaSyDoxFQVdiDhpDw83T93Lo0ifcXCfkcbmZI');

// Authorize.Net設定
// セキュリティのため、本番環境では環境変数を使用してください
// 実際の認証情報をバージョン管理にコミットしないでください

// Authorize.Net環境設定
// 本番決済の場合は'PRODUCTION'、テストの場合は'SANDBOX'に設定
define('AUTHORIZENET_ENVIRONMENT', getenv('AUTHORIZENET_ENVIRONMENT') ?: 'PRODUCTION');

// Authorize.Net API認証情報
// オプション1: 環境変数を使用（本番環境で推奨）
if (getenv('AUTHORIZENET_LOGIN_ID') && getenv('AUTHORIZENET_TRANSACTION_KEY')) {
    define('AUTHORIZENET_LOGIN_ID', getenv('AUTHORIZENET_LOGIN_ID'));
    define('AUTHORIZENET_TRANSACTION_KEY', getenv('AUTHORIZENET_TRANSACTION_KEY'));
    define('AUTHORIZENET_CLIENT_KEY', getenv('AUTHORIZENET_CLIENT_KEY') ?: 'YOUR_CLIENT_KEY');
} else {
    // オプション2: 直接定義（開発/テスト環境のみ）
    // 警告: 実際の認証情報に置き換えて、バージョン管理にコミットしないでください
    if (AUTHORIZENET_ENVIRONMENT === 'PRODUCTION') {
        define('AUTHORIZENET_LOGIN_ID', '6T29MysXvMQW');  // 本番環境用ログインID
        define('AUTHORIZENET_TRANSACTION_KEY', '2827w9k37ea9RQNr');  // 本番環境用トランザクションキー
        define('AUTHORIZENET_CLIENT_KEY', '979TavNpCxM844zhyjcC6GbR4hj83BF3SEmsq8K423EqsJyEWfgSGDaJ8umHXw2D');  // 本番環境用公開クライアントキー
    } else {
        // サンドボックス認証情報（Authorize.Net公式テスト用認証情報）
        define('AUTHORIZENET_LOGIN_ID', '5KP3u95bQpv');  // サンドボックス用ログインID
        define('AUTHORIZENET_TRANSACTION_KEY', '346HZ32z3fP4hTG2');  // サンドボックス用トランザクションキー
        define('AUTHORIZENET_CLIENT_KEY', '5FcB6WrfHGS76gHW3v7btBCE3HuuBuke9Pj96Ztfn5R32G5ep42vne7MCWesRsgv');  // サンドボックス用公開クライアントキー
    }
}

// Google Sheets API設定
// GoogleスプレッドシートとApps Script連携用の設定
define('GOOGLE_SHEETS_API_KEY', 'AIzaSyDbYCgutYbwoKeXgdz5kojMD_prNEk45iw');

// スプレッドシートIDと範囲
// スプレッドシートIDはGoogleスプレッドシートのURLから取得できます
define('GOOGLE_SPREADSHEET_ID', '165xAUyMl1wVoMwJBd941Uio1qZAjQl8555-PSlRHG3E');
define('GOOGLE_SHEETS_RANGE', 'Orders!A:Z');
define('GOOGLE_SHEETS_SHEET_NAME', 'Orders'); // 転記先シート名（変更可能）

// Google Apps Script Web App URL（重要：セットアップ後に設定してください）
// 統合後のWeb App URLを使用
define('GOOGLE_APPS_SCRIPT_URL', 'https://script.google.com/macros/s/AKfycbxVwzD7PFxeXdN87OsvCLDT4Q4TM68oWcwc1ToWXPzGnfzb4uFeniNK913ajuGYBTKFuQ/exec');

// ライブラリ情報（参考用）
define('GOOGLE_APPS_SCRIPT_LIBRARY_ID', '1n9ILIDRppTaH1i0B7wora2bFmZnZAwPovHRfxS1sk3h-BMH3PSJpdsyd');
define('GOOGLE_APPS_SCRIPT_LIBRARY_VERSION', '11');

// サービスアカウント使用時の設定（上級者向け）
define('GOOGLE_SERVICE_ACCOUNT_FILE', 'path/to/your/service-account.json');

// 環境設定
define('ENVIRONMENT', 'development'); // 'development'（開発）, 'staging'（ステージング）, 'production'（本番）
define('DEBUG_MODE', true);

// アプリケーション設定
define('CURRENCY', 'USD');
define('DEFAULT_LANGUAGE', 'en');
define('SUPPORTED_LANGUAGES', ['en', 'ja']);

// メール設定
// define('SMTP_HOST', 'smtp.gmail.com');
// define('SMTP_PORT', 587);
// define('SMTP_USERNAME', 'your-email@gmail.com');
// define('SMTP_PASSWORD', 'your-app-password');

// セキュリティ設定
define('SESSION_TIMEOUT', 3600); // 1時間（秒単位）
define('MAX_LOGIN_ATTEMPTS', 5);

// ログ設定
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_FILE', 'logs/application.log');

/**
 * APIキーの設定手順:
 * 
 * 1. Google Maps APIキー:
 *    - https://console.cloud.google.com/ にアクセス
 *    - 新しいプロジェクトを作成または既存のプロジェクトを選択
 *    - 「Geocoding API」を有効化
 *    - 認証情報（APIキー）を作成
 *    - セキュリティのためキーをドメインに制限
 *    - 'YOUR_GOOGLE_MAPS_API_KEY'を実際のキーに置き換え
 * 
 * 2. Authorize.Net:
 *    - SampleCodeConstants.phpで既に設定済み
 *    - 本番環境では、本番用の認証情報を使用
 * 
 * 3. Google Sheets API:
 *    - 上記のGOOGLE_SHEETS_API_KEYを実際のキーに置き換え
 *    - GOOGLE_SPREADSHEET_IDを実際のスプレッドシートIDに置き換え
 *    - GOOGLE_APPS_SCRIPT_URLを作成したApps ScriptのURLに置き換え
 *    - GOOGLE_SHEETS_SHEET_NAMEで転記先シート名を指定（デフォルト: 'Orders'）
 * 
 * 4. 環境変数（本番環境で推奨）:
 *    - ハードコードされた値の代わりに$_ENV['GOOGLE_MAPS_API_KEY']を使用
 *    - サーバー設定で環境変数を設定
 */

// 利用可能な場合は環境変数を読み込み
if (function_exists('getenv')) {
    if (getenv('GOOGLE_MAPS_API_KEY')) {
        define('GOOGLE_MAPS_API_KEY_ENV', getenv('GOOGLE_MAPS_API_KEY'));
    }
}
?>