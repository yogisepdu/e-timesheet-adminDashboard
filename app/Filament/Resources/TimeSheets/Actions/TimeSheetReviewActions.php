<?php

namespace App\Filament\Resources\TimeSheets\Actions;

use App\Models\TimeSheet;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class TimeSheetReviewActions
{
    public static function approve(): Action
    {
        return Action::make('approve')
            ->label('Setujui')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(
                fn (TimeSheet $record): bool =>
                    $record->status === TimeSheet::STATUS_SUBMITTED
                    && auth()->user()?->canReviewTimeSheets() === true,
            )
            ->requiresConfirmation()
            ->modalHeading('Setujui Time Sheet')
            ->modalDescription(
                'Pastikan seluruh data laporan sudah benar sebelum disetujui.',
            )
            ->modalSubmitActionLabel('Ya, Setujui')
            ->schema([
                Textarea::make('notes')
                    ->label('Catatan Persetujuan')
                    ->placeholder('Opsional. Tambahkan catatan jika diperlukan.')
                    ->rows(4)
                    ->maxLength(1000),
            ])
            ->action(function (
                array $data,
                TimeSheet $record,
            ): void {
                Gate::authorize('approve', $record);

                $reviewer = auth()->user();

                abort_unless(
                    $reviewer instanceof User,
                    403,
                );

                $record->approve(
                    $reviewer,
                    filled($data['notes'] ?? null)
                        ? trim($data['notes'])
                        : null,
                );

                Notification::make()
                    ->title('Time Sheet disetujui')
                    ->body("{$record->code} telah disetujui.")
                    ->success()
                    ->send();
            });
    }

    public static function requestRevision(): Action
    {
        return Action::make('requestRevision')
            ->label('Perlu Perbaikan')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->visible(
                fn (TimeSheet $record): bool =>
                    $record->status === TimeSheet::STATUS_SUBMITTED
                    && auth()->user()?->canReviewTimeSheets() === true,
            )
            ->modalHeading('Minta Perbaikan Time Sheet')
            ->modalDescription(
                'Tuliskan bagian yang harus diperbaiki oleh Pengawas.',
            )
            ->modalSubmitActionLabel('Kirim Catatan Perbaikan')
            ->schema([
                Textarea::make('notes')
                    ->label('Catatan Perbaikan')
                    ->placeholder(
                        'Contoh: Periksa kembali HM akhir dan jumlah produksi.',
                    )
                    ->rows(5)
                    ->required()
                    ->minLength(5)
                    ->maxLength(1000),
            ])
            ->action(function (
                array $data,
                TimeSheet $record,
            ): void {
                Gate::authorize(
                    'requestRevision',
                    $record,
                );

                $reviewer = auth()->user();

                abort_unless(
                    $reviewer instanceof User,
                    403,
                );

                $record->requestRevision(
                    $reviewer,
                    trim($data['notes']),
                );

                Notification::make()
                    ->title('Perbaikan diminta')
                    ->body(
                        "{$record->code} dikembalikan kepada Pengawas untuk diperbaiki.",
                    )
                    ->warning()
                    ->send();
            });
    }

    public static function reject(): Action
    {
        return Action::make('reject')
            ->label('Tolak')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(
                fn (TimeSheet $record): bool =>
                    $record->status === TimeSheet::STATUS_SUBMITTED
                    && auth()->user()?->canReviewTimeSheets() === true,
            )
            ->requiresConfirmation()
            ->modalHeading('Tolak Time Sheet')
            ->modalDescription(
                'Penolakan bersifat final untuk laporan ini. Berikan alasan yang jelas.',
            )
            ->modalSubmitActionLabel('Ya, Tolak')
            ->schema([
                Textarea::make('notes')
                    ->label('Alasan Penolakan')
                    ->placeholder('Jelaskan alasan laporan ditolak.')
                    ->rows(5)
                    ->required()
                    ->minLength(5)
                    ->maxLength(1000),
            ])
            ->action(function (
                array $data,
                TimeSheet $record,
            ): void {
                Gate::authorize('reject', $record);

                $reviewer = auth()->user();

                abort_unless(
                    $reviewer instanceof User,
                    403,
                );

                $record->reject(
                    $reviewer,
                    trim($data['notes']),
                );

                Notification::make()
                    ->title('Time Sheet ditolak')
                    ->body("{$record->code} telah ditolak.")
                    ->danger()
                    ->send();
            });
    }
}
