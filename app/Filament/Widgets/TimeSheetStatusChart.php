<?php

namespace App\Filament\Widgets;

use App\Models\TimeSheet;
use Filament\Widgets\ChartWidget;

class TimeSheetStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Time Sheet';

    protected ?string $description = 'Distribusi status seluruh time sheet';

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Time Sheet',
                    'data' => [
                        TimeSheet::where(
                            'status',
                            TimeSheet::STATUS_DRAFT
                        )->count(),

                        TimeSheet::where(
                            'status',
                            TimeSheet::STATUS_SUBMITTED
                        )->count(),

                        TimeSheet::where(
                            'status',
                            TimeSheet::STATUS_APPROVED
                        )->count(),

                        TimeSheet::where(
                            'status',
                            TimeSheet::STATUS_REVISION
                        )->count(),

                        TimeSheet::where(
                            'status',
                            TimeSheet::STATUS_REJECTED
                        )->count(),
                    ],
                ],
            ],

            'labels' => [
                'Draft',
                'Menunggu Persetujuan',
                'Disetujui',
                'Perlu Perbaikan',
                'Ditolak',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
