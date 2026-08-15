<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <title>Application Tracking OTP</title>

</head>

<body style="font-family: Arial, sans-serif; background:#f5f6fa; padding:30px;">

    <table width="100%"
           cellpadding="0"
           cellspacing="0">

        <tr>

            <td align="center">

                <table width="600"
                       cellpadding="0"
                       cellspacing="0"
                       style="background:#ffffff; border-radius:10px; padding:30px;">

                    <tr>

                        <td>

                            <h2 style="margin-top:0;">
                                Application Tracking
                            </h2>

                            <p>
                                Hello,
                            </p>

                            <p>
                                We received a request to track your job application.
                            </p>

                            <p>
                                Your verification code is:
                            </p>


                            <div style="
                                text-align:center;
                                margin:30px 0;
                            ">

                                <span style="
                                    display:inline-block;
                                    background:#f1f3f5;
                                    padding:15px 30px;
                                    border-radius:8px;
                                    font-size:32px;
                                    font-weight:bold;
                                    letter-spacing:8px;
                                ">

                                    {{ $otp }}

                                </span>

                            </div>


                            <p>
                                This OTP is valid for
                                <strong>{{ $expires }} minutes</strong>.
                            </p>


                            <p style="color:#777;">

                                If you did not request this code,
                                you can safely ignore this email.

                            </p>


                            <hr>


                            <p style="
                                font-size:12px;
                                color:#999;
                            ">

                                PT. Hermes Solusi Integrasi

                            </p>

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

</body>
</html>