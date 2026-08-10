<?php

namespace App\Filament\Resources\Contractors\Schemas;

use App\Models\Contractor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContractorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kontraktor')
                    ->description(
                        'Data perusahaan atau pihak yang menyediakan operator dan unit alat.'
                    )
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Kontraktor')
                            ->placeholder('Contoh: CTR001')
                            ->required()
                            ->maxLength(30)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(
                                fn(?string $state): string =>
                                strtoupper(trim((string) $state))
                            ),

                        TextInput::make('name')
                            ->label('Nama Kontraktor')
                            ->placeholder('Contoh: PT Karya Abadi')
                            ->required()
                            ->maxLength(150),

                        Select::make('type')
                            ->label('Jenis Kontraktor')
                            ->options([
                                Contractor::TYPE_INTERNAL => 'Internal',
                                Contractor::TYPE_EXTERNAL => 'External',
                            ])
                            ->default(Contractor::TYPE_EXTERNAL)
                            ->required()
                            ->native(false),

                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->placeholder('08xxxxxxxxxx')
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Section::make('Informasi Tambahan')
                    ->schema([
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->placeholder('Masukkan alamat kontraktor')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Keterangan tambahan')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText(
                                'Kontraktor nonaktif tidak akan ditampilkan pada aplikasi mobile.'
                            )
                            ->default(true),
                    ]),
            ]);
    }
}
