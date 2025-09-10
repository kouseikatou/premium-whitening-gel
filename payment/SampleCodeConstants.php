<?php
/**
 * SampleCodeConstants
 * 
 * This file now uses credentials from config.php for better security
 * and centralized configuration management.
 */

// Include config.php if not already included
if (!defined('AUTHORIZENET_LOGIN_ID')) {
    require_once __DIR__ . '/config.php';
}

class SampleCodeConstants
{
    // Authorize.Net Credentials (now loaded from config.php)
    const MERCHANT_LOGIN_ID = AUTHORIZENET_LOGIN_ID;
    const MERCHANT_TRANSACTION_KEY = AUTHORIZENET_TRANSACTION_KEY;
    
    /**
     * Get merchant login ID
     * @return string
     */
    public static function getMerchantLoginId()
    {
        return defined('AUTHORIZENET_LOGIN_ID') ? AUTHORIZENET_LOGIN_ID : self::MERCHANT_LOGIN_ID;
    }
    
    /**
     * Get merchant transaction key
     * @return string
     */
    public static function getMerchantTransactionKey()
    {
        return defined('AUTHORIZENET_TRANSACTION_KEY') ? AUTHORIZENET_TRANSACTION_KEY : self::MERCHANT_TRANSACTION_KEY;
    }
}
?>