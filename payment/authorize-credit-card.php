<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>INFINITE TOOTH</title>
    <link rel="stylesheet" href="stely-top.css">
    
</head>

<header>
    <div class="head-1">INFINITE TOOTH</div>
</header>

<body>

  

<?php
  // ファイルの存在確認とエラーハンドリング
  if (!file_exists('autoload.php')) {
      die('Error: autoload.php not found. Please upload all SDK files to the server.');
  }
  if (!file_exists('SampleCodeConstants.php')) {
      die('Error: SampleCodeConstants.php not found. Please upload all SDK files to the server.');
  }
  if (!file_exists('config.php')) {
      die('Error: config.php not found. Please upload all required files to the server.');
  }
  if (!file_exists('google-sheets-complete.php')) {
      die('Error: google-sheets-complete.php not found. Please upload all required files to the server.');
  }
  
  require 'autoload.php';
  require_once 'config.php';
  require_once 'SampleCodeConstants.php';
  require_once 'google-sheets-complete.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  // 環境設定を確実に取得（config.phpから、または直接定義）
  if (!defined('AUTHORIZENET_ENVIRONMENT')) {
      // config.phpが読み込まれていない場合のフォールバック
      define('AUTHORIZENET_ENVIRONMENT', getenv('AUTHORIZENET_ENVIRONMENT') ?: 'PRODUCTION');
  }
  
  // 環境設定（config.phpで定義するか、環境変数から取得）
  $apiEnvironment = AUTHORIZENET_ENVIRONMENT;
  
  // 環境に応じたエンドポイントを設定
  $environment = ($apiEnvironment === 'PRODUCTION') ? 
                 \net\authorize\api\constants\ANetEnvironment::PRODUCTION : 
                 \net\authorize\api\constants\ANetEnvironment::SANDBOX;

/**
 * 顧客住所と配送先住所が異なるかチェックする関数
 */
function checkAddressDifference($orderData) {
    // 比較対象フィールド
    $billingFields = [
        'firstName' => $orderData['firstName'] ?? '',
        'lastName' => $orderData['lastName'] ?? '',
        'company' => $orderData['company'] ?? '',
        'address' => $orderData['address'] ?? '',
        'apartment' => $orderData['apartment'] ?? '',
        'city' => $orderData['city'] ?? '',
        'state' => $orderData['state'] ?? '',
        'zip' => $orderData['zip'] ?? '',
        'country' => $orderData['country'] ?? '',
        'phone' => $orderData['phone'] ?? ''
    ];
    
    $shippingFields = [
        'firstName' => $orderData['shippingFirstName'] ?? '',
        'lastName' => $orderData['shippingLastName'] ?? '',
        'company' => $orderData['shippingCompany'] ?? '',
        'address' => $orderData['shippingAddress'] ?? '',
        'apartment' => $orderData['shippingApartment'] ?? '',
        'city' => $orderData['shippingCity'] ?? '',
        'state' => $orderData['shippingState'] ?? '',
        'zip' => $orderData['shippingZip'] ?? '',
        'country' => $orderData['shippingCountry'] ?? '',
        'phone' => $orderData['shippingPhone'] ?? ''
    ];
    
    // 文字列を正規化（空白削除、小文字変換）して比較
    foreach ($billingFields as $key => $value) {
        $billing = trim(strtolower($value));
        $shipping = trim(strtolower($shippingFields[$key]));
        
        // 空の配送先フィールドは請求先と同じとみなす
        if (empty($shipping)) {
            continue;
        }
        
        if ($billing !== $shipping) {
            return true; // 異なる住所
        }
    }
    
    return false; // 同じ住所
}


