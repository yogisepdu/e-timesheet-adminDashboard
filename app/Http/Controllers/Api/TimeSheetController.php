<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\EquipmentUnit;
use App\Models\Operator;
use App\Models\TimeSheet;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TimeSheetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => [
                'nullable',
                Rule::in([
                    TimeSheet::STATUS_DRAFT,
                    TimeSheet::STATUS_SUBMITTED,
                    TimeSheet::STATUS_APPROVED,
                    TimeSheet::STATUS_REVISION,
                    TimeSheet::STATUS_REJECTED,
                ]),
            ],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));

        $timeSheets = TimeSheet::query()
            ->with([
                'operator:id,code,name',
                'equipmentUnit:id,code,equipment_type',
                'activity:id,code,name',
            ])
            ->where('user_id', $request->user()->id)
            ->when(
                filled($validated['status'] ?? null),
                fn(Builder $query) => $query->where(
                    'status',
                    $validated['status'],
                ),
            )
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(function (Builder $query) use ($search): void {
                        $query
                            ->where('code', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%")
                            ->orWhereHas(
                                'operator',
                                fn(Builder $operatorQuery) => $operatorQuery
                                    ->where('code', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%"),
                            )
                            ->orWhereHas(
                                'equipmentUnit',
                                fn(Builder $unitQuery) => $unitQuery
                                    ->where('code', 'like', "%{$search}%")
                                    ->orWhere(
                                        'equipment_type',
                                        'like',
                                        "%{$search}%",
                                    ),
                            )
                            ->orWhereHas(
                                'activity',
                                fn(Builder $activityQuery) => $activityQuery
                                    ->where('name', 'like', "%{$search}%"),
                            );
                    });
                },
            )
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn(TimeSheet $timeSheet): array => $this->historyItem(
                $timeSheet,
            ));

        return response()->json([
            'data' => $timeSheets,
        ]);
    }

    public function show(
        Request $request,
        TimeSheet $timeSheet,
    ): JsonResponse {
        $this->ensureOwner($request, $timeSheet);

        $timeSheet->load([
            'contractor:id,code,name,type',
            'operator:id,code,name',
            'equipmentUnit:id,code,equipment_type,brand,model',
            'activity:id,code,name',
        ]);

        return response()->json([
            'data' => [
                'id' => $timeSheet->id,
                'code' => $timeSheet->code,
                'work_date' => $timeSheet->work_date?->format('Y-m-d'),
                'status' => $timeSheet->status,

                'contractor' => $timeSheet->contractor,
                'operator' => $timeSheet->operator,
                'equipment_unit' => $timeSheet->equipmentUnit,
                'activity' => $timeSheet->activity,

                'start_time' => $this->formatTime($timeSheet->start_time),
                'end_time' => $this->formatTime($timeSheet->end_time),
                'total_hours' => $timeSheet->total_hours,

                'hm_start' => $timeSheet->hm_start,
                'hm_end' => $timeSheet->hm_end,
                'total_hm' => $timeSheet->total_hm,

                'fuel_used' => $timeSheet->fuel_used,
                'location' => $timeSheet->location,

                'production' => $timeSheet->production,
                'production_unit' => $timeSheet->production_unit,

                'notes' => $timeSheet->notes,
                'review_notes' => $timeSheet->review_notes,

                'submitted_at' =>
                $timeSheet->submitted_at?->toISOString(),
                'approved_at' =>
                $timeSheet->approved_at?->toISOString(),
                'revision_requested_at' =>
                $timeSheet->revision_requested_at?->toISOString(),
                'rejected_at' =>
                $timeSheet->rejected_at?->toISOString(),
                'created_at' =>
                $timeSheet->created_at?->toISOString(),
            ],
        ]);
    }

    public function storeDraft(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request, false);
        $values = $this->prepareValues($validated, false);

        $timeSheet = DB::transaction(function () use (
            $request,
            $values,
        ): TimeSheet {
            $timeSheet = TimeSheet::query()->create([
                ...$values,
                'code' => 'TMP-' . Str::uuid(),
                'user_id' => $request->user()->id,
                'status' => TimeSheet::STATUS_DRAFT,
            ]);

            $timeSheet->forceFill([
                'code' => $this->makeCode($timeSheet),
            ])->save();

            return $timeSheet;
        });

        return response()->json([
            'message' => 'Draft Time Sheet berhasil disimpan.',
            'data' => $this->savedItem($timeSheet),
        ], 201);
    }

    public function updateDraft(
        Request $request,
        TimeSheet $timeSheet,
    ): JsonResponse {
        $this->ensureOwner($request, $timeSheet);

        if ($timeSheet->status !== TimeSheet::STATUS_DRAFT) {
            throw ValidationException::withMessages([
                'time_sheet' => [
                    'Hanya Time Sheet berstatus draft yang dapat diperbarui melalui form ini.',
                ],
            ]);
        }

        $validated = $this->validatePayload($request, false);
        $values = $this->prepareValues($validated, false);

        $timeSheet->fill($values)->save();

        return response()->json([
            'message' => 'Draft Time Sheet berhasil diperbarui.',
            'data' => $this->savedItem($timeSheet),
        ]);
    }

    public function storeAndSubmit(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request, true);
        $values = $this->prepareValues($validated, true);

        $timeSheet = DB::transaction(function () use (
            $request,
            $values,
        ): TimeSheet {
            $timeSheet = TimeSheet::query()->create([
                ...$values,
                'code' => 'TMP-' . Str::uuid(),
                'user_id' => $request->user()->id,
                'status' => TimeSheet::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);

            $timeSheet->forceFill([
                'code' => $this->makeCode($timeSheet),
            ])->save();

            return $timeSheet;
        });

        return response()->json([
            'message' => 'Time Sheet berhasil dikirim.',
            'data' => $this->savedItem($timeSheet),
        ], 201);
    }

    public function submitDraft(
        Request $request,
        TimeSheet $timeSheet,
    ): JsonResponse {
        $this->ensureOwner($request, $timeSheet);

        if (! in_array(
            $timeSheet->status,
            [
                TimeSheet::STATUS_DRAFT,
                TimeSheet::STATUS_REVISION,
            ],
            true,
        )) {
            throw ValidationException::withMessages([
                'time_sheet' => [
                    'Time Sheet ini tidak dapat dikirim ulang pada status saat ini.',
                ],
            ]);
        }

        $validated = $this->validatePayload($request, true);
        $values = $this->prepareValues($validated, true);

        $timeSheet->fill([
            ...$values,
            'status' => TimeSheet::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'revision_requested_at' => null,
            'review_notes' => null,
        ])->save();

        return response()->json([
            'message' => 'Time Sheet berhasil dikirim.',
            'data' => $this->savedItem($timeSheet),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(
        Request $request,
        bool $forSubmit,
    ): array {
        $required = $forSubmit ? 'required' : 'nullable';

        $validated = $request->validate([
            'work_date' => [$required, 'date_format:Y-m-d'],

            'contractor_id' => [
                $required,
                'integer',
                Rule::exists('contractors', 'id')
                    ->where('is_active', true),
            ],

            'operator_id' => [
                $required,
                'integer',
                Rule::exists('operators', 'id')
                    ->where('is_active', true),
            ],

            'equipment_unit_id' => [
                $required,
                'integer',
                Rule::exists('equipment_units', 'id')
                    ->where('is_active', true),
            ],

            'activity_id' => [
                $required,
                'integer',
                Rule::exists('activities', 'id')
                    ->where('is_active', true),
            ],

            'start_time' => [$required, 'date_format:H:i'],
            'end_time' => [$required, 'date_format:H:i'],

            'hm_start' => [$required, 'numeric', 'min:0'],
            'hm_end' => [$required, 'numeric', 'min:0'],

            'fuel_used' => ['nullable', 'numeric', 'min:0'],

            'location' => [$required, 'string', 'max:255'],

            'production' => ['nullable', 'numeric', 'min:0'],

            'production_unit' => [
                'nullable',
                Rule::in([
                    Activity::UNIT_METER,
                    Activity::UNIT_CUBIC_METER,
                    Activity::UNIT_HECTARE,
                ]),
            ],
        ]);

        $this->validateRelationships($validated, $forSubmit);

        return $validated;
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function validateRelationships(
        array $validated,
        bool $forSubmit,
    ): void {
        $contractorId = $validated['contractor_id'] ?? null;
        $operatorId = $validated['operator_id'] ?? null;
        $equipmentUnitId = $validated['equipment_unit_id'] ?? null;

        if ($contractorId && $operatorId) {
            $operator = Operator::query()->find($operatorId);

            if (
                $operator &&
                $operator->contractor_id !== null &&
                (int) $operator->contractor_id !== (int) $contractorId
            ) {
                throw ValidationException::withMessages([
                    'operator_id' => [
                        'Operator tidak sesuai dengan kontraktor yang dipilih.',
                    ],
                ]);
            }
        }

        if ($contractorId && $equipmentUnitId) {
            $unit = EquipmentUnit::query()->find($equipmentUnitId);

            if (
                $unit &&
                $unit->contractor_id !== null &&
                (int) $unit->contractor_id !== (int) $contractorId
            ) {
                throw ValidationException::withMessages([
                    'equipment_unit_id' => [
                        'Unit alat tidak sesuai dengan kontraktor yang dipilih.',
                    ],
                ]);
            }
        }

        if (
            isset($validated['hm_start'], $validated['hm_end']) &&
            (float) $validated['hm_end'] < (float) $validated['hm_start']
        ) {
            throw ValidationException::withMessages([
                'hm_end' => [
                    'HM akhir tidak boleh lebih kecil dari HM awal.',
                ],
            ]);
        }

        if (
            ! empty($validated['start_time']) &&
            ! empty($validated['end_time']) &&
            $validated['start_time'] === $validated['end_time']
        ) {
            throw ValidationException::withMessages([
                'end_time' => [
                    'Jam selesai tidak boleh sama dengan jam mulai.',
                ],
            ]);
        }

        if (
            $forSubmit &&
            array_key_exists('production', $validated) &&
            $validated['production'] !== null &&
            empty($validated['production_unit'])
        ) {
            throw ValidationException::withMessages([
                'production_unit' => [
                    'Satuan produksi wajib dipilih jika jumlah produksi diisi.',
                ],
            ]);
        }
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function prepareValues(
        array $validated,
        bool $forSubmit,
    ): array {
        $totalHours = null;

        if (
            ! empty($validated['start_time']) &&
            ! empty($validated['end_time'])
        ) {
            $start = CarbonImmutable::createFromFormat(
                'H:i',
                $validated['start_time'],
            );

            $end = CarbonImmutable::createFromFormat(
                'H:i',
                $validated['end_time'],
            );

            if ($end->lessThan($start)) {
                $end = $end->addDay();
            }

            $totalHours = round(
                $start->diffInMinutes($end) / 60,
                2,
            );
        }

        $totalHm = null;

        if (
            isset($validated['hm_start'], $validated['hm_end']) &&
            $validated['hm_start'] !== null &&
            $validated['hm_end'] !== null
        ) {
            $totalHm = round(
                (float) $validated['hm_end'] -
                    (float) $validated['hm_start'],
                2,
            );
        }

        return [
            'work_date' => $validated['work_date'] ?? null,
            'contractor_id' => $validated['contractor_id'] ?? null,
            'operator_id' => $validated['operator_id'] ?? null,
            'equipment_unit_id' =>
            $validated['equipment_unit_id'] ?? null,
            'activity_id' => $validated['activity_id'] ?? null,

            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'total_hours' => $totalHours,

            'hm_start' => $validated['hm_start'] ?? null,
            'hm_end' => $validated['hm_end'] ?? null,
            'total_hm' => $totalHm,

            'fuel_used' => $validated['fuel_used'] ?? null,
            'location' => $validated['location'] ?? null,

            'production' => $validated['production'] ?? null,
            'production_unit' =>
            $validated['production_unit'] ?? null,
        ];
    }

    private function ensureOwner(
        Request $request,
        TimeSheet $timeSheet,
    ): void {
        abort_unless(
            (int) $timeSheet->user_id ===
                (int) $request->user()->id,
            404,
        );
    }

    private function makeCode(TimeSheet $timeSheet): string
    {
        $date = $timeSheet->work_date?->format('Ymd')
            ?? $timeSheet->created_at?->format('Ymd')
            ?? now()->format('Ymd');

        return sprintf(
            'TS-%s-%04d',
            $date,
            $timeSheet->id,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function savedItem(TimeSheet $timeSheet): array
    {
        return [
            'id' => $timeSheet->id,
            'code' => $timeSheet->code,
            'status' => $timeSheet->status,
            'work_date' => $timeSheet->work_date?->format('Y-m-d'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function historyItem(TimeSheet $timeSheet): array
    {
        return [
            'id' => $timeSheet->id,
            'code' => $timeSheet->code,
            'work_date' => $timeSheet->work_date?->format('Y-m-d'),
            'status' => $timeSheet->status,
            'location' => $timeSheet->location,
            'start_time' => $this->formatTime($timeSheet->start_time),
            'end_time' => $this->formatTime($timeSheet->end_time),
            'total_hours' => $timeSheet->total_hours,
            'total_hm' => $timeSheet->total_hm,
            'fuel_used' => $timeSheet->fuel_used,
            'production' => $timeSheet->production,
            'production_unit' => $timeSheet->production_unit,
            'review_notes' => $timeSheet->review_notes,

            'operator' => $timeSheet->operator
                ? [
                    'id' => $timeSheet->operator->id,
                    'code' => $timeSheet->operator->code,
                    'name' => $timeSheet->operator->name,
                ]
                : null,

            'equipment_unit' => $timeSheet->equipmentUnit
                ? [
                    'id' => $timeSheet->equipmentUnit->id,
                    'code' => $timeSheet->equipmentUnit->code,
                    'equipment_type' =>
                    $timeSheet->equipmentUnit->equipment_type,
                ]
                : null,

            'activity' => $timeSheet->activity
                ? [
                    'id' => $timeSheet->activity->id,
                    'code' => $timeSheet->activity->code,
                    'name' => $timeSheet->activity->name,
                ]
                : null,

            'created_at' => $timeSheet->created_at?->toISOString(),
            'submitted_at' =>
            $timeSheet->submitted_at?->toISOString(),
            'approved_at' =>
            $timeSheet->approved_at?->toISOString(),
        ];
    }

    private function formatTime(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return substr($value, 0, 5);
    }
}
