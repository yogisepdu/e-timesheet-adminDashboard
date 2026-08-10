<?php

namespace App\Filament\Resources\EquipmentUnits\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EquipmentUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Unit Alat')
                    ->description(
                        'Data unit alat yang digunakan untuk pengisian Time Sheet.'
                    )
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Unit')
                            ->placeholder('Contoh: EXC-001')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->dehydrateStateUsing(
                                fn(?string $state): string =>
                                strtoupper(trim((string) $state))
                            ),

                        TextInput::make('equipment_type')
                            ->label('Jenis Alat')
                            ->placeholder(
                                'Contoh: Excavator Komatsu PC200'
                            )
                            ->required()
                            ->maxLength(150),

                        Select::make('contractor_id')
                            ->label('Kontraktor / Pemilik')
                            ->relationship(
                                name: 'contractor',
                                titleAttribute: 'name',
                            )
                            ->searchable([
                                'code',
                                'name',
                            ])
                            ->preload()
                            ->native(false)
                            ->placeholder('Pilih kontraktor'),

                        TextInput::make('brand')
                            ->label('Merek')
                            ->placeholder('Contoh: Komatsu')
                            ->maxLength(100),

                        TextInput::make('model')
                            ->label('Model')
                            ->placeholder('Contoh: PC200')
                            ->maxLength(100),

                        TextInput::make('registration_number')
                            ->label('Nomor Registrasi')
                            ->placeholder('Opsional')
                            ->maxLength(100),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText(
                                'Unit nonaktif tidak dapat dipilih pada aplikasi Android.'
                            ),
                    ])
                    ->columns(2),

                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('description')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
