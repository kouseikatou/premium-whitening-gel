<?php
/**
 * 注文データエクスポートサンプル
 * 
 * 別システムで転記処理を行う場合のデータ取得例
 */

// サンプルデータ（実際はauthorize-credit-card.phpから取得）
$sampleOrderData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'transactionId' => 'TXN123456789',
    'email' => 'customer@example.com',
    'lastName' => '山田',
    'firstName' => '太郎',
    'company' => '株式会社サンプル',
    'country' => '日本',
    'state' => '東京都',
    'city' => '東京都',
    'address' => '千代田区丸の内1-1-1',
    'apartment' => '101号室',
    'zip' => '100-0001',
    'phone' => '03-1234-5678',
    'shippingLastName' => '山田',
    'shippingFirstName' => '太郎',
    'shippingCompany' => '',
    'shippingCountry' => '日本',
    'shippingState' => '東京都',
    'shippingCity' => '東京都',
    'shippingAddress' => '千代田区丸の内1-1-1',
    'shippingApartment' => '101号室',
    'shippingZip' => '100-0001',
    'shippingPhone' => '03-1234-5678',
    'amount' => '140.00',
    'isDifferentAddress' => false
];

// データフォーマットの表示
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>注文データエクスポートサンプル</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .data-format { background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0; }
        pre { background: #fff; padding: 15px; border: 1px solid #ddd; overflow-x: auto; }
        .csv-format { margin: 20px 0; }
        textarea { width: 100%; height: 150px; font-family: monospace; }
        .json-format { margin: 20px 0; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        .note { background: #fef3cd; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>注文データエクスポートサンプル</h1>
        
        <div class="note">
            <strong>注意:</strong> このページは別システムで転記処理を行う場合のデータフォーマットの参考例です。
            実際の決済処理では、authorize-credit-card.phpで生成されたデータを使用してください。
        </div>

        <h2>1. JSON形式</h2>
        <div class="json-format">
            <p>APIやWebhookで送信する場合に適した形式です。</p>
            <pre><?php echo htmlspecialchars(json_encode($sampleOrderData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)); ?></pre>
        </div>

        <h2>2. CSV形式</h2>
        <div class="csv-format">
            <p>ExcelやGoogleスプレッドシートにインポートする場合に適した形式です。</p>
            <textarea readonly><?php
// CSVヘッダー
$headers = array_keys($sampleOrderData);
echo implode(',', $headers) . "\n";

// CSVデータ
$values = array_map(function($value) {
    return '"' . str_replace('"', '""', $value) . '"';
}, array_values($sampleOrderData));
echo implode(',', $values);
?></textarea>
        </div>

        <h2>3. テーブル形式</h2>
        <table>
            <thead>
                <tr>
                    <th>フィールド名</th>
                    <th>値</th>
                    <th>説明</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fieldDescriptions = [
                    'timestamp' => '注文日時',
                    'transactionId' => '取引ID（Authorize.Netから発行）',
                    'email' => 'メールアドレス',
                    'lastName' => 'お客様名（姓）',
                    'firstName' => 'お客様名（名）',
                    'company' => '会社名',
                    'country' => '国',
                    'state' => '都道府県',
                    'city' => '市区町村',
                    'address' => '住所（区・町名）',
                    'apartment' => '住所（番地・建物）',
                    'zip' => '郵便番号',
                    'phone' => '電話番号',
                    'shippingLastName' => '配送先名（姓）',
                    'shippingFirstName' => '配送先名（名）',
                    'shippingCompany' => '配送先会社名',
                    'shippingCountry' => '配送先国',
                    'shippingState' => '配送先都道府県',
                    'shippingCity' => '配送先市区町村',
                    'shippingAddress' => '配送先住所（区・町名）',
                    'shippingApartment' => '配送先住所（番地・建物）',
                    'shippingZip' => '配送先郵便番号',
                    'shippingPhone' => '配送先電話番号',
                    'amount' => '決済金額（USD）',
                    'isDifferentAddress' => '配送先住所が異なるかどうか'
                ];
                
                foreach ($sampleOrderData as $key => $value) {
                    $description = isset($fieldDescriptions[$key]) ? $fieldDescriptions[$key] : '-';
                    $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : htmlspecialchars($value);
                    echo "<tr>";
                    echo "<td><strong>$key</strong></td>";
                    echo "<td>$displayValue</td>";
                    echo "<td>$description</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>4. 実装例</h2>
        <div class="data-format">
            <h3>PHPでデータを取得する場合</h3>
            <pre><?php echo htmlspecialchars('<?php
// authorize-credit-card.phpから送信されたデータを受け取る
$orderData = $_POST[\'order_data\'] ?? null;

if ($orderData) {
    // データを処理
    $transactionId = $orderData[\'transactionId\'];
    $email = $orderData[\'email\'];
    // ... その他のフィールド
    
    // スプレッドシートやデータベースに保存
    saveToSpreadsheet($orderData);
}
?>'); ?></pre>
        </div>

        <div class="data-format">
            <h3>JavaScriptでデータを受け取る場合</h3>
            <pre><?php echo htmlspecialchars('// Webhookエンドポイントでデータを受信
app.post(\'/webhook/order\', (req, res) => {
    const orderData = req.body.order_data;
    
    // データを処理
    console.log(\'Transaction ID:\', orderData.transactionId);
    console.log(\'Email:\', orderData.email);
    
    // レスポンス
    res.json({ status: \'success\', message: \'Data received\' });
});'); ?></pre>
        </div>

        <h2>5. セキュリティの考慮事項</h2>
        <ul>
            <li>HTTPSを使用してデータを送信する</li>
            <li>APIキーや認証トークンで送信元を検証する</li>
            <li>受信したデータのバリデーションを行う</li>
            <li>個人情報の取り扱いに注意する</li>
        </ul>
    </div>
</body>
</html>