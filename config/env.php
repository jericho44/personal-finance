<?php

return [
    'midtrans_base_url' => env('MIDTRANS_BASE_URL'),
    'midtrans_merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'midtrans_client_id' => env('MIDTRANS_CLIENT_ID'),
    'midtrans_server_key' => env('MIDTRANS_SERVER_KEY'),
    'midtrans_server_password' => env('MIDTRANS_SERVER_PASSWORD', ''),

    // https://docs.midtrans.com/reference/override-notification-url
    'midtrans_append_notification_url' => env('MIDTRANS_APPEND_NOTIFICATION_URL', ''),
    'midtrans_override_notification_url' => env('MIDTRANS_OVERRIDE_NOTIFICATION_URL', ''),

    'fcm_service_account_path' => env('FCM_SERVICE_ACCOUNT_PATH', null),
    'fcm_pending_notification_expired' => env('FCM_PENDING_NOTIFICATION_EXPIRED', 4320),

    'user_2fa_enabled' => env('USER_2FA_ENABLED', false),
    'user_2fa_token_expired' => env('USER_2FA_TOKEN_EXPIRED', 60), // menit (default: 60 menit)
    'user_2fa_otp_expired' => env('USER_2FA_OTP_EXPIRED', 5), // menit (default: 5 menit)
];
