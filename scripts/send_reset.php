<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

$email = 'admin@banco.ao';

$status = Password::sendResetLink(['email' => $email]);

Log::info('Password reset status: ' . $status);

echo "Status: $status\n";
