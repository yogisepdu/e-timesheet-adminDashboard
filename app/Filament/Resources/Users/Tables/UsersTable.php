<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            User::ROLE_ADMIN => 'Admin',
                            User::ROLE_PENGAWAS => 'Pengawas',
                            default => ucfirst($state),
                        }
                    )
                    ->color(
                        fn(string $state): string => match ($state) {
                            User::ROLE_ADMIN => 'danger',
                            User::ROLE_PENGAWAS => 'info',
                            default => 'gray',
                        }
                    ),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('last_login_at')
                    ->label('Login Terakhir')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum pernah login')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        User::ROLE_ADMIN => 'Admin',
                        User::ROLE_PENGAWAS => 'Pengawas',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status Akun')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make()
                    ->visible(
                        fn(User $record): bool =>
                        $record->getKey() !== auth()->id()
                    )
                    ->requiresConfirmation(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
