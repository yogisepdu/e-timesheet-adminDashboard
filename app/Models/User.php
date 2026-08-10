<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_ATASAN = 'atasan';
    public const ROLE_PENGAWAS = 'pengawas';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'position',
        'phone',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        return $this->is_active
            && in_array(
                $this->role,
                [
                    self::ROLE_ADMIN,
                    self::ROLE_ATASAN,
                ],
                true,
            );
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAtasan(): bool
    {
        return $this->role === self::ROLE_ATASAN;
    }

    public function isPengawas(): bool
    {
        return $this->role === self::ROLE_PENGAWAS;
    }

    public function canReviewTimeSheets(): bool
    {
        return $this->is_active
            && ($this->isAdmin() || $this->isAtasan());
    }
}
