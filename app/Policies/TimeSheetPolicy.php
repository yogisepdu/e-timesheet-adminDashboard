<?php

namespace App\Policies;

use App\Models\TimeSheet;
use App\Models\User;

class TimeSheetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canReviewTimeSheets();
    }

    public function view(User $user, TimeSheet $timeSheet): bool
    {
        return $user->canReviewTimeSheets();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, TimeSheet $timeSheet): bool
    {
        return false;
    }

    public function delete(User $user, TimeSheet $timeSheet): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function approve(User $user, TimeSheet $timeSheet): bool
    {
        return $user->canReviewTimeSheets()
            && $timeSheet->status === TimeSheet::STATUS_SUBMITTED;
    }

    public function requestRevision(User $user, TimeSheet $timeSheet): bool
    {
        return $user->canReviewTimeSheets()
            && $timeSheet->status === TimeSheet::STATUS_SUBMITTED;
    }

    public function reject(User $user, TimeSheet $timeSheet): bool
    {
        return $user->canReviewTimeSheets()
            && $timeSheet->status === TimeSheet::STATUS_SUBMITTED;
    }
}
