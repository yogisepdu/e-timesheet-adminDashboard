<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->description(
                        'Data akun yang digunakan pengguna untuk mengakses sistem.'
                    )
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->alphaDash()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText(
                                'Username digunakan Pengawas untuk login ke aplikasi Android.'
                            ),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Opsional untuk akun Pengawas'),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(
                                fn(string $operation): bool =>
                                $operation === 'create'
                            )
                            ->dehydrated(
                                fn(?string $state): bool =>
                                filled($state)
                            )
                            ->minLength(8)
                            ->maxLength(255)
                            ->helperText(
                                'Kosongkan pada halaman edit jika password tidak ingin diubah.'
                            ),
                    ])
                    ->columns(2),

                Section::make('Informasi Pengguna')
                    ->schema([
                        Select::make('role')
                            ->label('Role')
                            ->options([
                                User::ROLE_ADMIN => 'Admin',
                                User::ROLE_PENGAWAS => 'Pengawas',
                            ])
                            ->default(User::ROLE_PENGAWAS)
                            ->required()
                            ->native(false),

                        TextInput::make('position')
                            ->label('Jabatan')
                            ->placeholder('Contoh: Pengawas Operasional')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('phone')
                            ->label('Nomor HP')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('08xxxxxxxxxx'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText(
                                'Pengguna yang nonaktif tidak dapat mengakses sistem.'
                            )
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }
}
