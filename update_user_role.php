<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if ($argc < 2) {
    echo "Usage: php update_user_role.php user_email [new_role]\n";
    echo "Example: php update_user_role.php agent@example.com admin\n";
    exit(1);
}

$email = $argv[1];
$newRole = $argv[2] ?? 'admin';

$user = User::where('email', $email)->first();
if (! $user) {
    echo "Utilisateur introuvable pour l'email : {$email}\n";
    exit(1);
}

$user->role = $newRole;
$user->save();

echo "Rôle mis à jour pour {$user->email} : {$newRole}\n";
exit(0);
