<?php
/**
 * Apps Script 修正ガイド
 * 
 * 401 Unauthorized エラーの解決方法
 */

require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>🔧 Apps Script 修正ガイド</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { background: #e74c3c; color: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 2.5em; }
        .section { background: #fff; margin: 20px 0; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-top: 0; }
        .error-box { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .success-box { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info-box { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning-box { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .step { background: #f8f9fa; border-left: 4px solid #3498db; padding: 15px; margin: 15px 0; }
        .step h3 { margin: 0 0 10px 0; color: #2c3e50; }
        .code-block { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; overflow-x: auto; }
        .code-block pre { margin: 0; white-space: pre-wrap; }
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #229954; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { color: #3498db; text-decoration: none; margin: 0 15px; padding: 8px 16px; background: #ecf0f1; border-radius: 5px; }
        .config-display { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #dee2e6; }
        .highlight { background: #ffeb3b; padding: 2px 4px; border-radius: 3px; }
        ol.large-steps { counter-reset: step-counter; list-style: none; padding: 0; }
        ol.large-steps li { counter-increment: step-counter; margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #3498db; }
        ol.large-steps li::before { content: "ステップ " counter(step-counter); display: block; font-weight: bold; font-size: 1.2em; color: #3498db; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Apps Script 修正ガイド</h1>
            <p>401 Unauthorized エラーの解決方法</p>
        </div>

        <div class="nav-links">
            <a href="debug-sheets-connection.php">🔍 デバッグツール</a>
            <a href="sheets-test-suite.php">🧪 テストスイート</a>
            <a href="index.php">📊 Sheetsポータル</a>
        </div>

        <div class="error-box">
            <strong>🚨 発生している問題:</strong><br>
            <strong>HTTP 401 Unauthorized</strong> - Apps Script へのアクセスが拒否されています。<br>
            これは Apps Script の権限設定に問題があることを示しています。
        </div>

        <div class="section">
            <h2>🔍 問題の原因</h2>
            <p>401 エラーは以下のいずれかが原因です：</p>
            <ul>
                <li>Apps Script が正しくデプロイされていない</li>
                <li>デプロイ時の権限設定が間違っている</li>
                <li>Apps Script のコードにエラーがある</li>
                <li>スプレッドシートへのアクセス権限がない</li>
            </ul>
        </div>

        <div class="section">
            <h2>🛠️ 修正手順</h2>
            
            <ol class="large-steps">
                <li>
                    <strong>Google Apps Script にアクセス</strong>
                    <p>以下のURLにアクセスしてください：</p>
                    <a href="https://script.google.com/" target="_blank" class="btn btn-success">Google Apps Script を開く</a>
                    <p>現在のプロジェクトまたは新しいプロジェクトを作成してください。</p>
                </li>

                <li>
                    <strong>正しいコードを貼り付け</strong>
                    <p>以下のコードをコピーして、Apps Script エディタに貼り付けてください：</p>
                    <div class="code-block">
                        <pre>function doPost(e) {
  try {
    console.log('POST request received');
    console.log('Request data:', e.postData.contents);
    
    const data = JSON.parse(e.postData.contents);
    const spreadsheetId = data.spreadsheetId;
    const sheetName = data.sheetName || 'Orders';
    
    console.log('Spreadsheet ID:', spreadsheetId);
    console.log('Sheet name:', sheetName);
    
    // スプレッドシートを開く
    const spreadsheet = SpreadsheetApp.openById(spreadsheetId);
    const sheet = spreadsheet.getSheetByName(sheetName) || spreadsheet.getActiveSheet();
    
    // データを行として準備
    const rowData = [
      data.timestamp || new Date().toISOString(),
      data.transactionId || '',
      data.email || '',
      data.firstName || '',
      data.lastName || '',
      data.company || '',
      data.address || '',
      data.apartment || '',
      data.city || '',
      data.state || '',
      data.zip || '',
      data.country || '',
      data.phone || '',
      data.shippingFirstName || '',
      data.shippingLastName || '',
      data.shippingCompany || '',
      data.shippingAddress || '',
      data.shippingApartment || '',
      data.shippingCity || '',
      data.shippingState || '',
      data.shippingZip || '',
      data.shippingCountry || '',
      data.shippingPhone || '',
      data.amount || '',
      data.isDifferentAddress || false
    ];
    
    // 行を追加
    sheet.appendRow(rowData);
    
    console.log('Data successfully added to spreadsheet');
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Data successfully added to spreadsheet',
        timestamp: new Date().toISOString(),
        rowsAdded: 1
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    console.error('Error in doPost:', error);
    
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
      message: 'Google Apps Script Web App is running',
      timestamp: new Date().toISOString(),
      version: '1.0'
    }))
    .setMimeType(ContentService.MimeType.JSON);
}</pre>
                    </div>
                </li>

                <li>
                    <strong>デプロイ設定の確認</strong>
                    <p>「デプロイ」→「新しいデプロイ」または「デプロイを管理」をクリックして、以下の設定を確認してください：</p>
                    <div class="warning-box">
                        <strong>重要な設定:</strong>
                        <ul>
                            <li><strong>種類:</strong> ウェブアプリ</li>
                            <li><strong>実行者:</strong> <span class="highlight">自分</span></li>
                            <li><strong>アクセス:</strong> <span class="highlight">全員</span></li>
                        </ul>
                    </div>
                </li>

                <li>
                    <strong>権限の承認</strong>
                    <p>初回デプロイ時に権限の承認が求められます：</p>
                    <ol>
                        <li>「権限を確認」をクリック</li>
                        <li>Googleアカウントを選択</li>
                        <li>「詳細」→「（プロジェクト名）に移動」をクリック</li>
                        <li>「許可」をクリック</li>
                    </ol>
                </li>

                <li>
                    <strong>新しいURLの取得</strong>
                    <p>デプロイ完了後、新しいWeb App URLが表示されます。このURLをコピーしてください。</p>
                    <div class="info-box">
                        <strong>現在のURL:</strong><br>
                        <code><?php echo GOOGLE_APPS_SCRIPT_URL; ?></code>
                    </div>
                </li>

                <li>
                    <strong>config.php の更新</strong>
                    <p>新しいURLを config.php に設定してください：</p>
                    <div class="code-block">
                        <pre>define('GOOGLE_APPS_SCRIPT_URL', '新しいURL');</pre>
                    </div>
                </li>
            </ol>
        </div>

        <div class="section">
            <h2>🧪 テスト方法</h2>
            
            <div class="step">
                <h3>1. GET リクエストテスト</h3>
                <p>まず、ブラウザで直接 Apps Script URL にアクセスしてください：</p>
                <a href="<?php echo GOOGLE_APPS_SCRIPT_URL; ?>" target="_blank" class="btn btn-info">Apps Script をブラウザで開く</a>
                <p>正常に動作している場合、以下のような JSON レスポンスが表示されます：</p>
                <div class="code-block">
                    <pre>{
  "message": "Google Apps Script Web App is running",
  "timestamp": "2025-07-09T21:34:29.000Z",
  "version": "1.0"
}</pre>
                </div>
            </div>

            <div class="step">
                <h3>2. POST リクエストテスト</h3>
                <p>GET テストが成功したら、デバッグツールで POST テストを実行してください：</p>
                <a href="debug-sheets-connection.php" class="btn btn-warning">デバッグツールでテスト</a>
            </div>
        </div>

        <div class="section">
            <h2>📋 スプレッドシートの確認</h2>
            
            <div class="config-display">
                <strong>現在のスプレッドシート:</strong><br>
                ID: <code><?php echo GOOGLE_SPREADSHEET_ID; ?></code><br>
                シート名: <code><?php echo defined('GOOGLE_SHEETS_SHEET_NAME') ? GOOGLE_SHEETS_SHEET_NAME : 'Orders'; ?></code>
            </div>
            
            <p>スプレッドシートも確認してください：</p>
            <ol>
                <li>スプレッドシートが存在するか</li>
                <li>「Orders」シートが存在するか</li>
                <li>Apps Script を実行するアカウントがスプレッドシートにアクセス権限を持っているか</li>
            </ol>
            
            <a href="https://docs.google.com/spreadsheets/d/<?php echo GOOGLE_SPREADSHEET_ID; ?>/edit" target="_blank" class="btn btn-success">スプレッドシートを開く</a>
        </div>

        <div class="section">
            <h2>❓ まだ解決しない場合</h2>
            
            <div class="warning-box">
                <strong>以下を確認してください:</strong>
                <ul>
                    <li>Google Apps Script と Google Sheets が同じアカウントで使用されているか</li>
                    <li>スプレッドシートが削除されていないか</li>
                    <li>Apps Script プロジェクトが削除されていないか</li>
                    <li>Googleアカウントの権限に問題がないか</li>
                </ul>
            </div>
            
            <p>それでも解決しない場合は、新しい Apps Script プロジェクトを作成することをお勧めします。</p>
        </div>
    </div>
</body>
</html>