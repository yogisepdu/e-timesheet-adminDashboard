<?php

namespace App\Filament\Resources\TimeSheets\Schemas;

use App\Models\TimeSheet;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TimeSheetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status Laporan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('code')
                            ->label('Kode Time Sheet')
                            ->copyable()
                            ->weight('bold'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string =>
                                    TimeSheet::statusLabel($state),
                            )
                            ->color(
                                fn (?string $state): string =>
                                    TimeSheet::statusColor($state),
                            ),

                        TextEntry::make('work_date')
                            ->label('Tanggal Pekerjaan')
                            ->date('d M Y'),
                    ]),

                Section::make('Pengawas & Identitas')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Pengawas'),

                        TextEntry::make('user.username')
                            ->label('Username'),

                        TextEntry::make('contractor.name')
                            ->label('Kontraktor'),
                    ]),

                Section::make('Operator & Unit Alat')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('operator.name')
                            ->label('Operator')
                            ->state(
                                fn (TimeSheet $record): string =>
                                    $record->operator
                                        ? "{$record->operator->code} - {$record->operator->name}"
                                        : '-',
                            ),

                        TextEntry::make('equipmentUnit.code')
                            ->label('Unit Alat')
                            ->state(
                                fn (TimeSheet $record): string =>
                                    $record->equipmentUnit
                                        ? "{$record->equipmentUnit->code} - {$record->equipmentUnit->equipment_type}"
                                        : '-',
                            ),
                    ]),

                Section::make('Jam Operasi')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('start_time')
                            ->label('Jam Mulai')
                            ->state(
                                fn (TimeSheet $record): string =>
                                    $record->start_time
                                        ? substr((string) $record->start_time, 0, 5)
                                        : '-',
                            ),

                        TextEntry::make('end_time')
                            ->label('Jam Selesai')
                            ->state(
                                fn (TimeSheet $record): string =>
                                    $record->end_time
                                        ? substr((string) $record->end_time, 0, 5)
                                        : '-',
                            ),

                        TextEntry::make('total_hours')
                            ->label('Total Jam')
                            ->formatStateUsing(
                                fn ($state): string =>
                                    $state !== null
                                        ? number_format((float) $state, 2, ',', '.') . ' jam'
                                        : '-',
                            ),
                    ]),

                Section::make('Hour Meter & BBM')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('hm_start')
                            ->label('HM Awal')
                            ->formatStateUsing(
                                fn ($state): string =>
                                    $state !== null
                                        ? number_format((float) $state, 2, ',', '.')
                                        : '-',
                            ),

                        TextEntry::make('hm_end')
                            ->label('HM Akhir')
                            ->formatStateUsing(
                                fn ($state): string =>
                                    $state !== null
                                        ? number_format((float) $state, 2, ',', '.')
                                        : '-',
                            ),

                        TextEntry::make('total_hm')
                            ->label('Total HM')
                            ->formatStateUsing(
                                fn ($state): string =>
                                    $state !== null
                                        ? number_format((float) $state, 2, ',', '.')
                                        : '-',
                            ),

                        TextEntry::make('fuel_used')
                            ->label('BBM Terpakai')
                            ->formatStateUsing(
                                fn ($state): string =>
                                    $state !== null
                                        ? number_format((float) $state, 2, ',', '.') . ' Liter'
                                        : '-',
                            ),
                    ]),

                Section::make('Kegiatan & Produksi')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('location')
                            ->label('Lokasi'),

                        TextEntry::make('activity.name')
                            ->label('Kegiatan'),

                        TextEntry::make('production')
                            ->label('Jumlah Produksi')
                            ->state(
                                fn (TimeSheet $record): string =>
                                    $record->production !== null
                                        ? number_format(
                                            (float) $record->production,
                                            2,
                                            ',',
                                            '.',
                                        ) . ' ' . ($record->production_unit ?? '')
                                        : '-',
                            ),

                        TextEntry::make('submitted_at')
                            ->label('Dikirim Pada')
                            ->dateTime('d M Y H:i'),
                    ]),

                Section::make('Hasil Pemeriksaan Atasan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reviewer.name')
                            ->label('Diperiksa Oleh')
                            ->state(
                                fn (TimeSheet $record): string =>
                                    $record->reviewer?->name ?? '-',
                            ),

                        TextEntry::make('reviewed_at')
                            ->label('Diperiksa Pada')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('review_notes')
                            ->label('Catatan Atasan')
                            ->state(
                                fn (TimeSheet $record): string =>
                                    filled($record->review_notes)
                                        ? $record->review_notes
                                        : '-',
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