function createAnAcceptPaymentTransaction($amount)
{
    // グローバル変数を関数内で使用
    global $apiEnvironment, $environment;
    
    try {
        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
        $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
        
        // Validate merchant authentication
        if (empty(\SampleCodeConstants::MERCHANT_LOGIN_ID) || empty(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY)) {
            throw new \Exception('Merchant authentication credentials are not configured');
        }
    
    // Set the transaction's refId (max 20 characters)
    $refId = 'ref' . substr(time(), -6) . rand(100, 999);
    
    // 入力値のバリデーション（Accept.jsのデータはサニタイズしない）
    $dataDescriptor = isset($_POST['dataDescriptor']) ? $_POST['dataDescriptor'] : '';
    $dataValue = isset($_POST['dataValue']) ? $_POST['dataValue'] : '';
    
    if (empty($dataDescriptor) || empty($dataValue)) {
        throw new \Exception('Payment data is missing');
    }


    // Create the payment object for a payment nonce
    $opaqueData = new AnetAPI\OpaqueDataType();
    $opaqueData->setDataDescriptor($dataDescriptor);
    $opaqueData->setDataValue($dataValue);

    
    // Add the payment data to a paymentType object
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setOpaqueData($opaqueData);


    // Create order information
    $order = new AnetAPI\OrderType();
    $order->setInvoiceNumber("1");
    $order->setDescription("INFINITE TOOTH");

    // Set the customer's Bill To address with validation
    $customerAddress = new AnetAPI\CustomerAddressType();
    $FirstName = isset($_POST['FirstName']) ? htmlspecialchars($_POST['FirstName'], ENT_QUOTES, 'UTF-8') : '';
    $LastName = isset($_POST['LastName']) ? htmlspecialchars($_POST['LastName'], ENT_QUOTES, 'UTF-8') : '';
    $Company = isset($_POST['Company']) ? htmlspecialchars($_POST['Company'], ENT_QUOTES, 'UTF-8') : '';

    $zip = isset($_POST['zip']) ? htmlspecialchars($_POST['zip'], ENT_QUOTES, 'UTF-8') : '';
    $x_ship_to_country = isset($_POST['x_ship_to_country']) ? htmlspecialchars($_POST['x_ship_to_country'], ENT_QUOTES, 'UTF-8') : '';
    $treet = isset($_POST['street']) ? htmlspecialchars($_POST['street'], ENT_QUOTES, 'UTF-8') : '';
    $city = isset($_POST['city']) ? htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8') : '';
    $select = isset($_POST['select']) ? htmlspecialchars($_POST['select'], ENT_QUOTES, 'UTF-8') : '';

    $customerAddress->setFirstName($FirstName);
    $customerAddress->setLastName($LastName);
    $customerAddress->setCompany($Company);

    $customerAddress->setAddress($treet);
    $customerAddress->setCity($city);
    $customerAddress->setState($select);
    $customerAddress->setZip($zip);
    $customerAddress->setCountry($x_ship_to_country);

    $shippingAddress = new AnetAPI\CustomerAddressType();
    $shippingFirstName = isset($_POST['shippingFirstName']) ? htmlspecialchars($_POST['shippingFirstName'], ENT_QUOTES, 'UTF-8') : '';
    $shippingLastName = isset($_POST['shippingLastName']) ? htmlspecialchars($_POST['shippingLastName'], ENT_QUOTES, 'UTF-8') : '';
    $shippingCompany = isset($_POST['shippingCompany']) ? htmlspecialchars($_POST['shippingCompany'], ENT_QUOTES, 'UTF-8') : '';

    $shippingZip = isset($_POST['shippingZip']) ? htmlspecialchars($_POST['shippingZip'], ENT_QUOTES, 'UTF-8') : '';
    $shippingCountry = isset($_POST['shippingCountry']) ? htmlspecialchars($_POST['shippingCountry'], ENT_QUOTES, 'UTF-8') : '';
    $shippingStreet = isset($_POST['shippingStreet']) ? htmlspecialchars($_POST['shippingStreet'], ENT_QUOTES, 'UTF-8') : '';
    $shippingCity = isset($_POST['shippingCity']) ? htmlspecialchars($_POST['shippingCity'], ENT_QUOTES, 'UTF-8') : '';
    $shippingState = isset($_POST['shippingState']) ? htmlspecialchars($_POST['shippingState'], ENT_QUOTES, 'UTF-8') : '';

    $shippingAddress->setFirstName($shippingFirstName);
    $shippingAddress->setLastName($shippingLastName);
    $shippingAddress->setCompany($shippingCompany);

    $shippingAddress->setAddress($shippingStreet);
    $shippingAddress->setCity($shippingCity);
    $shippingAddress->setState($shippingState);
    $shippingAddress->setZip($shippingZip);
    $shippingAddress->setCountry($shippingCountry);

    // Set the customer's identifying information
    $customerData = new AnetAPI\CustomerDataType();
    $mell_contact = isset($_POST['mell-contact']) ? filter_var($_POST['mell-contact'], FILTER_SANITIZE_EMAIL) : '';
    $contact = isset($_POST['contact']) ? htmlspecialchars($_POST['contact'], ENT_QUOTES, 'UTF-8') : '';

    $customerData->setType("individual");
    $customerData->setId($contact);
    $customerData->setEmail($mell_contact);

    // Add values for transaction settings
    $duplicateWindowSetting = new AnetAPI\SettingType();
    $duplicateWindowSetting->setSettingName("duplicateWindow");
    $duplicateWindowSetting->setSettingValue("60");

    // Add some merchant defined fields. These fields won't be stored with the transaction,
    // but will be echoed back in the response.
    //$merchantDefinedField1 = new AnetAPI\UserFieldType();
    //$merchantDefinedField1->setName("customerLoyaltyNum");
    //$merchantDefinedField1->setValue("1128836273");

    //$merchantDefinedField2 = new AnetAPI\UserFieldType();
    //$merchantDefinedField2->setName("favoriteColor");
    //$merchantDefinedField2->setValue("blue");

    // Create a TransactionRequestType object and add the previous objects to it


    // デバッグ: 受信したデータを確認
    error_log("dataDescriptor: " . $dataDescriptor);
    error_log("dataValue length: " . strlen($dataValue));
    error_log("Environment: " . $apiEnvironment);
    error_log("API Endpoint: " . ($environment === \net\authorize\api\constants\ANetEnvironment::PRODUCTION ? 'PRODUCTION' : 'SANDBOX'));
    
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authCaptureTransaction"); 
    $transactionRequestType->setAmount($amount);
    $transactionRequestType->setOrder($order);
    $transactionRequestType->setPayment($paymentOne);
    $transactionRequestType->setBillTo($customerAddress);
    $transactionRequestType->setShipTo($shippingAddress); // 出荷情報を追加
    $transactionRequestType->setCustomer($customerData);
    $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
    //$transactionRequestType->addToUserFields($merchantDefinedField1);
    //$transactionRequestType->addToUserFields($merchantDefinedField2);

    // Assemble the complete transaction request
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest($transactionRequestType);

    // Create the controller and get the response
    $controller = new AnetController\CreateTransactionController($request);
    
    // Debug: 環境情報のみログ出力（認証情報は出力しない）
    error_log("Environment: " . $apiEnvironment);
    
    $response = $controller->executeWithApiResponse($environment);
    

    if ($response != null) {
        // Debug: レスポンス全体を確認
        error_log("Response ResultCode: " . $response->getMessages()->getResultCode());
        
        // Check to see if the API request was successfully received and acted upon
        if ($response->getMessages()->getResultCode() == "Ok") {
            // Since the API request was successful, look for a transaction response
            // and parse it to display the results of authorizing the card
            $tresponse = $response->getTransactionResponse();
        
            if ($tresponse != null && $tresponse->getMessages() != null) {
                echo "
                <div style='max-width: 600px; margin: 40px auto; padding: 40px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); text-align: center; color: #333;'>
                    <div style='width: 80px; height: 80px; border: 3px solid #28a745; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center;'>
                        <div style='font-size: 32px; color: #28a745;'>✓</div>
                    </div>
                    <h1 style='font-size: 28px; margin-bottom: 12px; font-weight: 600; color: #333;'>ご購入ありがとうございました</h1>
                    <p style='font-size: 16px; margin-bottom: 32px; color: #666;'>Thank you for your purchase!</p>
                    
                    <div style='background: #f8f9fa; border: 1px solid #e9ecef; padding: 24px; border-radius: 6px; margin: 24px 0; text-align: left;'>
                        <h3 style='margin-bottom: 16px; font-size: 18px; color: #333; text-align: center; border-bottom: 1px solid #dee2e6; padding-bottom: 12px;'>注文詳細 / Order Details</h3>
                        <div style='display: grid; gap: 12px;'>
                            <div style='display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;'>
                                <span style='color: #666;'>取引ID / Transaction ID:</span>
                                <span style='font-weight: 600; color: #333;'>" . $tresponse->getTransId() . "</span>
                            </div>
                            <div style='display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;'>
                                <span style='color: #666;'>ステータス / Status:</span>
                                <span style='font-weight: 600; color: #28a745;'>" . $tresponse->getMessages()[0]->getDescription() . "</span>
                            </div>
                            <div style='display: flex; justify-content: space-between; padding: 8px 0;'>
                                <span style='color: #666;'>金額 / Amount:</span>
                                <span style='font-weight: 600; color: #333;'>$" . htmlspecialchars($amount, ENT_QUOTES, 'UTF-8') . " USD</span>
                            </div>
                        </div>
                    </div>";
                
                // Response Code 4 = Held for Review
                if ($tresponse->getResponseCode() == "4") {
                    echo "
                    <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 16px; border-radius: 6px; margin: 16px 0; text-align: left;'>
                        <h4 style='margin-bottom: 8px; color: #856404; font-size: 16px;'>⚠️ セキュリティレビュー中</h4>
                        <p style='font-size: 14px; margin: 0; color: #856404;'>ご注文は一時的に保留中です。確認が完了次第、処理を進めさせていただきます。</p>
                    </div>";
                }
                
                echo "
                    <div style='margin-top: 32px; padding-top: 24px; border-top: 1px solid #e9ecef; text-align: center;'>
                        <div style='background: #e7f3ff; border: 1px solid #bee5eb; padding: 16px; border-radius: 6px; margin-bottom: 16px;'>
                            <p style='font-size: 14px; margin: 0; color: #0c5460; font-weight: 500;'>📧 確認メールをお送りしています</p>
                        </div>
                        <p style='font-size: 14px; color: #666; margin: 4px 0;'>商品は2-3営業日以内に発送予定です</p>
                        <p style='font-size: 14px; color: #666; margin: 4px 0;'>Your order will be shipped within 2-3 business days</p>
                    </div>
                </div>";

                // 決済成功時にGoogleスプレッドシートに注文情報を送信（サニタイズ済みの値を使用）
                $orderData = [
                    'transactionId' => $tresponse->getTransId(),
                    'email' => $mell_contact,
                    'firstName' => $FirstName,
                    'lastName' => $LastName,
                    'company' => $Company,
                    'address' => $treet,
                    'apartment' => isset($_POST['apartment']) ? htmlspecialchars($_POST['apartment'], ENT_QUOTES, 'UTF-8') : '',
                    'city' => $city,
                    'state' => $select,
                    'zip' => $zip,
                    'country' => $x_ship_to_country,
                    'phone' => $contact,
                    'shippingFirstName' => $shippingFirstName,
                    'shippingLastName' => $shippingLastName,
                    'shippingCompany' => $shippingCompany,
                    'shippingAddress' => $shippingStreet,
                    'shippingApartment' => isset($_POST['shippingApartment']) ? htmlspecialchars($_POST['shippingApartment'], ENT_QUOTES, 'UTF-8') : '',
                    'shippingCity' => $shippingCity,
                    'shippingState' => $shippingState,
                    'shippingZip' => $shippingZip,
                    'shippingCountry' => $shippingCountry,
                    'shippingPhone' => isset($_POST['shippingPhone']) ? htmlspecialchars($_POST['shippingPhone'], ENT_QUOTES, 'UTF-8') : '',
                    'amount' => $amount
                ];
                
                // 住所比較：顧客住所と配送先住所が異なるかチェック
                $isDifferentAddress = checkAddressDifference($orderData);
                $orderData['isDifferentAddress'] = $isDifferentAddress;
                
                // Googleスプレッドシートに送信（別システムで転記する場合はコメントアウト）
                $sheetsResult = sendOrderToGoogleSheets($orderData);
                
                // データ送信の準備（別システムへの転送用）
                // このデータを使って別の転記システムと連携できます
                $orderDataJson = json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                
                // デバッグ用：送信データをログに記録（本番環境ではコメントアウト推奨）
                error_log("Order data for external system: " . $orderDataJson);
                
                // 管理者向けの記号表示（データ準備完了の確認）
                echo "<!-- SYS_LOG: ✓ Order data prepared for transfer -->";
                echo "<div style='position: fixed; bottom: 20px; right: 20px; background: rgba(40, 167, 69, 0.8); color: white; padding: 8px 12px; border-radius: 50%; font-size: 18px; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2);' title='データ準備完了'>✓</div>";
                
                if ($orderData['isDifferentAddress']) {
                    echo "<!-- SYS_LOG: Address difference detected -->";
                    echo "<div style='position: fixed; bottom: 20px; right: 70px; background: rgba(220, 53, 69, 0.8); color: white; padding: 8px 12px; border-radius: 50%; font-size: 18px; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2);' title='配送先住所が異なります'>⚠</div>";
                }
                
                // 記号を3秒後に自動で非表示にする
                echo "<script>
                setTimeout(function() {
                    const indicators = document.querySelectorAll('div[style*=\"position: fixed; bottom: 20px; right:\"]');
                    indicators.forEach(function(indicator) {
                        indicator.style.opacity = '0';
                        indicator.style.transition = 'opacity 0.5s';
                        setTimeout(function() {
                            indicator.remove();
                        }, 500);
                    });
                }, 3000);
                </script>";
                
                // 別システムへのデータ送信（必要に応じて実装）
                // sendToExternalSystem($orderData);
                
            } else {
                // Transaction failed - show error in result screen format
                $errorCode = $tresponse->getErrors() != null ? $tresponse->getErrors()[0]->getErrorCode() : 'Unknown';
                $errorMessage = $tresponse->getErrors() != null ? $tresponse->getErrors()[0]->getErrorText() : 'Unknown error occurred';
                
                echo "
                <div style='max-width: 600px; margin: 40px auto; padding: 40px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); text-align: center; color: #333;'>
                    <div style='width: 80px; height: 80px; border: 3px solid #dc3545; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center;'>
                        <div style='font-size: 32px; color: #dc3545;'>✗</div>
                    </div>
                    <h1 style='font-size: 28px; margin-bottom: 12px; font-weight: 600; color: #dc3545;'>決済に失敗しました</h1>
                    <p style='font-size: 16px; margin-bottom: 32px; color: #666;'>Payment Failed</p>
                    <div style='background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 24px;'>
                        <p style='font-size: 14px; color: #666; margin: 0 0 8px 0;'>エラーコード: <strong style='color: #dc3545;'>" . htmlspecialchars($errorCode) . "</strong></p>
                        <p style='font-size: 14px; color: #666; margin: 0;'>エラーメッセージ: <strong style='color: #dc3545;'>" . htmlspecialchars($errorMessage) . "</strong></p>
                    </div>
                    <button onclick='window.history.back()' style='background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer; margin-right: 12px;'>戻る</button>
                    <button onclick='window.location.reload()' style='background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer;'>再試行</button>
                </div>
                ";
            }
            // Or, print errors if the API request wasn't successful
        } else {
            // API request failed - show error in result screen format
            $tresponse = $response->getTransactionResponse();
            
            if ($tresponse != null && $tresponse->getErrors() != null) {
                $errorCode = $tresponse->getErrors()[0]->getErrorCode();
                $errorMessage = $tresponse->getErrors()[0]->getErrorText();
            } else {
                $errorCode = $response->getMessages()->getMessage()[0]->getCode();
                $errorMessage = $response->getMessages()->getMessage()[0]->getText();
            }
            
            echo "
            <div style='max-width: 600px; margin: 40px auto; padding: 40px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); text-align: center; color: #333;'>
                <div style='width: 80px; height: 80px; border: 3px solid #dc3545; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center;'>
                    <div style='font-size: 32px; color: #dc3545;'>✗</div>
                </div>
                <h1 style='font-size: 28px; margin-bottom: 12px; font-weight: 600; color: #dc3545;'>決済に失敗しました</h1>
                <p style='font-size: 16px; margin-bottom: 32px; color: #666;'>Payment Failed</p>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 24px;'>
                    <p style='font-size: 14px; color: #666; margin: 0 0 8px 0;'>エラーコード: <strong style='color: #dc3545;'>" . htmlspecialchars($errorCode) . "</strong></p>
                    <p style='font-size: 14px; color: #666; margin: 0;'>エラーメッセージ: <strong style='color: #dc3545;'>" . htmlspecialchars($errorMessage) . "</strong></p>
                </div>
                <button onclick='window.history.back()' style='background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer; margin-right: 12px;'>戻る</button>
                <button onclick='window.location.reload()' style='background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer;'>再試行</button>
            </div>
            ";
        }      
    } else {
        // No response returned - show error in result screen format
        echo "
        <div style='max-width: 600px; margin: 40px auto; padding: 40px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); text-align: center; color: #333;'>
            <div style='width: 80px; height: 80px; border: 3px solid #dc3545; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center;'>
                <div style='font-size: 32px; color: #dc3545;'>✗</div>
            </div>
            <h1 style='font-size: 28px; margin-bottom: 12px; font-weight: 600; color: #dc3545;'>決済に失敗しました</h1>
            <p style='font-size: 16px; margin-bottom: 32px; color: #666;'>Payment Failed</p>
            <div style='background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 24px;'>
                <p style='font-size: 14px; color: #666; margin: 0 0 8px 0;'>エラーコード: <strong style='color: #dc3545;'>CONNECTION_ERROR</strong></p>
                <p style='font-size: 14px; color: #666; margin: 0;'>エラーメッセージ: <strong style='color: #dc3545;'>サーバーからの応答がありません</strong></p>
            </div>
            <button onclick='window.history.back()' style='background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer; margin-right: 12px;'>戻る</button>
            <button onclick='window.location.reload()' style='background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer;'>再試行</button>
        </div>
        ";
    }

    return $response;
    
    } catch (\Exception $e) {
        // エラーをログに記録
        error_log('Payment processing error: ' . $e->getMessage());
        
        // エラー画面を表示
        echo "
        <div style='max-width: 600px; margin: 40px auto; padding: 40px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); text-align: center; color: #333;'>
            <div style='width: 80px; height: 80px; border: 3px solid #dc3545; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center;'>
                <div style='font-size: 32px; color: #dc3545;'>✗</div>
            </div>
            <h1 style='font-size: 28px; margin-bottom: 12px; font-weight: 600; color: #dc3545;'>システムエラーが発生しました</h1>
            <p style='font-size: 16px; margin-bottom: 32px; color: #666;'>System Error</p>
            <div style='background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 24px;'>
                <p style='font-size: 14px; color: #666; margin: 0;'>エラーメッセージ: <strong style='color: #dc3545;'>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</strong></p>
            </div>
            <button onclick='window.history.back()' style='background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer;'>戻る</button>
        </div>
        ";
        
        return null;
    }
}

