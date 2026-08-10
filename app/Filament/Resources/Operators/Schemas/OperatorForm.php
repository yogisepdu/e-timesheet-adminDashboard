<?php

namespace App\Filament\Resources\Operators\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OperatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Operator')
                    ->description(
                        'Data operator alat yang akan tersedia pada aplikasi Android.'
                    )
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Operator')
                            ->placeholder('Contoh: OP001')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30)
                            ->dehydrateStateUsing(
                                fn(?string $state): string =>
                                strtoupper(trim((string) $state))
                            ),

                        TextInput::make('name')
                            ->label('Nama Operator')
                            ->placeholder('Masukkan nama operator')
                            ->required()
                            ->maxLength(150),

                        Select::make('contractor_id')
                            ->label('Kontraktor')
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

                        TextInput::make('position')
                            ->label('Jabatan')
                            ->default('Operator')
                            ->maxLength(100),

                        TextInput::make('phone')
                            ->label('Nomor HP')
                            ->tel()
                            ->placeholder('08xxxxxxxxxx')
                            ->maxLength(20),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText(
                                'Operator nonaktif tidak ditampilkan di aplikasi Android.'
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
