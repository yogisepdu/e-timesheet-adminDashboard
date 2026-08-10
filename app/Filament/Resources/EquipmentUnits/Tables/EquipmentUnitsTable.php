<?php

namespace App\Filament\Resources\EquipmentUnits\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EquipmentUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Unit')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('equipment_type')
                    ->label('Jenis Alat')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('brand')
                    ->label('Merek')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('model')
                    ->label('Model')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('contractor.name')
                    ->label('Kontraktor')
                    ->badge()
                    ->placeholder('-'),

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
                SelectFilter::make('contractor')
                    ->label('Kontraktor')
                    ->relationship(
                        'contractor',
                        'name'
                    )
                    ->searchable()
                    ->preload(),

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
                    ->requiresConfirmation(),
            ])
            ->defaultSort('code');
    }
}
