<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">

    <title>
        {{ $timeSheet->code }} - Laporan Pemakaian Alat
    </title>

    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #111;
            font-size: 8.2px;
            line-height: 1.2;
        }

        .sheet {
            width: 96%;
            margin: 0 auto;
            border: 1.2px solid #111;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .header-table {
            width: 100%;
            table-layout: fixed;
        }

        .header-table>tbody>tr>td {
            border-right: 1px solid #111;
            border-bottom: 1px solid #111;
            vertical-align: middle;
        }

        .header-table>tbody>tr>td:last-child {
            border-right: 0;
        }

        .logo-cell {
            width: 15%;
            height: 42mm;
            text-align: center;
            padding: 3mm 2mm;
        }

        .logo-cell img {
            max-width: 31mm;
            max-height: 27mm;
        }

        .logo-fallback {
            font-size: 9px;
            font-weight: bold;
            line-height: 1.3;
        }

        .title-cell {
            width: 50%;
            padding: 0;
        }

        .document-cell {
            width: 35%;
            padding: 0;
        }

        .main-title {
            height: 31mm;
            text-align: center;
            font-size: 18px;
            line-height: 1.35;
            font-weight: bold;
            padding-top: 6mm;
        }

        .form-label {
            height: 11mm;
            border-top: 1px solid #111;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding-top: 2.5mm;
        }

        .document-table {
            width: 100%;
            height: 42mm;
            table-layout: fixed;
        }

        .document-table td {
            border-bottom: 1px solid #111;
            padding: 1.7mm 2mm;
            font-size: 8px;
            vertical-align: middle;
        }

        .document-table tr:last-child td {
            border-bottom: 0;
        }

        .document-table .doc-label {
            width: 46%;
            border-right: 1px solid #111;
        }

        .document-table .doc-value {
            width: 54%;
        }

        .identity {
            width: 100%;
            padding: 2.2mm 4mm 2.4mm;
            border-bottom: 1px solid #111;
        }

        .identity-table {
            width: 100%;
            table-layout: fixed;
        }

        .identity-table td {
            padding: 0.7mm 0;
            vertical-align: top;
        }

        .identity-left {
            width: 60%;
        }

        .identity-right {
            width: 40%;
        }

        .identity-label {
            display: inline-block;
            width: 27mm;
        }

        .identity-label-right {
            display: inline-block;
            width: 30mm;
        }

        .identity-value {
            font-weight: bold;
        }

        .work-table-wrap {
            padding: 1.5mm 3.5mm 0;
        }

        .work-table {
            width: 100%;
            table-layout: fixed;
        }

        .work-table th,
        .work-table td {
            border: 0.8px solid #111;
            text-align: center;
            vertical-align: middle;
            padding: 1.1mm 0.8mm;
            overflow: hidden;
        }

        .work-table th {
            font-weight: normal;
        }

        .work-table .group {
            font-size: 7.5px;
        }

        .work-table .sub {
            font-size: 7.2px;
        }

        .work-table tbody td {
            height: 8mm;
            font-size: 7.4px;
        }

        .work-table .activity-cell {
            text-align: left;
            padding-left: 1.4mm;
            white-space: normal;
        }

        .work-table .location-cell {
            white-space: normal;
        }

        .footer-area {
            min-height: 52mm;
            padding: 2.5mm 3.5mm 1mm;
        }

        .notes {
            height: 14mm;
            font-size: 8px;
            line-height: 1.8;
        }

        .signature-table {
            width: 100%;
            table-layout: fixed;
            margin-top: 1mm;
        }

        .signature-table td {
            width: 25%;
            text-align: center;
            vertical-align: top;
        }

        .signature-title {
            height: 6mm;
            font-size: 8px;
        }

        .signature-space {
            height: 18mm;
        }

        .signature-line {
            width: 33mm;
            margin: 0 auto;
            border-bottom: 0.7px dotted #111;
        }

        .signature-name {
            min-height: 6mm;
            padding-top: 1.5mm;
            font-size: 8px;
        }

        .empty {
            color: transparent;
        }

        .status-watermark {
            position: fixed;
            top: 43%;
            left: 10%;
            width: 80%;

            text-align: center;

            font-size: 46px;
            font-weight: bold;
            letter-spacing: 2px;

            color: #000;
            opacity: 0.08;

            transform: rotate(-25deg);

            z-index: -1;
        }
    </style>
