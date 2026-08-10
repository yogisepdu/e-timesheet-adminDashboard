<?php

namespace App\Filament\Resources\TimeSheets\Pages;

use App\Filament\Resources\TimeSheets\Actions\TimeSheetReviewActions;
use App\Filament\Resources\TimeSheets\TimeSheetResource;
use App\Models\TimeSheet;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTimeSheet extends ViewRecord
{
    protected static string $resource =
    TimeSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(
                    fn(): string =>
                    route(
                        'time-sheets.pdf.download',
                        $this->record,
                    ),
                )
                ->visible(
                    fn(): bool =>
                    $this->record->status !==
                        TimeSheet::STATUS_DRAFT,
                ),

            TimeSheetReviewActions::approve(),

            TimeSheetReviewActions::requestRevision(),

            TimeSheetReviewActions::reject(),
        ];
    }
}
