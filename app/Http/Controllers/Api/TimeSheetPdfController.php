<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TimeSheetPdfController extends Controller
{
    public function download(TimeSheet $timeSheet): Response
    {
        /*
         * Authorization:
         *
         * Pengawas hanya boleh mengunduh Time Sheet
         * yang memang menjadi miliknya.
         *
         * Aturan detailnya mengikuti Policy/Gate yang
         * sudah digunakan oleh controller Admin.
         */
        Gate::authorize('view', $timeSheet);

        $timeSheet->load([
            'user:id,name,username,position',
            'contractor:id,code,name,type',
            'operator:id,code,name',
            'equipmentUnit:id,code,equipment_type,brand,model',
            'activity:id,code,name',
            'reviewer:id,name,username,position',
        ]);

        /*
         * Logo perusahaan.
         */
        $logoPath = public_path(
            'images/logo-pns.png',
        );

        /*
         * Label status untuk watermark PDF.
         */
        $statusLabel = TimeSheet::statusLabel(
            $timeSheet->status,
        );

        $pdf = Pdf::loadView(
            'pdf.time-sheet',
            [
                'timeSheet' => $timeSheet,
                'logoPath' => $logoPath,
                'statusLabel' => $statusLabel,
            ],
        )
            ->setPaper(
                'a4',
                'landscape',
            );

        $filename = "{$timeSheet->code}.pdf";

        return $pdf->download(
            $filename,
        );
    }
}
