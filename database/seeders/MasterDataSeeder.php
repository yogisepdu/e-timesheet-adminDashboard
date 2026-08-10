<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Contractor;
use App\Models\EquipmentUnit;
use App\Models\Operator;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Contractor
        |--------------------------------------------------------------------------
        */

        $internal = Contractor::query()->updateOrCreate(
            [
                'code' => 'INT',
            ],
            [
                'name' => 'Internal',
                'type' => Contractor::TYPE_INTERNAL,
                'description' => 'Tenaga kerja dan unit milik perusahaan.',
                'is_active' => true,
            ],
        );

        Contractor::query()->updateOrCreate(
            [
                'code' => 'EXT',
            ],
            [
                'name' => 'External',
                'type' => Contractor::TYPE_EXTERNAL,
                'description' => 'Tenaga kerja atau unit milik kontraktor.',
                'is_active' => true,
            ],
        );

        /*
        |--------------------------------------------------------------------------
        | Operator
        |--------------------------------------------------------------------------
        */

        $operators = [
            [
                'code' => 'OP001',
                'name' => 'Wak Heri',
            ],
            [
                'code' => 'OP002',
                'name' => 'Budi Santoso',
            ],
            [
                'code' => 'OP003',
                'name' => 'Andi Saputra',
            ],
            [
                'code' => 'OP004',
                'name' => 'Rahmat Hidayat',
            ],
            [
                'code' => 'OP005',
                'name' => 'M. Ridwan',
            ],
        ];

        foreach ($operators as $operator) {
            Operator::query()->updateOrCreate(
                [
                    'code' => $operator['code'],
                ],
                [
                    'contractor_id' => $internal->id,
                    'name' => $operator['name'],
                    'position' => 'Operator',
                    'is_active' => true,
                ],
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Equipment Unit
        |--------------------------------------------------------------------------
        */

        $equipmentUnits = [
            [
                'code' => 'SPD16',
                'equipment_type' => 'SK75',
            ],
            [
                'code' => 'EXC-001',
                'equipment_type' => 'Excavator Komatsu PC200',
                'brand' => 'Komatsu',
                'model' => 'PC200',
            ],
            [
                'code' => 'EXC-002',
                'equipment_type' => 'Excavator Hitachi ZX200',
                'brand' => 'Hitachi',
                'model' => 'ZX200',
            ],
            [
                'code' => 'DZ-003',
                'equipment_type' => 'Bulldozer Komatsu D85',
                'brand' => 'Komatsu',
                'model' => 'D85',
            ],
            [
                'code' => 'DT-012',
                'equipment_type' => 'Dump Truck Hino 500',
                'brand' => 'Hino',
                'model' => '500',
            ],
            [
                'code' => 'GD-002',
                'equipment_type' => 'Motor Grader Komatsu GD535',
                'brand' => 'Komatsu',
                'model' => 'GD535',
            ],
            [
                'code' => 'WL-004',
                'equipment_type' => 'Wheel Loader WA200',
                'brand' => 'Komatsu',
                'model' => 'WA200',
            ],
        ];

        foreach ($equipmentUnits as $unit) {
            EquipmentUnit::query()->updateOrCreate(
                [
                    'code' => $unit['code'],
                ],
                [
                    'contractor_id' => $internal->id,
                    'equipment_type' => $unit['equipment_type'],
                    'brand' => $unit['brand'] ?? null,
                    'model' => $unit['model'] ?? null,
                    'is_active' => true,
                ],
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Activities
        |--------------------------------------------------------------------------
        */

        $activities = [
            [
                'code' => 'ACT001',
                'name' => 'MCH S. Shaving',
                'unit' => Activity::UNIT_HECTARE,
            ],
            [
                'code' => 'ACT002',
                'name' => 'Land Clearing',
                'unit' => Activity::UNIT_HECTARE,
            ],
            [
                'code' => 'ACT003',
                'name' => 'Chipping',
                'unit' => Activity::UNIT_HECTARE,
            ],
            [
                'code' => 'ACT004',
                'name' => 'Hauling',
                'unit' => Activity::UNIT_CUBIC_METER,
            ],
            [
                'code' => 'ACT005',
                'name' => 'Grading',
                'unit' => Activity::UNIT_METER,
            ],
            [
                'code' => 'ACT006',
                'name' => 'Excavation',
                'unit' => Activity::UNIT_CUBIC_METER,
            ],
            [
                'code' => 'ACT007',
                'name' => 'Road Maintenance',
                'unit' => Activity::UNIT_METER,
            ],
            [
                'code' => 'ACT008',
                'name' => 'Loading Material',
                'unit' => Activity::UNIT_CUBIC_METER,
            ],
            [
                'code' => 'ACT009',
                'name' => 'Unloading Material',
                'unit' => Activity::UNIT_CUBIC_METER,
            ],
            [
                'code' => 'ACT010',
                'name' => 'Pembuatan Parit',
                'unit' => Activity::UNIT_METER,
            ],
        ];

        foreach ($activities as $activity) {
            Activity::query()->updateOrCreate(
                [
                    'code' => $activity['code'],
                ],
                [
                    'name' => $activity['name'],
                    'default_production_unit' => $activity['unit'],
                    'is_active' => true,
                ],
            );
        }
    }
}
