<?php

namespace App\Filament\Resources\EquipmentUnits;

use App\Filament\Resources\EquipmentUnits\Pages\CreateEquipmentUnit;
use App\Filament\Resources\EquipmentUnits\Pages\EditEquipmentUnit;
use App\Filament\Resources\EquipmentUnits\Pages\ListEquipmentUnits;
use App\Filament\Resources\EquipmentUnits\Schemas\EquipmentUnitForm;
use App\Filament\Resources\EquipmentUnits\Tables\EquipmentUnitsTable;
use App\Models\EquipmentUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EquipmentUnitResource extends Resource
{
    protected static ?string $model = EquipmentUnit::class;

    protected static string|BackedEnum|null $navigationIcon =
    Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup =
    'Master Data Operasional';

    protected static ?string $navigationLabel =
    'Unit Alat';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel =
    'Unit Alat';

    protected static ?string $pluralModelLabel =
    'Unit Alat';

    protected static ?string $recordTitleAttribute =
    'code';

    public static function form(Schema $schema): Schema
    {
        return EquipmentUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquipmentUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEquipmentUnits::route('/'),
            'create' => CreateEquipmentUnit::route('/create'),
            'edit' => EditEquipmentUnit::route('/{record}/edit'),
        ];
    }
}
