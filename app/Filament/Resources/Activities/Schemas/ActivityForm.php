<?php

namespace App\Filament\Resources\Activities\Schemas;

use App\Models\Activity;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kegiatan')
                    ->description(
                        'Master kegiatan operasional yang dapat dipilih pada Time Sheet.'
                    )
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Kegiatan')
                            ->placeholder('Contoh: ACT001')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30)
                            ->dehydrateStateUsing(
                                fn(?string $state): string =>
                                strtoupper(trim((string) $state))
                            ),

                        TextInput::make('name')
                            ->label('Nama Kegiatan')
                            ->placeholder('Contoh: MCH S. Shaving')
                            ->required()
                            ->maxLength(150),

                        Select::make('default_production_unit')
                            ->label('Satuan Produksi')
                            ->options(
                                Activity::productionUnits()
                            )
                            ->native(false)
                            ->placeholder('Pilih satuan'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText(
                                'Kegiatan nonaktif tidak ditampilkan pada aplikasi Android.'
                            ),
                    ])
                    ->columns(2),

                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
