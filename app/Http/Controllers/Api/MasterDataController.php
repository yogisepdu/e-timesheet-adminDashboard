<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Contractor;
use App\Models\EquipmentUnit;
use App\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Seluruh master data untuk form Time Sheet.
     *
     * Endpoint ini direkomendasikan untuk aplikasi Android
     * karena hanya membutuhkan satu request.
     */
    public function index(): JsonResponse
    {
        $contractors = Contractor::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn(Contractor $contractor): array => [
                'id' => $contractor->id,
                'code' => $contractor->code,
                'name' => $contractor->name,
                'type' => $contractor->type,
            ]);

        $operators = Operator::query()
            ->with('contractor:id,code,name,type')
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn(Operator $operator): array => [
                'id' => $operator->id,
                'contractor_id' => $operator->contractor_id,
                'code' => $operator->code,
                'name' => $operator->name,
                'display_name' => "{$operator->code} - {$operator->name}",

                'contractor' => $operator->contractor
                    ? [
                        'id' => $operator->contractor->id,
                        'code' => $operator->contractor->code,
                        'name' => $operator->contractor->name,
                        'type' => $operator->contractor->type,
                    ]
                    : null,
            ]);

        $equipmentUnits = EquipmentUnit::query()
            ->with('contractor:id,code,name,type')
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn(EquipmentUnit $unit): array => [
                'id' => $unit->id,
                'contractor_id' => $unit->contractor_id,
                'code' => $unit->code,
                'equipment_type' => $unit->equipment_type,
                'brand' => $unit->brand,
                'model' => $unit->model,
                'display_name' => "{$unit->code} - {$unit->equipment_type}",

                'contractor' => $unit->contractor
                    ? [
                        'id' => $unit->contractor->id,
                        'code' => $unit->contractor->code,
                        'name' => $unit->contractor->name,
                        'type' => $unit->contractor->type,
                    ]
                    : null,
            ]);

        $activities = Activity::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn(Activity $activity): array => [
                'id' => $activity->id,
                'code' => $activity->code,
                'name' => $activity->name,
                'default_production_unit' =>
                $activity->default_production_unit,
            ]);

        return response()->json([
            'data' => [
                'contractors' => $contractors,
                'operators' => $operators,
                'equipment_units' => $equipmentUnits,
                'activities' => $activities,

                'production_units' => [
                    [
                        'value' => Activity::UNIT_METER,
                        'label' => 'Meter',
                    ],
                    [
                        'value' => Activity::UNIT_CUBIC_METER,
                        'label' => 'm³',
                    ],
                    [
                        'value' => Activity::UNIT_HECTARE,
                        'label' => 'Ha',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Kontraktor aktif.
     */
    public function contractors(): JsonResponse
    {
        $contractors = Contractor::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'type',
            ]);

        return response()->json([
            'data' => $contractors,
        ]);
    }

    /**
     * Operator aktif.
     *
     * Bisa difilter menggunakan:
     * ?contractor_id=1
     */
    public function operators(Request $request): JsonResponse
    {
        $query = Operator::query()
            ->with('contractor:id,code,name,type')
            ->where('is_active', true);

        if ($request->filled('contractor_id')) {
            $query->where(
                'contractor_id',
                $request->integer('contractor_id'),
            );
        }

        $operators = $query
            ->orderBy('code')
            ->get()
            ->map(fn(Operator $operator): array => [
                'id' => $operator->id,
                'contractor_id' => $operator->contractor_id,
                'code' => $operator->code,
                'name' => $operator->name,
                'display_name' => "{$operator->code} - {$operator->name}",

                'contractor' => $operator->contractor
                    ? [
                        'id' => $operator->contractor->id,
                        'code' => $operator->contractor->code,
                        'name' => $operator->contractor->name,
                        'type' => $operator->contractor->type,
                    ]
                    : null,
            ]);

        return response()->json([
            'data' => $operators,
        ]);
    }

    /**
     * Unit alat aktif.
     *
     * Bisa difilter:
     * ?contractor_id=1
     */
    public function equipmentUnits(Request $request): JsonResponse
    {
        $query = EquipmentUnit::query()
            ->with('contractor:id,code,name,type')
            ->where('is_active', true);

        if ($request->filled('contractor_id')) {
            $query->where(
                'contractor_id',
                $request->integer('contractor_id'),
            );
        }

        $units = $query
            ->orderBy('code')
            ->get()
            ->map(fn(EquipmentUnit $unit): array => [
                'id' => $unit->id,
                'contractor_id' => $unit->contractor_id,
                'code' => $unit->code,
                'equipment_type' => $unit->equipment_type,
                'brand' => $unit->brand,
                'model' => $unit->model,
                'display_name' => "{$unit->code} - {$unit->equipment_type}",

                'contractor' => $unit->contractor
                    ? [
                        'id' => $unit->contractor->id,
                        'code' => $unit->contractor->code,
                        'name' => $unit->contractor->name,
                        'type' => $unit->contractor->type,
                    ]
                    : null,
            ]);

        return response()->json([
            'data' => $units,
        ]);
    }

    /**
     * Kegiatan aktif.
     */
    public function activities(): JsonResponse
    {
        $activities = Activity::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get([
                'id',
                'code',
                'name',
                'default_production_unit',
            ]);

        return response()->json([
            'data' => $activities,
        ]);
    }
}
