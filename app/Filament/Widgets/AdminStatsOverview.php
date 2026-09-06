<?php

namespace App\Filament\Widgets;

use App\Models\TimeSheet;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Ringkasan Operasional';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $total = TimeSheet::count();

        $todayCount = TimeSheet::query()
            ->whereDate('work_date', $today)
            ->count();

        $submitted = TimeSheet::query()
            ->where('status', TimeSheet::STATUS_SUBMITTED)
            ->count();

        $approvedThisMonth = TimeSheet::query()
            ->where('status', TimeSheet::STATUS_APPROVED)
            ->whereBetween('work_date', [
                $startOfMonth,
                $endOfMonth,
            ])
            ->count();

        $revision = TimeSheet::query()
            ->where('status', TimeSheet::STATUS_REVISION)
            ->count();

        $rejected = TimeSheet::query()
            ->where('status', TimeSheet::STATUS_REJECTED)
            ->count();

        $totalHours = TimeSheet::query()
            ->where('status', TimeSheet::STATUS_APPROVED)
            ->sum('total_hours');

        $totalHm = TimeSheet::query()
            ->where('status', TimeSheet::STATUS_APPROVED)
            ->sum('total_hm');

        return [
            Stat::make(
                'Total Time Sheet',
                number_format($total)
            )
                ->description('Seluruh data yang masuk')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make(
                'Hari Ini',
                number_format($todayCount)
            )
                ->description('Time sheet hari ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make(
                'Menunggu Persetujuan',
                number_format($submitted)
            )
                ->description('Perlu ditinjau')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(
                'Disetujui Bulan Ini',
                number_format($approvedThisMonth)
            )
                ->description('Time sheet approved')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(
                'Perlu Perbaikan',
                number_format($revision)
            )
                ->description('Dikembalikan ke client')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make(
                'Ditolak',
                number_format($rejected)
            )
                ->description('Time sheet rejected')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make(
                'Total Jam Kerja',
                number_format((float) $totalHours, 2)
            )
                ->description('Dari time sheet approved')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),

            Stat::make(
                'Total HM',
                number_format((float) $totalHm, 2)
            )
                ->description('Hour Meter')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
}
