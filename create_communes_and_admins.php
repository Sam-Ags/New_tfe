<?php

declare(strict_types=1);

use App\Models\Commune;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$items = [
    [
        'commune' => ['name' => 'Cotonou', 'department' => 'Littoral', 'latitude' => 6.3703, 'longitude' => 2.3912],
        'admin' => ['email' => 'admin.cotonou@smartcity.test', 'name' => 'Admin Cotonou', 'phone' => '+22960000001'],
    ],
    [
        'commune' => ['name' => 'Lokossa', 'department' => 'Mono'],
        'admin' => ['email' => 'admin.lokossa@smartcity.test', 'name' => 'Admin Lokossa', 'phone' => '+22960000002'],
    ],
    [
        'commune' => ['name' => 'Abomey-Calavi', 'department' => 'Atlantique', 'latitude' => 6.4485, 'longitude' => 2.3557],
        'admin' => ['email' => 'admin.calavi@smartcity.test', 'name' => 'Admin Abomey-Calavi', 'phone' => '+22960000003'],
    ],
    [
        'commune' => ['name' => 'Porto-Novo', 'department' => 'Oueme', 'latitude' => 6.4969, 'longitude' => 2.6289],
        'admin' => ['email' => 'admin.porto-novo@smartcity.test', 'name' => 'Admin Porto-Novo', 'phone' => '+22960000004'],
    ],
    [
        'commune' => ['name' => 'Bohicon', 'department' => 'Zou'],
        'admin' => ['email' => 'admin.bohicon@smartcity.test', 'name' => 'Admin Bohicon', 'phone' => '+22960000005'],
    ],
];

foreach ($items as $item) {
    $communeData = $item['commune'];
    $adminData = $item['admin'];

    $commune = Commune::updateOrCreate(
        ['name' => $communeData['name']],
        [
            'department' => $communeData['department'],
            'latitude' => $communeData['latitude'] ?? null,
            'longitude' => $communeData['longitude'] ?? null,
        ]
    );

    echo "Commune créée/mise à jour : {$commune->name} ({$commune->department})\n";

    $user = User::updateOrCreate(
        ['email' => $adminData['email']],
        [
            'name' => $adminData['name'],
            'phone' => $adminData['phone'],
            'commune_id' => $commune->id,
            'department' => $commune->department,
            'password' => Hash::make('12345'),
            'role' => 'admin',
        ]
    );

    echo "Admin créé/mis à jour : {$user->email} (mot de passe par défaut : 12345)\n";
}

exit(0);
