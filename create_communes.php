<?php

declare(strict_types=1);

use App\Models\Commune;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$communes = [
    ['name' => 'Cotonou', 'department' => 'Littoral', 'latitude' => 6.3703, 'longitude' => 2.3912],
    ['name' => 'Lokossa', 'department' => 'Mono'],
    ['name' => 'Abomey-Calavi', 'department' => 'Atlantique', 'latitude' => 6.4485, 'longitude' => 2.3557],
    ['name' => 'Porto-Novo', 'department' => 'Oueme', 'latitude' => 6.4969, 'longitude' => 2.6289],
    ['name' => 'Bohicon', 'department' => 'Zou'],
];

foreach ($communes as $data) {
    $commune = Commune::updateOrCreate(
        ['name' => $data['name']],
        [
            'department' => $data['department'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]
    );
    echo "Commune créée/mise à jour : {$commune->name} ({$commune->department})\n";
}

$lokossa = Commune::where('name', 'Lokossa')->first();

if ($lokossa) {
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
    echo "Identifiant créé/mis à jour : {$user->email} (Lokossa)\n";
} else {
    echo "Commune Lokossa introuvable, identifiant non créé.\n";
}

exit(0);
