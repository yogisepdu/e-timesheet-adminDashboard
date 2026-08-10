<?php

namespace App\Filament\Resources\EquipmentUnits\Pages;

use App\Filament\Resources\EquipmentUnits\EquipmentUnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEquipmentUnits extends ListRecords
{
    protected static string $resource = EquipmentUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
