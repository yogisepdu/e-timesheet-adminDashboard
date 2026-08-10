<?php

namespace App\Filament\Resources\EquipmentUnits\Pages;

use App\Filament\Resources\EquipmentUnits\EquipmentUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEquipmentUnit extends EditRecord
{
    protected static string $resource = EquipmentUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
