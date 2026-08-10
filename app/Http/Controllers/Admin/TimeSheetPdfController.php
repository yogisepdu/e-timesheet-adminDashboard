<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TimeSheetPdfController extends Controller
{
    public function download(TimeSheet $timeSheet): Response
    {
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
         * Lokasi logo pada folder public.
         *
         * File harus berada di:
         * public/images/logo-pns.png
         */
        $logoPath = public_path(
            'images/logo-pns.png',
        );

        $pdf = Pdf::loadView(
            'pdf.time-sheet',
            [
                'timeSheet' => $timeSheet,
                'logoPath' => $logoPath,
            ],
        )
            ->setPaper(
                'a4',
                'landscape',
            );

        $filename = "{$timeSheet->code}.pdf";

        // dd([
        //     'path' => $logoPath,
        //     'exists' => file_exists($logoPath),
        // ]);

        return $pdf->download(
            $filename,
        );
    }
}
