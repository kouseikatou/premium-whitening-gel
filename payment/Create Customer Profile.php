<?php
  require 'autoload.php';
  require_once 'SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

function createCustomerProfile($email = null)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    // Create a Customer Profile Request
    //  1. (Optionally) create a Payment Profile
    //  2. (Optionally) create a Shipping Profile
    //  3. Create a Customer Profile (or specify an existing profile)
    //  4. Submit a CreateCustomerProfile Request
    //  5. Validate Profile ID returned

    // Set credit card information for payment profile
    $opaqueData = new AnetAPI\OpaqueDataType();
    $dataDescriptor = $_POST['dataDescriptor'] ?? '';
    $dataValue = $_POST['dataValue'] ?? '';
    $opaqueData->setDataDescriptor($dataDescriptor);
    $opaqueData->setDataValue($dataValue);


    // Add the payment data to a paymentType object
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setOpaqueData($opaqueData);


    // Create the Bill To info for new payment type
    $billTo = new AnetAPI\CustomerAddressType();
    $billTo->setFirstName($_POST['FirstName'] ?? '');
    $billTo->setLastName($_POST['LastName'] ?? '');
    $billTo->setCompany($_POST['Company'] ?? '');
    $billTo->setAddress($_POST['street'] ?? '');
    $billTo->setCity($_POST['city'] ?? '');
    $billTo->setState($_POST['select'] ?? '');
    $billTo->setZip($_POST['zip'] ?? '');
    $billTo->setCountry($_POST['x_ship_to_country'] ?? '');
    $billTo->setPhoneNumber($_POST['contact'] ?? '');
    $billTo->setfaxNumber("");

    // Create a customer shipping address
    $customerShippingAddress = new AnetAPI\CustomerAddressType();
    $customerShippingAddress->setFirstName($_POST['shippingFirstName'] ?? '');
    $customerShippingAddress->setLastName($_POST['shippingLastName'] ?? '');
    $customerShippingAddress->setCompany($_POST['shippingCompany'] ?? '');
    $customerShippingAddress->setAddress($_POST['shippingStreet'] ?? '');
    $customerShippingAddress->setCity($_POST['shippingCity'] ?? '');
    $customerShippingAddress->setState($_POST['shippingState'] ?? '');
    $customerShippingAddress->setZip($_POST['shippingZip'] ?? '');
    $customerShippingAddress->setCountry($_POST['shippingCountry'] ?? '');
    $customerShippingAddress->setPhoneNumber($_POST['shippingPhone'] ?? '');
    $customerShippingAddress->setFaxNumber("");

    // Create an array of any shipping addresses
    $shippingProfiles[] = $customerShippingAddress;


    // Create a new CustomerPaymentProfile object
    $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
    $paymentProfile->setCustomerType('individual');
    $paymentProfile->setBillTo($billTo);
    $paymentProfile->setPayment($paymentOne);
    $paymentProfiles[] = $paymentProfile;


    // Create a new CustomerProfileType and add the payment profile object
    $customerProfile = new AnetAPI\CustomerProfileType();
    $customerProfile->setDescription("PHP payment profile");
    $customerProfile->setMerchantCustomerId("M_" . time());
    $customerProfile->setEmail($email ?? $_POST['mell-contact'] ?? '');
    $customerProfile->setpaymentProfiles($paymentProfiles);
    $customerProfile->setShipToList($shippingProfiles);


    // Assemble the complete transaction request
    $request = new AnetAPI\CreateCustomerProfileRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setProfile($customerProfile);

    // Create the controller and get the response
    $controller = new AnetController\CreateCustomerProfileController($request);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
  
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
        echo "Succesfully created customer profile : " . $response->getCustomerProfileId() . "\n";
        $paymentProfiles = $response->getCustomerPaymentProfileIdList();
        echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
    } else {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
}

if (!defined('DONT_RUN_SAMPLES')) {
    createCustomerProfile("test123@test.com");
}
