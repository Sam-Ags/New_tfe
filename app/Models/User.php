<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'phone', 'npi', 'profile_photo_path', 'password', 'role', 'commune_id', 'department', 'address', 'sex', 'created_by_user_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function assignedIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigned_agent_id');
    }

    public function dashboardIncidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'admin_incidents')->withTimestamps();
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function urbanNotifications(): HasMany
    {
        return $this->hasMany(UrbanNotification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function canManageIncidents(): bool
    {
        return in_array($this->role, ['admin', 'agent'], true);
    }

    public function assignmentLabel(): string
    {
        $details = collect([
            $this->phone ? 'Tel: '.$this->phone : null,
            $this->npi ? 'NPI: '.$this->npi : null,
            $this->email,
        ])->filter()->implode(' | ');

        return $details ? $this->name.' - '.$details : $this->name;
    }

    public function accountIdentifier(): string
    {
        $prefix = match ($this->role) {
            'agent' => 'AGT',
            'admin' => 'ADM',
            default => 'USR',
        };

        return $prefix.'-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
