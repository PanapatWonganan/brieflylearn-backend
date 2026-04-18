<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pay Solutions (ThaiePay) Payment Gateway
    |--------------------------------------------------------------------------
    | Docs: https://payso.co/developer/developer.html
    | Hosted form endpoint (credit card + PromptPay + mobile banking).
    */

    'merchant_id' => env('PAYSOLUTIONS_MERCHANT_ID', ''),
    'api_key' => env('PAYSOLUTIONS_API_KEY', ''),
    'secret_key' => env('PAYSOLUTIONS_SECRET_KEY', ''),

    // 'production' | 'sandbox'
    'env' => env('PAYSOLUTIONS_ENV', 'production'),

    // Hosted payment page (merchant posts form here, user completes payment)
    'hosted_url' => env(
        'PAYSOLUTIONS_HOSTED_URL',
        'https://www.thaiepay.com/epaylink/payment.aspx'
    ),

    // Server-to-server order inquiry
    'inquiry_url' => env(
        'PAYSOLUTIONS_INQUIRY_URL',
        'https://apis.paysolutions.asia/order/orderdetailpost'
    ),

    // Currency: "00"=THB, "01"=USD (we only use THB)
    'currency' => env('PAYSOLUTIONS_CURRENCY', '00'),

    // Language on hosted page: TH | EN
    'lang' => env('PAYSOLUTIONS_LANG', 'TH'),

    // Return URL user's browser is redirected to after payment.
    // IMPORTANT: must be the BACKEND endpoint so we can verify via inquiry API
    // and then redirect the browser to the frontend success/failed page.
    // Set this in the Pay Solutions merchant portal.
    'return_url' => env(
        'PAYSOLUTIONS_RETURN_URL',
        env('APP_URL', 'https://api.brieflylearn.com') . '/api/v1/payments/paysolutions/return'
    ),

    // Server-to-server postback (configured in Pay Solutions merchant portal).
    'postback_url' => env(
        'PAYSOLUTIONS_POSTBACK_URL',
        env('APP_URL', 'https://api.brieflylearn.com') . '/api/v1/payments/paysolutions/postback'
    ),
];
