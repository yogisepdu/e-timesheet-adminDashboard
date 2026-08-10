<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'code',
        'equipment_type',
        'brand',
        'model',
        'registration_number',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->equipment_type}";
    }
}
