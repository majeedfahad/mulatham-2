<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | Your Telegram bot token from @BotFather
    |
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Default Chat ID
    |--------------------------------------------------------------------------
    |
    | The default Telegram chat/group ID for notifications
    |
    */

    'chat_id' => env('TELEGRAM_CHAT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Enable Notifications
    |--------------------------------------------------------------------------
    |
    | Enable or disable Telegram notifications globally
    |
    */

    'enabled' => env('TELEGRAM_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Sentry Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Secret token to verify Sentry webhook requests
    |
    */

    'sentry_webhook_secret' => env('TELEGRAM_SENTRY_WEBHOOK_SECRET'),

];
