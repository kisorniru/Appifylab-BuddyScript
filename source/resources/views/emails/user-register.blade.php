@extends('emails.layout')

@section('content')
    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="border-collapse: collapse;">

        <tr>
            <td style="padding-bottom: 20px; font-size: 20px; font-weight: bold; color: #333333; line-height: 28px;">
                Hello <b>{{ $user->name }}</b>,
            </td>
        </tr>

        <tr>
            <td style="padding-bottom: 25px; font-size: 16px; color: #555555; line-height: 24px;">
                Welcome to <b>{{ config('app.name') }}</b>! Your account has been <b>successfully verified</b>. We are excited for you to join our community and start exploring.
            </td>
        </tr>

        <tr>
            <td style="padding-bottom: 30px; font-size: 16px; color: #555555; line-height: 24px;">
                Please go to your application and click <b>log in</b> and <b>get started</b> on your journey.
            </td>
        </tr>

        <tr>
            <td style="padding-top: 10px; border-top: 1px solid #eeeeee;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding-top: 20px; font-size: 15px; color: #555555; line-height: 22px;">
                            If you run into any issues or have questions, please don't hesitate to reach out to our dedicated support team at:
                            <br><br>
                            <a href="mailto:{{ $supportEmail }}" style="color:#007bff; text-decoration:none; font-weight: bold;">{{ $supportEmail }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding-top: 25px; font-size: 16px; color: #555555; line-height: 24px;">
                We look forward to seeing what you accomplish!
                <br><br>
                Best regards,
                <br>
                The <b>{{ config('app.name') }}</b> Team
            </td>
        </tr>

    </table>
@endsection