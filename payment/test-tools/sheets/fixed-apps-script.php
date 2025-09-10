<?php
/**
 * 修正されたApps Script コード
 * 
 * FALSE問題を解決したコード
 */

require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>🔧 修正されたApps Script</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f5f7fa; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { background: #e74c3c; color: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 2.5em; }
        .section { background: #fff; margin: 20px 0; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section h2 { color: #2c3e50; border-bottom: 2px solid #e74c3c; padding-bottom: 10px; margin-top: 0; }
        .error-box { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .success-box { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .code-block { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; overflow-x: auto; }
        .code-block pre { margin: 0; white-space: pre-wrap; font-size: 13px; line-height: 1.4; }
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
        .btn:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .nav-links { text-align: center; margin: 20px 0; }
        .nav-links a { color: #3498db; text-decoration: none; margin: 0 15px; padding: 8px 16px; background: #ecf0f1; border-radius: 5px; }
        .highlight { background: #ffeb3b; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 修正されたApps Script</h1>
            <p>FALSE問題を解決したコード</p>
        </div>

        <div class="nav-links">
            <a href="quick-test.php">⚡ クイックテスト</a>
            <a href="enhanced-apps-script.php">🚀 強化版</a>
            <a href="index.php">📊 Sheetsポータル</a>
        </div>

        <div class="section">
            <h2>🚨 問題の原因</h2>
            <div class="error-box">
                <strong>FALSE が大量に追加される理由:</strong>
                <ul>
                    <li>データマッピングで存在しないフィールドを参照</li>
                    <li><code>data.isDifferentAddress || false</code> の処理</li>
                    <li>空のフィールドが <code>false</code> として出力される</li>
                    <li>25列のデータに対して不正な列参照</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2>✅ 修正されたコード</h2>
            
            <div class="success-box">
                <strong>以下のコードをApps Scriptに貼り付けてください:</strong>
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
    
    // データを行として準備（空文字列で初期化、FALSEを回避）
    const rowData = [
      data.timestamp || '',
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
      data.amount || ''
      // isDifferentAddress は除外（FALSEを回避）
    ];
    
    // 行を追加
    sheet.appendRow(rowData);
    
    // 追加された行番号を取得
    const newRowNumber = sheet.getLastRow();
    
    // 自動処理を実行
    processNewOrder(spreadsheet, sheet, newRowNumber, data);
    
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

// 新しい注文の処理
function processNewOrder(spreadsheet, sourceSheet, row, originalData) {
  try {
    console.log('Processing new order at row:', row);
    
    // 発送用CSVシートへの転記
    copyToShippingSheet(spreadsheet, sourceSheet, row);
    
    // 月次管理シートへの転記
    copyToMonthlySheet(spreadsheet, sourceSheet, row);
    
    console.log('Order processing completed');
    
  } catch (error) {
    console.error('Error in processNewOrder:', error);
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
    
    // データを取得（24列まで）
    const data = sourceSheet.getRange(row, 1, 1, 24).getValues()[0];
    
    // データマッピング（インデックスを修正）
    const shippingData = [
      data[2] || '',   // Email (C列 - インデックス2)
      ((data[13] || '') + " " + (data[14] || '')).trim(),  // Name (N列 + O列)
      data[22] || '',  // Phone (W列 - インデックス22)
      data[19] || '',  // Address (T列 - インデックス19)
      data[20] || '',  // Address Line 2 (U列 - インデックス20)
      data[18] || '',  // City (S列 - インデックス18)
      data[17] || '',  // State (R列 - インデックス17)
      data[21] || '',  // Zipcode (V列 - インデックス21)
      data[16] || '',  // Country (Q列 - インデックス16)
      data[1] || '',   // Order ID (B列 - インデックス1)
      '',              // Order Items (空欄)
      '',              // Pounds (空欄)
      '',              // Length (空欄)
      '',              // Width (空欄)
      ''               // Height (空欄)
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
    
    // Orders シートのデータをコピー（24列まで）
    const data = sourceSheet.getRange(row, 1, 1, 24).getValues();
    
    // 月次シートに書き込み
    monthlySheet.getRange(lastRow, 1, 1, 24).setValues(data);
    
    // ステータス列に"新規"を設定（25列目）
    monthlySheet.getRange(lastRow, 25).setValue("新規");
    
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
  
  // ヘッダーを設定（25列）
  const headers = [
    "注文日時", "取引ID", "メールアドレス", "お客様名（名）", "お客様名（姓）",
    "会社名", "住所（区・町名）", "住所（番地・建物）", "市区町村", "都道府県",
    "郵便番号", "国", "電話番号", "配送先名（名）", "配送先名（姓）",
    "配送先会社名", "配送先国", "配送先都道府県", "配送先市区町村", "配送先住所（区・町名）",
    "配送先住所（番地・建物）", "配送先郵便番号", "配送先電話番号", "決済金額（USD）", "処理ステータス"
  ];
  
  newSheet.getRange(1, 1, 1, headers.length).setValues([headers]);
  
  // ヘッダー行の書式設定
  newSheet.getRange(1, 1, 1, headers.length)
    .setBackground("#4285F4")
    .setFontColor("#FFFFFF")
    .setFontWeight("bold");
  
  return newSheet;
}

function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({
      message: 'Google Apps Script Web App is running',
      timestamp: new Date().toISOString(),
      version: '3.0 - Fixed FALSE issue'
    }))
    .setMimeType(ContentService.MimeType.JSON);
}</pre>
            </div>
        </div>

        <div class="section">
            <h2>🔍 修正内容</h2>
            
            <div class="success-box">
                <h3>✅ 主な修正点:</h3>
                <ul>
                    <li><strong>FALSEの除去</strong>: <code>isDifferentAddress</code> フィールドを除外</li>
                    <li><strong>空文字列の使用</strong>: <code>|| ''</code> で空文字列を設定</li>
                    <li><strong>データ範囲の修正</strong>: 24列までに制限</li>
                    <li><strong>インデックス修正</strong>: 正しい列インデックスを使用</li>
                    <li><strong>エラーハンドリング</strong>: 各関数で適切なエラー処理</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2>🚀 更新手順</h2>
            
            <ol style="font-size: 1.1em; line-height: 1.6;">
                <li><strong>Apps Script エディタを開く</strong></li>
                <li><strong>上記のコードで置き換え</strong></li>
                <li><strong>保存してデプロイ</strong></li>
                <li><strong>既存のFALSEデータを削除</strong> (必要に応じて)</li>
                <li><strong>テスト実行</strong></li>
            </ol>
            
            <div class="nav-links">
                <a href="https://script.google.com/" target="_blank" class="btn btn-danger">Apps Script を開く</a>
                <a href="quick-test.php" class="btn">クイックテスト</a>
            </div>
        </div>

        <div class="section">
            <h2>🧹 既存データのクリーンアップ</h2>
            
            <div class="error-box">
                <strong>既存のFALSEデータを削除する方法:</strong>
                <ol>
                    <li>スプレッドシートを開く</li>
                    <li>Ctrl+H（検索と置換）を開く</li>
                    <li>「検索」に <code>FALSE</code> を入力</li>
                    <li>「置換」は空欄のまま</li>
                    <li>「すべて置換」をクリック</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>