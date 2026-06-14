@extends('emails.layout')

@section('content')
    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="border-collapse: collapse;">

        <tr>
            <td style="padding-bottom: 20px; font-size: 18px; color: #333333; line-height: 26px;">
                Hello <b>{{ " $user->name " }}</b>,
            </td>
        </tr>

        <tr>
            <td style="padding-bottom: 25px; font-size: 16px; color: #555555; line-height: 24px;">
                @if($reason === 'registration')
                    Welcome to our community! Thank you for choosing us. To get started, please use the verification passcode to confirm your account.
                @elseif($reason === 'forgot-password')
                    We received a request to reset the password for your account. If you made this request, please use the passcode to proceed.
                @endif
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 0 0 30px 0;">
                <table cellpadding="0" cellspacing="0" border="0" role="presentation" style="width: 100%; max-width: 300px; background-color: #f8f9fa; border-radius: 5px; border-left: 4px solid #007bff;">
                    <tr>
                        <td align="center" style="padding: 15px 15px 5px 15px;">
                            <div style="font-size: 14px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">
                                @if($reason === 'registration')
                                    Your Verification Passcode
                                @else
                                    Your Reset Passcode
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 5px 15px 15px 15px;">
                            <div style="font-size: 32px; font-weight: bold; color: #007bff; letter-spacing: 3px;">
                                <b>{{ " $passcode " }}</b>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        @if($reason === 'forgot-password')
        <tr>
            <td style="padding: 15px 0 15px 0; font-size: 14px; color: #777777; line-height: 20px; text-align: center;">
                If you did <b>not</b> request a password reset, you can safely ignore this email. Your password will remain unchanged.
            </td>
        </tr>
        @endif

        <tr>
            <td style="padding: 20px; background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; border-radius: 5px; margin: 20px 0; font-size: 14px; line-height: 20px;">
                <strong style="font-weight: bold;">Important:</strong> For your security, this code will expire in <b>{{ (int)config('app.passcode_ttl_minutes_for_registration') }} minutes</b>. Please use it as soon as possible.
            </td>
        </tr>
    </table>
@endsection