if (!defined('DONT_RUN_SAMPLES')) {
    try {
        // 金額をパラメータから取得するか、デフォルト値を使用
        $amount = isset($_POST['amount']) ? filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT) : 140.00;
        
        if ($amount === false || $amount <= 0) {
            throw new Exception('Invalid amount specified');
        }
        
        createAnAcceptPaymentTransaction(number_format($amount, 2, '.', ''));
    } catch (Exception $e) {
        // エラー表示
        echo "
        <div style='max-width: 600px; margin: 40px auto; padding: 40px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); text-align: center; color: #333;'>
            <div style='width: 80px; height: 80px; border: 3px solid #dc3545; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center;'>
                <div style='font-size: 32px; color: #dc3545;'>✗</div>
            </div>
            <h1 style='font-size: 28px; margin-bottom: 12px; font-weight: 600; color: #dc3545;'>エラーが発生しました</h1>
            <p style='font-size: 16px; margin-bottom: 32px; color: #666;'>An Error Occurred</p>
            <div style='background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 24px;'>
                <p style='font-size: 14px; color: #666; margin: 0 0 8px 0;'>エラーメッセージ: <strong style='color: #dc3545;'>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</strong></p>
            </div>
            <button onclick='window.history.back()' style='background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer;'>戻る</button>
        </div>
        ";
        error_log('Payment Error: ' . $e->getMessage());
    }
}
?>

</body>
</html>
