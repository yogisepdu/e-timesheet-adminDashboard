<?php

namespace App\Filament\Resources\TimeSheets\Pages;

use App\Filament\Resources\TimeSheets\TimeSheetResource;
use Filament\Resources\Pages\ListRecords;

class ListTimeSheets extends ListRecords
{
    protected static string $resource =
        TimeSheetResource::class;
}
