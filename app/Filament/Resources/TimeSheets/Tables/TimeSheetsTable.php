<?php

namespace App\Filament\Resources\TimeSheets\Tables;

use App\Filament\Resources\TimeSheets\Actions\TimeSheetReviewActions;
use App\Models\TimeSheet;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;

class TimeSheetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('work_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Pengawas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('operator.name')
                    ->label('Operator')
                    ->searchable()
                    ->description(
                        fn(TimeSheet $record): ?string =>
                        $record->operator?->code,
                    ),

                TextColumn::make('equipmentUnit.code')
                    ->label('Unit')
                    ->searchable()
                    ->description(
                        fn(TimeSheet $record): ?string =>
                        $record->equipmentUnit?->equipment_type,
                    ),

                TextColumn::make('activity.name')
                    ->label('Kegiatan')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn(?string $state): string =>
                        TimeSheet::statusLabel($state),
                    )
                    ->color(
                        fn(?string $state): string =>
                        TimeSheet::statusColor($state),
                    ),

                TextColumn::make('submitted_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        TimeSheet::STATUS_SUBMITTED =>
                        'Menunggu Persetujuan',
                        TimeSheet::STATUS_APPROVED =>
                        'Disetujui',
                        TimeSheet::STATUS_REVISION =>
                        'Perlu Perbaikan',
                        TimeSheet::STATUS_REJECTED =>
                        'Ditolak',
                        TimeSheet::STATUS_DRAFT =>
                        'Draft',
                    ]),

                SelectFilter::make('user')
                    ->label('Pengawas')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),

                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->tooltip('Download laporan Time Sheet')
                    ->url(
                        fn(TimeSheet $record): string =>
                        route(
                            'time-sheets.pdf.download',
                            $record,
                        ),
                    )
                    ->visible(
                        fn(TimeSheet $record): bool =>
                        $record->status !==
                            TimeSheet::STATUS_DRAFT,
                    ),

                TimeSheetReviewActions::approve(),

                TimeSheetReviewActions::requestRevision(),

                TimeSheetReviewActions::reject(),
            ])
            ->defaultSort('id', 'desc');
    }
}
