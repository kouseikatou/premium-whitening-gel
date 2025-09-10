/**
 * Google Apps Script - 住所比較と色付け機能付き
 * 顧客住所と配送先住所が異なる場合、注文日時を赤く塗る
 */

function doPost(e) {
  try {
    const data = JSON.parse(e.postData.contents);
    console.log('Received data:', data);
    
    const spreadsheetId = data.spreadsheetId || '11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs';
    
    // スプレッドシートを開く
    const spreadsheet = SpreadsheetApp.openById(spreadsheetId);
    let sheet = spreadsheet.getSheetByName('Orders');
    
    // Ordersシートが存在しない場合は作成
    if (!sheet) {
      sheet = spreadsheet.insertSheet('Orders');
      // ヘッダー行を追加（注文日時を含む指定された順番）
      const headers = [
        '注文日時',                    // A
        '取引ID',                      // B
        'メールアドレス',              // C
        'お客様名（姓）',              // D
        'お客様名（名）',              // E
        '会社名',                      // F
        '国',                          // G
        '都道府県',                    // H
        '市区町村',                    // I
        '住所（区・町名）',            // J
        '住所（番地・建物）',          // K
        '郵便番号',                    // L
        '電話番号',                    // M
        '配送先名（姓）',              // N
        '配送先名（名）',              // O
        '配送先会社名',                // P
        '配送先国',                    // Q
        '配送先都道府県',              // R
        '配送先市区町村',              // S
        '配送先住所（区・町名）',      // T
        '配送先住所（番地・建物）',    // U
        '配送先郵便番号',              // V
        '配送先電話番号',              // W
        '決済金額（USD）'               // X
      ];
      sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
      sheet.getRange(1, 1, 1, headers.length).setFontWeight('bold');
    }
    
    // 新しい行データを準備（注文日時を含む順序に合わせる）
    const rowData = [
      data.timestamp || new Date().toLocaleString('ja-JP'), // A: 注文日時
      data.transactionId || '',                             // B: 取引ID
      data.email || '',                                     // C: メールアドレス
      data.lastName || '',                                  // D: お客様名（姓）
      data.firstName || '',                                 // E: お客様名（名）
      data.company || '',                                   // F: 会社名
      data.country || '',                                   // G: 国
      data.state || '',                                     // H: 都道府県
      data.city || '',                                      // I: 市区町村
      data.address || '',                                   // J: 住所（区・町名）
      data.apartment || '',                                 // K: 住所（番地・建物）
      data.zip || '',                                       // L: 郵便番号
      data.phone || '',                                     // M: 電話番号
      data.shippingLastName || '',                          // N: 配送先名（姓）
      data.shippingFirstName || '',                         // O: 配送先名（名）
      data.shippingCompany || '',                           // P: 配送先会社名
      data.shippingCountry || '',                           // Q: 配送先国
      data.shippingState || '',                             // R: 配送先都道府県
      data.shippingCity || '',                              // S: 配送先市区町村
      data.shippingAddress || '',                           // T: 配送先住所（区・町名）
      data.shippingApartment || '',                         // U: 配送先住所（番地・建物）
      data.shippingZip || '',                               // V: 配送先郵便番号
      data.shippingPhone || '',                             // W: 配送先電話番号
      data.amount || ''                                     // X: 決済金額（USD）
    ];
    
    // 行を追加
    const newRowIndex = sheet.getLastRow() + 1;
    sheet.getRange(newRowIndex, 1, 1, rowData.length).setValues([rowData]);
    
    // 住所が異なる場合は注文日時（A列）を赤く塗る
    if (data.isDifferentAddress === true) {
      const timestampCell = sheet.getRange(newRowIndex, 1);
      timestampCell.setBackground('#ffcccc'); // 薄い赤色
      timestampCell.setFontColor('#cc0000');  // 赤文字
      
      // 備考として住所相違フラグを追加（Y列：25列目）
      const remarkCell = sheet.getRange(newRowIndex, 25);
      remarkCell.setValue('住所相違');
      remarkCell.setBackground('#ffcccc');
      remarkCell.setFontColor('#cc0000');
    }
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'データがスプレッドシートに正常に追加されました',
        rowIndex: newRowIndex,
        addressDifferent: data.isDifferentAddress || false
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    console.error('Error in doPost:', error);
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        error: error.toString(),
        stack: error.stack
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({
      message: 'Google Apps Script Web App (住所比較機能付き) が動作中',
      timestamp: new Date().toISOString(),
      version: '1.1'
    }))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * テスト用関数
 */
function testAddressComparison() {
  const testData = {
    spreadsheetId: '11TCDJ9TDPcfB-Ileiz9DrZZOdmMTEMnoHyj3L5cnTgs',
    timestamp: new Date().toLocaleString('ja-JP'),
    transactionId: 'TEST-' + Date.now(),
    email: 'test@example.com',
    lastName: '山田',
    firstName: '太郎',
    company: 'テスト会社',
    country: '日本',
    state: '東京都',
    city: '東京',
    address: '新宿区',
    apartment: '1-2-3',
    zip: '100-0001',
    phone: '090-1234-5678',
    shippingLastName: '佐藤',
    shippingFirstName: '花子',
    shippingCompany: 'テスト配送会社',
    shippingCountry: '日本',
    shippingState: '東京都',
    shippingCity: '東京',
    shippingAddress: '渋谷区',
    shippingApartment: '2-3-4',
    shippingZip: '100-0002',
    shippingPhone: '090-8765-4321',
    amount: '140.00',
    isDifferentAddress: true
  };
  
  const mockEvent = {
    postData: {
      contents: JSON.stringify(testData)
    }
  };
  
  const result = doPost(mockEvent);
  console.log('Test result:', result.getContent());
}