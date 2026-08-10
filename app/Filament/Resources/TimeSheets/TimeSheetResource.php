<?php

namespace App\Filament\Resources\TimeSheets;

use App\Filament\Resources\TimeSheets\Pages\ListTimeSheets;
use App\Filament\Resources\TimeSheets\Pages\ViewTimeSheet;
use App\Filament\Resources\TimeSheets\Schemas\TimeSheetInfolist;
use App\Filament\Resources\TimeSheets\Tables\TimeSheetsTable;
use App\Models\TimeSheet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TimeSheetResource extends Resource
{
    protected static ?string $model = TimeSheet::class;

    protected static string | BackedEnum | null $navigationIcon =
        'heroicon-o-clipboard-document-check';

    protected static string | UnitEnum | null $navigationGroup =
        'Laporan & Persetujuan';

    protected static ?string $navigationLabel =
        'Persetujuan Time Sheet';

    protected static ?string $modelLabel =
        'Time Sheet';

    protected static ?string $pluralModelLabel =
        'Time Sheet';

    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return TimeSheetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TimeSheetsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'user',
                'contractor',
                'operator',
                'equipmentUnit',
                'activity',
                'reviewer',
            ]);

        if (auth()->user()?->isAtasan()) {
            $query->where(
                'status',
                '!=',
                TimeSheet::STATUS_DRAFT,
            );
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTimeSheets::route('/'),
            'view' => ViewTimeSheet::route('/{record}'),
        ];
    }
}
