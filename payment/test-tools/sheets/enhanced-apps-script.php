<?php
/**
 * 強化されたApps Script コード
 * 
 * 自動発火機能を組み込んだApps Scriptのコード例
 */

require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>🚀 強化されたApps Script</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #2c3e50; color: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 2.5em; }
        .section { background: #fff; margin: 20px 0; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-top: 0; }
        .success-box { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info-box { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning-box { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .code-block { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; overflow-x: auto; }
        .code-block pre { margin: 0; white-space: pre-wrap; font-size: 13px; line-height: 1.4; }
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #229954; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { color: #3498db; text-decoration: none; margin: 0 15px; padding: 8px 16px; background: #ecf0f1; border-radius: 5px; }
        .highlight { background: #ffeb3b; padding: 2px 4px; border-radius: 3px; }
        .method-card { border: 1px solid #dee2e6; border-radius: 8px; margin: 15px 0; overflow: hidden; }
        .method-header { background: #f8f9fa; padding: 15px; border-bottom: 1px solid #dee2e6; }
        .method-content { padding: 15px; }
        .pros-cons { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 15px 0; }
        .pros, .cons { padding: 10px; border-radius: 5px; }
        .pros { background: #d4edda; border: 1px solid #c3e6cb; }
        .cons { background: #f8d7da; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 強化されたApps Script</h1>
            <p>自動発火機能を組み込んだApps Scriptのコード</p>
        </div>

        <div class="nav-links">
            <a href="quick-test.php">⚡ クイックテスト</a>
            <a href="fix-apps-script.php">🔧 修正ガイド</a>
            <a href="index.php">📊 Sheetsポータル</a>
        </div>

        <div class="section">
            <h2>🎯 問題の説明</h2>
            <div class="warning-box">
                <strong>なぜ onEdit が動作しないか:</strong>
                <ul>
                    <li><code>onEdit</code>トリガーは<strong>手動編集</strong>でのみ発火</li>
                    <li>Apps ScriptやAPIからのデータ追加では発火しない</li>
                    <li>PHPからのデータ送信は「プログラム的な追加」のため対象外</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2>💡 解決方法</h2>
            
            <div class="method-card">
                <div class="method-header">
                    <h3>🏆 方法1: doPost内で直接処理 (推奨)</h3>
                    <p>最も確実で遅延がない方法</p>
                </div>
                <div class="method-content">
                    <div class="pros-cons">
                        <div class="pros">
                            <strong>✅ メリット:</strong>
                            <ul>
                                <li>即座に処理される</li>
                                <li>確実に実行される</li>
                                <li>設定が簡単</li>
                            </ul>
                        </div>
                        <div class="cons">
                            <strong>❌ デメリット:</strong>
                            <ul>
                                <li>コードが少し複雑になる</li>
                                <li>処理時間が長くなる可能性</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="method-card">
                <div class="method-header">
                    <h3>⏰ 方法2: 時間ベースのトリガー</h3>
                    <p>定期的に新しいデータをチェック</p>
                </div>
                <div class="method-content">
                    <div class="pros-cons">
                        <div class="pros">
                            <strong>✅ メリット:</strong>
                            <ul>
                                <li>バッチ処理で効率的</li>
                                <li>エラー時の再試行可能</li>
                                <li>負荷分散</li>
                            </ul>
                        </div>
                        <div class="cons">
                            <strong>❌ デメリット:</strong>
                            <ul>
                                <li>処理に遅延が発生</li>
                                <li>重複処理の可能性</li>
                                <li>複雑な状態管理</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>🔧 実装コード</h2>
            
            <h3>方法1: doPost内で直接処理 (推奨)</h3>
            <div class="info-box">
                <strong>このコードを Apps Script エディタにコピーしてください:</strong>
            </div>
            
            <div class="code-block">
                <pre>function doPost(e) {
  try {
    console.log('POST request received');
    console.log('Request data:', e.postData.contents);
    
    const data = JSON.parse(e.postData.contents);
    const spreadsheetId = data.spreadsheetId;
    const sheetName = data.sheetName || 'Orders';
    
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
    
    // 追加された行番号を取得
    const newRowNumber = sheet.getLastRow();
    
    // 🚀 ここで自動処理を実行
    processNewOrder(spreadsheet, sheet, newRowNumber, rowData);
    
    console.log('Data successfully added and processed');
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Data successfully added and processed',
        timestamp: new Date().toISOString(),
        rowNumber: newRowNumber
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

// 新しい注文の処理（元のonEdit関数の処理を統合）
function processNewOrder(spreadsheet, sourceSheet, row, rowData) {
  try {
    console.log('Processing new order at row:', row);
    
    // 発送用CSVシートへの転記
    copyToShippingSheet(spreadsheet, sourceSheet, row);
    
    // 月次管理シートへの転記
    copyToMonthlySheet(spreadsheet, sourceSheet, row);
    
    console.log('Order processing completed');
    
  } catch (error) {
    console.error('Error in processNewOrder:', error);
    // エラーが発生してもメインの処理は続行
  }
}

function copyToShippingSheet(spreadsheet, sourceSheet, row) {
  try {
    let shippingSheet = spreadsheet.getSheetByName("発送用CSV");
    
    // 発送用CSVシートが存在しない場合は作成
    if (!shippingSheet) {
      shippingSheet = createShippingSheet(spreadsheet);
    }
    
    // 最終行を取得
    const lastRow = shippingSheet.getLastRow() + 1;
    
    // データマッピング
    const data = sourceSheet.getRange(row, 1, 1, 29).getValues()[0];
    
    const shippingData = [
      data[2] || '',  // Email (C列)
      (data[13] || '') + " " + (data[14] || ''),  // Name (N列 + O列)
      data[22] || '',  // phone (W列)
      data[19] || '',  // Address (T列)
      data[20] || '',  // Address Line 2 (U列)
      data[18] || '',  // City (S列)
      data[17] || '',  // State (R列)
      data[21] || '',  // Zipcode (V列)
      data[16] || '',  // Country (Q列)
      data[1] || '',   // Order ID (B列)
      data[24] || '',  // Order Items (Y列)
      data[25] || '',  // Pounds (Z列)
      data[26] || '',  // Length (AA列)
      data[27] || '',  // Width (AB列)
      data[28] || ''   // Height (AC列)
    ];
    
    // データを発送用CSVシートに書き込み
    shippingSheet.getRange(lastRow, 1, 1, 15).setValues([shippingData]);
    
    console.log('Data copied to shipping sheet');
    
  } catch (error) {
    console.error('Error in copyToShippingSheet:', error);
  }
}

function copyToMonthlySheet(spreadsheet, sourceSheet, row) {
  try {
    // 注文日時から年月を取得
    const orderDate = sourceSheet.getRange(row, 1).getValue();
    const year = orderDate.getFullYear();
    const month = orderDate.getMonth() + 1;
    const sheetName = year + "年" + month + "月";
    
    // 月次シートが存在しない場合は作成
    let monthlySheet = spreadsheet.getSheetByName(sheetName);
    if (!monthlySheet) {
      monthlySheet = createMonthlySheet(spreadsheet, sheetName);
    }
    
    // 最終行を取得
    const lastRow = monthlySheet.getLastRow() + 1;
    
    // Orders シートのデータをコピー
    const data = sourceSheet.getRange(row, 1, 1, 29).getValues();
    
    // 月次シートに書き込み
    monthlySheet.getRange(lastRow, 1, 1, 29).setValues(data);
    
    // ステータス列に"新規"を設定
    monthlySheet.getRange(lastRow, 30).setValue("新規");
    
    console.log('Data copied to monthly sheet:', sheetName);
    
  } catch (error) {
    console.error('Error in copyToMonthlySheet:', error);
  }
}

function createShippingSheet(spreadsheet) {
  const newSheet = spreadsheet.insertSheet("発送用CSV");
  
  // ヘッダーを設定
  const headers = [
    "Email", "Name", "Phone", "Address", "Address Line 2",
    "City", "State", "Zipcode", "Country", "Order ID",
    "Order Items", "Pounds", "Length", "Width", "Height"
  ];
  
  newSheet.getRange(1, 1, 1, headers.length).setValues([headers]);
  
  // ヘッダー行の書式設定
  newSheet.getRange(1, 1, 1, headers.length)
    .setBackground("#4285F4")
    .setFontColor("#FFFFFF")
    .setFontWeight("bold");
    
  return newSheet;
}

function createMonthlySheet(spreadsheet, sheetName) {
  const newSheet = spreadsheet.insertSheet(sheetName);
  
  // ヘッダーを設定
  const headers = [
    "注文日時", "取引ID", "メールアドレス", "お客様名（姓）", "お客様名（名）",
    "会社名", "国", "都道府県", "市区町村", "住所（区・町名）",
    "住所（番地・建物）", "郵便番号", "電話番号", "配送先名（姓）", "配送先名（名）",
    "配送先会社名", "配送先国", "配送先都道府県", "配送先市区町村", "配送先住所（区・町名）",
    "配送先住所（番地・建物）", "配送先郵便番号", "配送先電話番号", "決済金額（USD）", "Order Items",
    "Pounds", "Length", "Width", "Height", "処理ステータス",
    "発送日", "追跡番号", "備考"
  ];
  
  newSheet.getRange(1, 1, 1, headers.length).setValues([headers]);
  
  // ヘッダー行の書式設定
  newSheet.getRange(1, 1, 1, headers.length)
    .setBackground("#4285F4")
    .setFontColor("#FFFFFF")
    .setFontWeight("bold");
  
  return newSheet;
}

// CSVエクスポート関数
function exportShippingCSV() {
  try {
    const spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = spreadsheet.getSheetByName("発送用CSV");
    
    if (!sheet) {
      throw new Error("発送用CSVシートが見つかりません");
    }
    
    const data = sheet.getDataRange().getValues();
    
    // CSVフォーマットに変換
    let csv = "";
    for (let i = 0; i < data.length; i++) {
      csv += data[i].join(",") + "\n";
    }
    
    // ファイルを作成
    const blob = Utilities.newBlob(csv, "text/csv", "shipping_" + new Date().toISOString().slice(0, 10) + ".csv");
    
    // Googleドライブに保存
    DriveApp.createFile(blob);
    
    console.log("CSV exported successfully");
    
    // メールで送信する場合（オプション）
    // MailApp.sendEmail("shipping@example.com", "発送データ", "本日の発送データを添付します。", {attachments: [blob]});
    
  } catch (error) {
    console.error("Error in exportShippingCSV:", error);
  }
}

function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({
      message: 'Google Apps Script Web App is running',
      timestamp: new Date().toISOString(),
      version: '2.0 - Enhanced with auto-processing'
    }))
    .setMimeType(ContentService.MimeType.JSON);
}</pre>
            </div>
        </div>

        <div class="section">
            <h2>🔄 方法2: 時間ベースのトリガー (オプション)</h2>
            
            <div class="info-box">
                <strong>定期的に新しいデータをチェックする場合:</strong>
            </div>
            
            <div class="code-block">
                <pre>// 時間ベースのトリガーを設定（初回のみ実行）
function setupTimeTrigger() {
  // 既存のトリガーを削除
  const triggers = ScriptApp.getProjectTriggers();
  triggers.forEach(trigger => {
    if (trigger.getHandlerFunction() === 'processNewOrders') {
      ScriptApp.deleteTrigger(trigger);
    }
  });
  
  // 新しいトリガーを作成（5分ごと）
  ScriptApp.newTrigger('processNewOrders')
    .timeBased()
    .everyMinutes(5)
    .create();
}

// 新しい注文を定期的に処理
function processNewOrders() {
  try {
    const spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
    const ordersSheet = spreadsheet.getSheetByName("Orders");
    
    if (!ordersSheet) return;
    
    // 最後に処理した行を取得（PropertiesServiceを使用）
    const properties = PropertiesService.getScriptProperties();
    const lastProcessedRow = parseInt(properties.getProperty('lastProcessedRow') || '1');
    
    const lastRow = ordersSheet.getLastRow();
    
    // 新しい行があるかチェック
    if (lastRow > lastProcessedRow) {
      for (let row = lastProcessedRow + 1; row <= lastRow; row++) {
        // データが存在するかチェック
        const orderDate = ordersSheet.getRange(row, 1).getValue();
        if (orderDate) {
          processNewOrder(spreadsheet, ordersSheet, row, null);
        }
      }
      
      // 最後に処理した行を更新
      properties.setProperty('lastProcessedRow', lastRow.toString());
    }
    
  } catch (error) {
    console.error('Error in processNewOrders:', error);
  }
}</pre>
            </div>
        </div>

        <div class="section">
            <h2>🚀 導入手順</h2>
            
            <ol style="font-size: 1.1em; line-height: 1.6;">
                <li><strong>Apps Script エディタを開く</strong>
                    <br><a href="https://script.google.com/" target="_blank" class="btn btn-success">Google Apps Script を開く</a>
                </li>
                
                <li><strong>上記のコードをコピー＆ペースト</strong>
                    <br>既存のコードを置き換えてください
                </li>
                
                <li><strong>保存してデプロイ</strong>
                    <br>「デプロイ」→「デプロイを管理」→「新しいバージョン」
                </li>
                
                <li><strong>config.php の更新</strong>
                    <br>新しいURLが生成された場合は更新してください
                </li>
                
                <li><strong>テスト実行</strong>
                    <br><a href="quick-test.php" class="btn btn-warning">クイックテストで確認</a>
                </li>
            </ol>
        </div>

        <div class="section">
            <h2>💡 重要なポイント</h2>
            
            <div class="success-box">
                <h3>✅ 方法1 (推奨) の利点:</h3>
                <ul>
                    <li>データ追加と同時に処理が実行される</li>
                    <li>確実に処理される</li>
                    <li>追加設定が不要</li>
                    <li>エラーハンドリングが簡単</li>
                </ul>
            </div>
            
            <div class="warning-box">
                <h3>⚠️ 注意事項:</h3>
                <ul>
                    <li>処理時間が長くなる可能性があります</li>
                    <li>エラーが発生した場合も基本的なデータ保存は完了します</li>
                    <li>大量のデータ処理時は分割して実行することを推奨</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>