<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'dechets' => 'Déchets',
        'route' => 'Route dégradée',
        'eclairage' => 'Éclairage public',
        'inondation' => 'Inondation',
        'securite' => 'Sécurité',
        'autre' => 'Autre',
    ];

    public const STATUSES = [
        'en_attente' => 'En attente',
        'en_cours' => 'En cours',
        'resolu' => 'Résolu',
    ];

    public const PRIORITIES = [
        'faible' => 'Faible',
        'moyenne' => 'Moyenne',
        'elevee' => 'Élevée',
    ];

    public const TITLE_OPTIONS = [
        'Route dégradée',
        'Trou sur la voie',
        'Tas d’ordures',
        'Caniveau bouché',
        'Lampadaire en panne',
        'Début d’inondation',
        'Eau stagnante',
        'Arbre ou obstacle sur la route',
        'Problème de sécurité',
        'Autre incident urbain',
    ];

    protected $fillable = [
        'citizen_name',
        'user_id',
        'commune_id',
        'citizen_phone',
        'title',
        'category',
        'district',
        'description',
        'latitude',
        'longitude',
        'urgency',
        'priority',
        'status',
        'assigned_to',
        'assigned_agent_id',
        'photo_path',
        'taken_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'taken_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public static function calculatePriority(string $category, string $urgency, string $district): string
    {
        $score = match ($category) {
            'inondation', 'securite' => 3,
            'route' => 2,
            'eclairage', 'dechets' => 1,
            default => 0,
        };

        $score += match ($urgency) {
            'critique' => 3,
            'urgent' => 2,
            default => 0,
        };

        if (str_contains(strtolower($district), 'centre')) {
            $score += 1;
        }

        return match (true) {
            $score >= 5 => 'elevee',
            $score >= 2 => 'moyenne',
            default => 'faible',
        };
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function dashboardUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admin_incidents')->withTimestamps();
    }

    public function urbanNotifications(): HasMany
    {
        return $this->hasMany(UrbanNotification::class);
    }
}
