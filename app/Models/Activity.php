<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    public const UNIT_METER = 'Meter';

    public const UNIT_CUBIC_METER = 'm³';

    public const UNIT_HECTARE = 'Ha';

    protected $fillable = [
        'code',
        'name',
        'default_production_unit',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function productionUnits(): array
    {
        return [
            self::UNIT_METER => 'Meter',
            self::UNIT_CUBIC_METER => 'm³',
            self::UNIT_HECTARE => 'Ha',
        ];
    }
}
