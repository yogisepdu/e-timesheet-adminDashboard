<?php

namespace App\Filament\Resources\Contractors\Tables;

use App\Models\Contractor;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContractorsTable
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

                TextColumn::make('name')
                    ->label('Nama Kontraktor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            Contractor::TYPE_INTERNAL => 'Internal',
                            Contractor::TYPE_EXTERNAL => 'External',
                            default => ucfirst($state),
                        }
                    )
                    ->color(
                        fn(string $state): string => match ($state) {
                            Contractor::TYPE_INTERNAL => 'success',
                            Contractor::TYPE_EXTERNAL => 'info',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->placeholder('-')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        Contractor::TYPE_INTERNAL => 'Internal',
                        Contractor::TYPE_EXTERNAL => 'External',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->visible(
                        fn(Contractor $record): bool =>
                        $record->code !== 'INT'
                    )
                    ->requiresConfirmation(),
            ])
            ->defaultSort('name');
    }
}
