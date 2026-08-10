<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSheetReview extends Model
{
    use HasFactory;

    public const ACTION_APPROVED = 'approved';
    public const ACTION_REVISION = 'revision';
    public const ACTION_REJECTED = 'rejected';

    protected $fillable = [
        'time_sheet_id',
        'reviewer_id',
        'action',
        'previous_status',
        'new_status',
        'notes',
    ];

    public function timeSheet(): BelongsTo
    {
        return $this->belongsTo(TimeSheet::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewer_id',
        );
    }
}
