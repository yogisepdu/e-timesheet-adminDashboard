<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contractor extends Model
{
    use HasFactory;

    public const TYPE_INTERNAL = 'internal';

    public const TYPE_EXTERNAL = 'external';

    protected $fillable = [
        'code',
        'name',
        'type',
        'phone',
        'address',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function operators(): HasMany
    {
        return $this->hasMany(Operator::class);
    }

    public function equipmentUnits(): HasMany
    {
        return $this->hasMany(EquipmentUnit::class);
    }

    public function isInternal(): bool
    {
        return $this->type === self::TYPE_INTERNAL;
    }

    public function isExternal(): bool
    {
        return $this->type === self::TYPE_EXTERNAL;
    }
}
