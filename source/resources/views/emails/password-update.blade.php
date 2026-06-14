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
                Your password for your <b>{{ config('app.name') }}</b> account has been <b>successfully updated</b>.
            </td>
        </tr>

        <tr>
            <td style="padding-bottom: 30px; font-size: 16px; color: #555555; line-height: 24px;">
                You can now log in to your account with your new password.
            </td>
        </tr>

        <tr>
            <td style="padding: 20px; background-color: #fce8e6; border: 1px solid #f9d8d6; color: #721c24; border-radius: 5px; font-size: 14px; line-height: 20px;">
                <strong style="font-weight: bold;">SECURITY ALERT:</strong> If you did <b>not</b> authorize this password change, please contact <b><a href="mailto:{{ $supportEmail ?? '#' }}" style="color:#721c24; text-decoration: underline;">our support team</a></b> immediately to secure your account.
            </td>
        </tr>

        <tr>
            <td style="padding-top: 25px; font-size: 16px; color: #555555; line-height: 24px;">
                Thank you for helping us keep your account safe.
                <br><br>
                The {{ config('app.name') }} Team
            </td>
        </tr>
    </table>
@endsection