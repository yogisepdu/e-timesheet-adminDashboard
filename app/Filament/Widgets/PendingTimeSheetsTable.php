<?php

namespace App\Filament\Widgets;

use App\Models\TimeSheet;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingTimeSheetsTable extends TableWidget
{
    protected static ?string $heading = 'Time Sheet Menunggu Persetujuan';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TimeSheet::query()
                    ->with([
                        'operator',
                        'contractor',
                        'equipmentUnit',
                        'activity',
                    ])
                    ->where(
                        'status',
                        TimeSheet::STATUS_SUBMITTED
                    )
                    ->latest('submitted_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('work_date')
                    ->label('Tanggal Kerja')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('operator.name')
                    ->label('Operator')
                    ->searchable(),

                Tables\Columns\TextColumn::make('contractor.name')
                    ->label('Kontraktor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('equipmentUnit.code')
                    ->label('Unit')
                    ->searchable(),

                Tables\Columns\TextColumn::make('activity.name')
                    ->label('Aktivitas')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Jam')
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\TextColumn::make('total_hm')
                    ->label('HM')
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25]);
    }
}
