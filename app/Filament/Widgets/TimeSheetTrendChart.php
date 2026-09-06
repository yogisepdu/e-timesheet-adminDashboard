<?php

namespace App\Filament\Widgets;

use App\Models\TimeSheet;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TimeSheetTrendChart extends ChartWidget
{
    protected ?string $heading = 'Trend Time Sheet';

    protected ?string $description = 'Jumlah time sheet 6 bulan terakhir';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()
                ->subMonths($i)
                ->startOfMonth();

            $end = $date->copy()->endOfMonth();

            $labels[] = $date->translatedFormat('M Y');

            $data[] = TimeSheet::query()
                ->whereBetween('work_date', [
                    $date,
                    $end,
                ])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Time Sheet',
                    'data' => $data,
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
