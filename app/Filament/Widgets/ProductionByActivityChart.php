<?php

namespace App\Filament\Widgets;

use App\Models\TimeSheet;
use Filament\Widgets\ChartWidget;

class ProductionByActivityChart extends ChartWidget
{
    protected ?string $heading = 'Produksi Berdasarkan Aktivitas';

    protected ?string $description = 'Total produksi dari time sheet yang disetujui';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected function getData(): array
    {
        $records = TimeSheet::query()
            ->selectRaw('activity_id, SUM(production) as total_production')
            ->where('status', TimeSheet::STATUS_APPROVED)
            ->whereNotNull('activity_id')
            ->whereNotNull('production')
            ->with('activity:id,name')
            ->groupBy('activity_id')
            ->orderByDesc('total_production')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Produksi',
                    'data' => $records
                        ->map(fn($record) => (float) $record->total_production)
                        ->values()
                        ->all(),
                ],
            ],

            'labels' => $records
                ->map(fn($record) => $record->activity?->name ?? 'Tidak diketahui')
                ->values()
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