</head>

<body>

    @php
        $formatNumber = static function ($value, int $decimals = 2): string {
            if ($value === null || $value === '') {
                return '';
            }

            return number_format((float) $value, $decimals, ',', '.');
        };

        $productionMeter = $timeSheet->production_unit === 'Meter' ? $formatNumber($timeSheet->production) : '';

        $productionM3 = in_array($timeSheet->production_unit, ['m³', 'M3', 'm3'], true)
            ? $formatNumber($timeSheet->production)
            : '';

        $productionHa = $timeSheet->production_unit === 'Ha' ? $formatNumber($timeSheet->production) : '';
    @endphp

    {{-- STATUS WATERMARK --}}
    @if (!empty($statusLabel))
        <div class="status-watermark">
            {{ strtoupper($statusLabel) }}
        </div>
    @endif

    <div class="sheet">

        {{-- HEADER --}}
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if (isset($logoPath) && file_exists($logoPath))
                        <img alt="Pratama Nusantara Sakti" src="{{ $logoPath }}">
                    @else
                        <div class="logo-fallback">
                            PRATAMA<br>
                            NUSANTARA SAKTI
                        </div>
                    @endif
                </td>

                <td class="title-cell">
                    <div class="main-title">
                        LAPORAN PEMAKAIAN ALAT<br>
                        (TIME SHEET)
                    </div>

                    <div class="form-label">
                        FORMULIR
                    </div>
                </td>

                <td class="document-cell">
                    <table class="document-table">
                        <tr>
                            <td class="doc-label">
                                Nomor Dokumen
                            </td>
                            <td class="doc-value">
                                : FM-BS-ADM-05
                            </td>
                        </tr>

                        <tr>
                            <td class="doc-label">
                                Revisi
                            </td>
                            <td class="doc-value">
                                : 01
                            </td>
                        </tr>

                        <tr>
                            <td class="doc-label">
                                Tanggal Efektif
                            </td>
                            <td class="doc-value">
                                : 1 Januari 2022
                            </td>
                        </tr>

                        <tr>
                            <td class="doc-label">
                                Halaman
                            </td>
                            <td class="doc-value">
                                : 1 dari 1
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- IDENTITAS --}}
        <div class="identity">
            <table class="identity-table">
                <tr>
                    <td class="identity-left">
                        <span class="identity-label">
                            Hari &amp; Tanggal
                        </span>
                        :
                        <span class="identity-value">
                            {{ $timeSheet->work_date?->format('d-m-Y') ?? '-' }}
                        </span>
                    </td>

                    <td class="identity-right">
                        <span class="identity-label-right">
                            Nama Operator
                        </span>
                        :
                        <span class="identity-value">
                            {{ $timeSheet->operator?->name ?? '-' }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td class="identity-left">
                        <span class="identity-label">
                            Nama Pengawas
                        </span>
                        :
                        <span class="identity-value">
                            {{ $timeSheet->user?->name ?? '-' }}
                        </span>
                    </td>

                    <td class="identity-right">
                        <span class="identity-label-right">
                            Kode Unit Alat
                        </span>
                        :
                        <span class="identity-value">
                            {{ $timeSheet->equipmentUnit?->code ?? '-' }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td class="identity-left">
                        <span class="identity-label">
                            Kontraktor
                        </span>
                        :
                        <span class="identity-value">
                            {{ $timeSheet->contractor?->name ?? '-' }}
                        </span>
                    </td>

                    <td class="identity-right">
                        <span class="identity-label-right">
                            Jenis Alat
                        </span>
                        :
                        <span class="identity-value">
                            {{ $timeSheet->equipmentUnit?->equipment_type ?? '-' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- TABEL UTAMA --}}
        <div class="work-table-wrap">
            <table class="work-table">
                <colgroup>
                    <col style="width: 7%;">
                    <col style="width: 7%;">
                    <col style="width: 7%;">

                    <col style="width: 7.5%;">
                    <col style="width: 7.5%;">
                    <col style="width: 7.5%;">

                    <col style="width: 6%;">

                    <col style="width: 7.5%;">
                    <col style="width: 25%;">

                    <col style="width: 6%;">
                    <col style="width: 6%;">
                    <col style="width: 6%;">
                </colgroup>

                <thead>
                    <tr>
                        <th class="group" colspan="3">
                            Jam Operator
                        </th>

                        <th class="group" colspan="3">
                            Jam Alat/Hours Meter (HM)
                        </th>

                        <th class="group">
                            BBM
                        </th>

                        <th class="group" rowspan="2">
                            Lokasi
                        </th>

                        <th class="group" rowspan="2">
                            Kegiatan / Activity
                        </th>

                        <th class="group" colspan="3">
                            Produksi
                        </th>
                    </tr>

                    <tr>
                        <th class="sub">
                            Mulai
                        </th>
                        <th class="sub">
                            Selesai
                        </th>
                        <th class="sub">
                            Total Jam
                        </th>

                        <th class="sub">
                            HM Awal
                        </th>
                        <th class="sub">
                            HM Akhir
                        </th>
                        <th class="sub">
                            Total HM
                        </th>

                        <th class="sub">
                            Terpakai
                        </th>

                        <th class="sub">
                            Meter
                        </th>
                        <th class="sub">
                            M3
                        </th>
                        <th class="sub">
                            Ha
                        </th>
                    </tr>
                </thead>

                <tbody>
                    {{-- Record Time Sheet --}}
                    <tr>
                        <td>
                            {{ $timeSheet->start_time ? substr((string) $timeSheet->start_time, 0, 5) : '' }}
                        </td>

                        <td>
                            {{ $timeSheet->end_time ? substr((string) $timeSheet->end_time, 0, 5) : '' }}
                        </td>

                        <td>
                            {{ $formatNumber($timeSheet->total_hours) }}
                        </td>

                        <td>
                            {{ $formatNumber($timeSheet->hm_start) }}
                        </td>

                        <td>
                            {{ $formatNumber($timeSheet->hm_end) }}
                        </td>

                        <td>
                            {{ $formatNumber($timeSheet->total_hm) }}
                        </td>

                        <td>
                            {{ $formatNumber($timeSheet->fuel_used) }}
                        </td>

                        <td class="location-cell">
                            {{ $timeSheet->location ?? '' }}
                        </td>

                        <td class="activity-cell">
                            {{ $timeSheet->activity?->name ?? '' }}
                        </td>

                        <td>
                            {{ $productionMeter }}
                        </td>

                        <td>
                            {{ $productionM3 }}
                        </td>

                        <td>
                            {{ $productionHa }}
                        </td>
                    </tr>

                    {{-- Baris kosong supaya format mengikuti formulir asli --}}
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                            <td>&nbsp;</td>

                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- FOOTER + TANDA TANGAN --}}
        <div class="footer-area">
            <div class="notes">
                <div>
                    BBM dikirim hari ini :
                </div>

                <div>
                    Oli
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    :
                </div>
            </div>

            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-title">
                            Operator,
                        </div>

                        <div class="signature-space">
                            &nbsp;
                        </div>

                        <div class="signature-line">
                            &nbsp;
                        </div>

                        <div class="signature-name">
                            ({{ $timeSheet->operator?->name ?? '' }})
                        </div>
                    </td>

                    <td>
                        <div class="signature-title">
                            Pengawas
                        </div>

                        <div class="signature-space">
                            &nbsp;
                        </div>

                        <div class="signature-line">
                            &nbsp;
                        </div>

                        <div class="signature-name">
                            ({{ $timeSheet->user?->name ?? '' }})
                        </div>
                    </td>

                    <td>
                        <div class="signature-title">
                            Supervisor,
                        </div>

                        <div class="signature-space">
                            &nbsp;
                        </div>

                        <div class="signature-line">
                            &nbsp;
                        </div>

                        <div class="signature-name">
                            @if ($timeSheet->reviewer)
                                ({{ $timeSheet->reviewer->name }})
                            @else
                                (&nbsp;)
                            @endif
                        </div>
                    </td>

                    <td>
                        <div class="signature-title">
                            Superintendent,
                        </div>

                        <div class="signature-space">
                            &nbsp;
                        </div>

                        <div class="signature-line">
                            &nbsp;
                        </div>

                        <div class="signature-name">
                            (&nbsp;)
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</body>

</html>
