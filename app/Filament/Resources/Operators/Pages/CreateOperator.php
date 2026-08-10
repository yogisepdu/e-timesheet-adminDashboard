<?php

namespace App\Filament\Resources\Operators\Pages;

use App\Filament\Resources\Operators\OperatorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOperator extends CreateRecord
{
    protected static string $resource = OperatorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
