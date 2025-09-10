<?php
/**
 * 外部転記システムへのデータ送信
 * 
 * このファイルは決済完了データを外部システムに送信するための
 * インターフェースを提供します
 */

require_once 'config.php';

/**
 * 外部システムへ注文データを送信
 * 
 * @param array $orderData 注文データ
 * @return bool 送信成功の場合true
 */
function sendToExternalSystem($orderData) {
    try {
        // 外部システムのエンドポイント（config.phpで定義）
        $externalUrl = defined('EXTERNAL_SYSTEM_URL') ? EXTERNAL_SYSTEM_URL : '';
        
        if (empty($externalUrl)) {
            error_log("External system URL is not configured");
            return false;
        }
        
        // データを送信用に準備
        $postData = [
            'order_data' => $orderData,
            'timestamp' => date('Y-m-d H:i:s'),
            'source' => 'authorize_net_payment',
            'api_version' => '1.0'
        ];
        
        // cURLで送信
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $externalUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("External system communication error: " . $error);
            return false;
        }
        
        if ($httpCode >= 200 && $httpCode < 300) {
            error_log("Successfully sent data to external system. Response: " . $response);
            return true;
        } else {
            error_log("External system returned error. HTTP Code: " . $httpCode . ", Response: " . $response);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Exception in sendToExternalSystem: " . $e->getMessage());
        return false;
    }
}

/**
 * Webhook形式でデータを受信する（外部システムから呼び出される場合）
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'receive') {
    // POSTデータを取得
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if ($data && isset($data['order_data'])) {
        // ここで受信したデータを処理
        error_log("Received order data from external source: " . json_encode($data['order_data']));
        
        // 成功レスポンス
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Data received']);
    } else {
        // エラーレスポンス
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
    exit;
}

/**
 * データフォーマットの例
 * 
 * $orderData = [
 *     'transactionId' => 'TXN123456',
 *     'email' => 'customer@example.com',
 *     'firstName' => '太郎',
 *     'lastName' => '山田',
 *     'company' => '株式会社サンプル',
 *     'address' => '千代田区丸の内1-1-1',
 *     'apartment' => '101号室',
 *     'city' => '東京都',
 *     'state' => '東京都',
 *     'zip' => '100-0001',
 *     'country' => '日本',
 *     'phone' => '03-1234-5678',
 *     'shippingFirstName' => '太郎',
 *     'shippingLastName' => '山田',
 *     'shippingCompany' => '',
 *     'shippingAddress' => '千代田区丸の内1-1-1',
 *     'shippingApartment' => '101号室',
 *     'shippingCity' => '東京都',
 *     'shippingState' => '東京都',
 *     'shippingZip' => '100-0001',
 *     'shippingCountry' => '日本',
 *     'shippingPhone' => '03-1234-5678',
 *     'amount' => '140.00',
 *     'isDifferentAddress' => false
 * ];
 */
?>