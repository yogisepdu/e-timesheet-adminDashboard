<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $baseQuery = TimeSheet::query()
            ->where('user_id', $user->id);

        $summary = [
            'draft' => (clone $baseQuery)
                ->where('status', TimeSheet::STATUS_DRAFT)
                ->count(),

            'submitted' => (clone $baseQuery)
                ->where('status', TimeSheet::STATUS_SUBMITTED)
                ->count(),

            'approved' => (clone $baseQuery)
                ->where('status', TimeSheet::STATUS_APPROVED)
                ->count(),

            'revision' => (clone $baseQuery)
                ->where('status', TimeSheet::STATUS_REVISION)
                ->count(),
        ];

        $recentReports = (clone $baseQuery)
            ->with([
                'operator:id,name',
                'equipmentUnit:id,code,equipment_type',
            ])
            ->latest('work_date')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(function (TimeSheet $timeSheet): array {
                return [
                    'id' => $timeSheet->id,
                    'code' => $timeSheet->code,
                    'operator' => $timeSheet->operator?->name ?? '-',
                    'unit' => $timeSheet->equipmentUnit
                        ? $timeSheet->equipmentUnit->code
                        : '-',
                    'status' => $timeSheet->status,
                    'status_label' => TimeSheet::statusLabel(
                        $timeSheet->status
                    ),
                    'work_date' => $timeSheet->work_date?->toDateString(),
                ];
            })
            ->values();

        return response()->json([
            'data' => [
                'summary' => $summary,
                'recent_reports' => $recentReports,
            ],
        ]);
    }
}
