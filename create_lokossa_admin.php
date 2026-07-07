<?php

declare(strict_types=1);

use App\Models\Commune;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$lokossa = Commune::firstOrCreate(
    ['name' => 'Lokossa'],
    ['department' => 'Mono']
);

$user = User::updateOrCreate(
    ['email' => 'mssougo2@gmail.com'],
    [
        'name' => 'Admin Lokossa',
        'phone' => '+22960000000',
        'commune_id' => $lokossa->id,
        'department' => $lokossa->department,
        'password' => Hash::make('12345'),
        'role' => 'admin',
    ]
);

echo "Admin Lokossa créé ou mis à jour : {$user->email}\n";
exit(0);
