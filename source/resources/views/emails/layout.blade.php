<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $header ?? config('app.name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0;">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f7f7f7;">
        <tr>
            <td align="center" style="padding: 40px 0;">

                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-collapse: collapse;">

                    <tr>
                        <td class="email-header" style="padding: 32px 32px 16px 32px; border-bottom: 1px solid #eeeeee;">
                            <h2 style="margin: 0; font-size: 24px; color: #333333;">{{ $header ??  config('app.name')  }}</h2>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-body" style="padding: 32px;">
                            @yield('content')
                        </td>
                    </tr>

                    <tr>
                        <td class="email-footer" style="padding: 16px 32px 32px 32px; border-top: 1px solid #eeeeee; text-align: center; color: #888888; font-size: 13px;">
                            &copy; {{  date('Y')  }} <b>{{  config('app.name')  }}</b>. All rights reserved.<br>
                            <span style="font-size: 11px; color: #aaaaaa; display: block; margin-top: 5px;">
                                This is an automated email, please do not reply.
                            </span>
                        </td>
                    </tr>

                </table>
                </td>
        </tr>
    </table>
    </body>
</html>