<?php

return [
    'app_key' => env('JPush_APP_KEY'),
    'master_secret' => env('JPush_MASTER_SECRET'),
    'default_log_file' => storage_path('/logs/jpush.log')
];
