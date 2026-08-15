<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Interview Invitation</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 650px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0;
            color: #6777ef;
        }

        .info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .info table {
            width: 100%;
        }

        .info td {
            padding: 6px 0;
        }

        .label {
            font-weight: bold;
            width: 150px;
        }

        .footer {
            margin-top: 30px;
            color: #777;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <h2>Interview Invitation</h2>
    </div>

    <p>
        Dear <strong>{{ $application->applicant->name }}</strong>,
    </p>

    <p>
        Thank you for your application for the
        <strong>{{ $application->job->title }}</strong>
        position at PT. Hermes Solusi Integrasi.
    </p>

    <p>
        We are pleased to inform you that you have been selected
        to proceed to the interview stage.
    </p>

    <div class="info">

        <table>

            <tr>
                <td class="label">
                    Name
                </td>

                <td>
                    {{ $application->applicant->name }}
                </td>
            </tr>

            <tr>
                <td class="label">
                    Position
                </td>

                <td>
                    {{ $application->job->title }}
                </td>
            </tr>

            <tr>
                <td class="label">
                    Date
                </td>

                <td>
                    {{ $application->interview_date
                        ? $application->interview_date->format('d F Y')
                        : '-' }}
                </td>
            </tr>

            <tr>
                <td class="label">
                    Time
                </td>

                <td>
                    {{ $application->interview_time
                        ? \Carbon\Carbon::parse($application->interview_time)->format('H:i')
                        : '-' }}
                </td>
            </tr>

            <tr>
                <td class="label">
                    Location
                </td>

                <td>
                    {{ $application->interview_location ?? '-' }}
                </td>
            </tr>

        </table>

    </div>

    <p>
        Please make sure to be available at the scheduled time.
    </p>

    <p>
        We look forward to meeting you.
    </p>

    <p>
        Best regards,<br>
        <strong>Talent Acquisition</strong><br>
        PT. Hermes Solusi Integrasi
    </p>

    <div class="footer">
        This email was sent automatically from the recruitment system.
    </div>

</div>

</body>
</html>