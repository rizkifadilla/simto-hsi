<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Attendance Timesheet</title>

    <style>

        /* =========================================================
           PAGE
        ========================================================= */

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #222;
            margin: 0;
            padding: 0;
        }


        /* =========================================================
           HEADER
        ========================================================= */

        .company-name {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .company-address {
            font-size: 9px;
            color: #555;
            margin-bottom: 8px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 8px 0;
        }


        /* =========================================================
           EMPLOYEE INFORMATION
        ========================================================= */

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-table td {
            border: none;
            padding: 1px;
            vertical-align: middle;
        }

        .info-label {
            width: 110px;
            font-weight: bold;
        }

        .info-value {
            padding-left: 1px !important;
        }


        /* =========================================================
           SUMMARY
        ========================================================= */

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .summary-table td {
            width: 16.66%;
            border: 1px solid #d9d9d9;
            text-align: center;
            padding: 1px;
        }

        .summary-header {
            background: #1F4E79;
            color: #FFFFFF;
            font-weight: bold;
            font-size: 9px;
            padding: 3px 1px !important;
        }

        .summary-number {
            background: #FFFFFF;
            color: #000000;
            font-size: 15px;
            font-weight: bold;
            padding: 3px 1px !important;
        }


        /* =========================================================
           ATTENDANCE TABLE
        ========================================================= */

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table th {
            background: #1F4E79;
            color: #FFFFFF;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            border: 1px solid #999;
        }

        .attendance-table td {
            border: 1px solid #BFBFBF;
            padding: 3px 5px;
            vertical-align: middle;
        }


        /* =========================================================
           ALIGNMENT
        ========================================================= */

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }


        /* =========================================================
           WEEKEND
        ========================================================= */

        .weekend {
            background: #E7E6E6 !important;
            font-weight: bold;
        }


        /* =========================================================
           PERMIT / LEAVE / SICK
        ========================================================= */

        .special-day {
            background: #FCE5CD !important;
            font-weight: bold;
        }


        /* =========================================================
           ABSENT
        ========================================================= */

        .absent-day {
            background: #FFFFFF;
        }


        /* =========================================================
           PRINT
        ========================================================= */

        @media print {

            body {
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

        }

    </style>

</head>


<body>


    {{-- =========================================================
         COMPANY
    ========================================================= --}}

    <div class="company-name">
        {{ config('app.company_name', 'PT. Hermes Solusi Integrasi') }}
    </div>

    <div class="company-address">
        {{ config(
            'app.company_address',
            '88@Kasablanka Office Tower, Lantai 3, Unit A Jl. Kasablanka Kav. 88, DKI Jakarta, 12870'
        ) }}
    </div>


    {{-- =========================================================
         TITLE
    ========================================================= --}}

    <div class="title">
        ATTENDANCE TIMESHEET
    </div>


    {{-- =========================================================
         EMPLOYEE INFORMATION
    ========================================================= --}}

    <table class="info-table">

        <tr>

            <td class="info-label">
                Consultant Name
            </td>

            <td class="info-value">
                : {{ $employee->full_name ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Employee ID
            </td>

            <td class="info-value">
                : {{ $employee->employee_id ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Role
            </td>

            <td class="info-value">
                : {{ $employee->position ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Client
            </td>

            <td class="info-value">
                : {{ $employee->client->name ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Client Address
            </td>

            <td class="info-value">
                : {{ $employee->client->address ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Periode
            </td>

            <td class="info-value">
                : {{ $periode }}
            </td>

        </tr>

    </table>


    {{-- =========================================================
         SUMMARY
    ========================================================= --}}

    <table class="summary-table">

        <tr>

            <td class="summary-header">
                Total Days
            </td>

            <td class="summary-header">
                Present
            </td>

            <td class="summary-header">
                Permit
            </td>

            <td class="summary-header">
                Leave
            </td>

            <td class="summary-header">
                Sick
            </td>

            <td class="summary-header">
                Absent
            </td>

        </tr>


        <tr>

            <td class="summary-number">
                {{ $totalDays }}
            </td>

            <td class="summary-number">
                {{ $totalPresent }}
            </td>

            <td class="summary-number">
                {{ $totalPermit }}
            </td>

            <td class="summary-number">
                {{ $totalLeave }}
            </td>

            <td class="summary-number">
                {{ $totalSick }}
            </td>

            <td class="summary-number">
                {{ $totalAbsent }}
            </td>

        </tr>

    </table>


    {{-- =========================================================
         CLIENT WORKING TIME
    ========================================================= --}}

    @php

        $client = $employee->client;

        $timeIn = $client && $client->check_in_time
            ? \Carbon\Carbon::parse($client->check_in_time)->format('H:i')
            : '-';

        $timeOut = $client && $client->check_out_time
            ? \Carbon\Carbon::parse($client->check_out_time)->format('H:i')
            : '-';

        $workingHours = '-';

        if (
            $client &&
            $client->check_in_time &&
            $client->check_out_time
        ) {

            $minutes = \Carbon\Carbon::parse(
                $client->check_out_time
            )->diffInMinutes(
                \Carbon\Carbon::parse(
                    $client->check_in_time
                )
            );

            $workingHours = sprintf(
                '%d:%02d',
                intdiv($minutes, 60),
                $minutes % 60
            );

        }

    @endphp


    {{-- =========================================================
         WORKING TIME INFO
    ========================================================= --}}

    <table class="info-table">

        <tr>

            <td class="info-label">
                Client Check In
            </td>

            <td class="info-value">
                : {{ $timeIn }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Client Check Out
            </td>

            <td class="info-value">
                : {{ $timeOut }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Working Hours
            </td>

            <td class="info-value">
                : {{ $workingHours }}
            </td>

        </tr>

    </table>


    {{-- =========================================================
         ATTENDANCE TABLE
    ========================================================= --}}

    <table class="attendance-table">

        <thead>

            <tr>

                <th style="width: 35px;">
                    No.
                </th>

                <th style="width: 75px;">
                    Date
                </th>

                <th style="width: 65px;">
                    Day
                </th>

                <th style="width: 60px;">
                    Time In
                </th>

                <th style="width: 60px;">
                    Time Out
                </th>

                <th style="width: 80px;">
                    Working Hours
                </th>

                <th>
                    Task
                </th>

            </tr>

        </thead>


        <tbody>

            @php
                $no = 1;
            @endphp


            @foreach($period as $date)

                @php

                    $key = $date->format('Y-m-d');

                    $att = $attendance->get($key);

                    $isWeekend = in_array(
                        $date->dayOfWeek,
                        [0, 6]
                    );

                    $hasWork = $att && $att->check_in;

                    $task = $att
                        ? trim($att->task ?? '')
                        : '';

                    $taskLower = strtolower($task);

                    /*
                     * Tentukan status hari
                     */

                    if ($isWeekend) {

                        $rowClass = 'weekend';

                        if (!$task) {

                            $task = $date->dayOfWeek === 6
                                ? 'Saturday'
                                : 'Sunday';

                        }

                    } elseif ($att && $taskLower === 'izin') {

                        $rowClass = 'special-day';

                    } elseif ($att && $taskLower === 'cuti') {

                        $rowClass = 'special-day';

                    } elseif ($att && $taskLower === 'sakit') {

                        $rowClass = 'special-day';

                    } elseif (!$att) {

                        $rowClass = 'absent-day';

                        $task = 'Absent';

                    } elseif (!$hasWork) {

                        $rowClass = 'absent-day';

                        if (!$task) {
                            $task = 'Absent';
                        }

                    } else {

                        $rowClass = '';

                    }


                    /*
                     * Waktu kerja hanya tampil
                     * jika benar-benar hadir
                     */

                    if ($hasWork) {

                        $rowTimeIn = $timeIn;

                        $rowTimeOut = $timeOut;

                        $rowWorkingHours = $workingHours;

                    } else {

                        $rowTimeIn = '';

                        $rowTimeOut = '';

                        $rowWorkingHours = '';

                    }

                @endphp


                <tr class="{{ $rowClass }}">

                    <td class="text-center">
                        {{ $no }}
                    </td>


                    <td class="text-center">

                        {{ $date->format('d M Y') }}

                    </td>


                    <td class="text-center">

                        {{ $date->format('l') }}

                    </td>


                    <td class="text-center">

                        {{ $rowTimeIn }}

                    </td>


                    <td class="text-center">

                        {{ $rowTimeOut }}

                    </td>


                    <td class="text-center">

                        {{ $rowWorkingHours }}

                    </td>


                    <td class="text-left">

                        {{ $task ?: '-' }}

                    </td>

                </tr>


                @php
                    $no++;
                @endphp

            @endforeach

        </tbody>

    </table>


    {{-- =========================================================
         PRINT / PDF
    ========================================================= --}}

    <script>

        window.onload = function () {

            window.print();

        };

    </script>


</body>

</html>