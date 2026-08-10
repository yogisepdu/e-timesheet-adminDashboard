<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use LogicException;

class TimeSheet extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REVISION = 'revision';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'code',
        'user_id',
        'contractor_id',
        'operator_id',
        'equipment_unit_id',
        'activity_id',
        'work_date',
        'start_time',
        'end_time',
        'total_hours',
        'hm_start',
        'hm_end',
        'total_hm',
        'fuel_used',
        'location',
        'production',
        'production_unit',
        'status',
        'notes',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
        'approved_at',
        'revision_requested_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'total_hours' => 'decimal:2',
            'hm_start' => 'decimal:2',
            'hm_end' => 'decimal:2',
            'total_hm' => 'decimal:2',
            'fuel_used' => 'decimal:2',
            'production' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'revision_requested_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function equipmentUnit(): BelongsTo
    {
        return $this->belongsTo(EquipmentUnit::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by',
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TimeSheetReview::class);
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Menunggu Persetujuan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REVISION => 'Perlu Perbaikan',
            self::STATUS_REJECTED => 'Ditolak',
            default => '-',
        };
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SUBMITTED => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REVISION => 'info',
            self::STATUS_REJECTED => 'danger',
            default => 'gray',
        };
    }

    public function approve(
        User $reviewer,
        ?string $notes = null,
    ): void {
        $this->review(
            reviewer: $reviewer,
            action: TimeSheetReview::ACTION_APPROVED,
            newStatus: self::STATUS_APPROVED,
            notes: $notes,
        );
    }

    public function requestRevision(
        User $reviewer,
        string $notes,
    ): void {
        $this->review(
            reviewer: $reviewer,
            action: TimeSheetReview::ACTION_REVISION,
            newStatus: self::STATUS_REVISION,
            notes: $notes,
        );
    }

    public function reject(
        User $reviewer,
        string $notes,
    ): void {
        $this->review(
            reviewer: $reviewer,
            action: TimeSheetReview::ACTION_REJECTED,
            newStatus: self::STATUS_REJECTED,
            notes: $notes,
        );
    }

    private function review(
        User $reviewer,
        string $action,
        string $newStatus,
        ?string $notes,
    ): void {
        if ($this->status !== self::STATUS_SUBMITTED) {
            throw new LogicException(
                'Hanya Time Sheet yang menunggu persetujuan yang dapat diperiksa.',
            );
        }

        DB::transaction(function () use (
            $reviewer,
            $action,
            $newStatus,
            $notes,
        ): void {
            $locked = self::query()
                ->lockForUpdate()
                ->findOrFail($this->getKey());

            if ($locked->status !== self::STATUS_SUBMITTED) {
                throw new LogicException(
                    'Status Time Sheet telah berubah. Muat ulang halaman dan coba kembali.',
                );
            }

            $previousStatus = $locked->status;
            $reviewedAt = now();

            $locked->forceFill([
                'status' => $newStatus,
                'review_notes' => $notes,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => $reviewedAt,
                'approved_at' =>
                    $newStatus === self::STATUS_APPROVED
                        ? $reviewedAt
                        : null,
                'revision_requested_at' =>
                    $newStatus === self::STATUS_REVISION
                        ? $reviewedAt
                        : null,
                'rejected_at' =>
                    $newStatus === self::STATUS_REJECTED
                        ? $reviewedAt
                        : null,
            ])->save();

            $locked->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'action' => $action,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'notes' => $notes,
            ]);

            $this->setRawAttributes(
                $locked->getAttributes(),
                true,
            );
        });
    }
}
