<?php

namespace App\Policies;

use App\Models\TimeSheet;
use App\Models\User;

class TimeSheetPolicy
{
    /**
     * User yang dapat melihat daftar Time Sheet.
     *
     * Pengawas tidak menggunakan viewAny() untuk daftar
     * pada Admin Dashboard. Daftar milik pengawas diambil
     * melalui API TimeSheetController yang sudah dibatasi
     * berdasarkan user_id.
     */
    public function viewAny(User $user): bool
    {
        return $user->canReviewTimeSheets();
    }

    /**
     * Menentukan apakah user boleh melihat satu Time Sheet.
     *
     * Admin / Atasan:
     * - Tetap mengikuti hak review yang sudah ada.
     *
     * Pengawas:
     * - Hanya boleh melihat Time Sheet miliknya sendiri.
     */
    public function view(User $user, TimeSheet $timeSheet): bool
    {
        /**
         * Admin / Atasan / user yang memiliki hak review
         * tetap memiliki akses view seperti sebelumnya.
         */
        if ($user->canReviewTimeSheets()) {
            return true;
        }

        /**
         * Pengawas hanya boleh melihat Time Sheet
         * yang dibuat oleh dirinya sendiri.
         */
        if ($user->isPengawas()) {
            return (int) $timeSheet->user_id === (int) $user->id;
        }

        return false;
    }

    /**
     * Membuat Time Sheet.
     *
     * Pembuatan Time Sheet dilakukan melalui API/mobile
     * controller, bukan melalui policy create Admin.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Update Time Sheet melalui policy Admin.
     *
     * Proses update draft dikontrol oleh endpoint/service
     * Time Sheet masing-masing.
     */
    public function update(User $user, TimeSheet $timeSheet): bool
    {
        return false;
    }

    /**
     * Delete satu Time Sheet.
     */
    public function delete(User $user, TimeSheet $timeSheet): bool
    {
        return false;
    }

    /**
     * Delete seluruh Time Sheet.
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }

    /**
     * Approve Time Sheet.
     *
     * Hanya user yang memiliki hak review.
     */
    public function approve(User $user, TimeSheet $timeSheet): bool
    {
        return $user->canReviewTimeSheets()
            && $timeSheet->status === TimeSheet::STATUS_SUBMITTED;
    }

    /**
     * Meminta revisi Time Sheet.
     *
     * Hanya user yang memiliki hak review.
     */
    public function requestRevision(
        User $user,
        TimeSheet $timeSheet,
    ): bool {
        return $user->canReviewTimeSheets()
            && $timeSheet->status === TimeSheet::STATUS_SUBMITTED;
    }

    /**
     * Menolak Time Sheet.
     *
     * Hanya user yang memiliki hak review.
     */
    public function reject(
        User $user,
        TimeSheet $timeSheet,
    ): bool {
        return $user->canReviewTimeSheets()
            && $timeSheet->status === TimeSheet::STATUS_SUBMITTED;
    }
}
