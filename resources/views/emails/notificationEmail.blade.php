<!DOCTYPE html>
<html>
<head>
    <title>New Notification</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}?v={{ filemtime(public_path('/assets/css/bootstrap.min.css')) }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/style_adm.css') }}?v={{ filemtime(public_path('/assets/css/style_adm.css')) }}">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        You’ve received a new notification from GloryServant.
    </div>
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 20px 10px;">
                <table width="100%" style="max-width: 600px;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td bgcolor="#ffffff" align="left" style="padding: 30px; border-radius: 8px; color: #444; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; line-height: 28px;">
                            <div class="logo align-center" style="text-align: center; margin: 30px 0;"><img src="{{ asset('/assets/images/img/logo4.png') }}" alt="Workday"></div>
                            <h2 style="font-size: 24px; margin-top: 0;">Hello {{ $notificationData['target_name'] ?? 'User' }},</h2>
                            <p style="margin: 20px 0;">
                                You have received a new notification:
                            </p>

                            <p style="margin: 20px 0; font-size: 16px;">
                                <strong>Message:</strong> {{ $notificationData['message'] }}
                            </p>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $notificationData['url'] ?? '#' }}" 
                                   style="background-color: #FFA73B; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-size: 18px; font-family: Helvetica, Arial, sans-serif;">
                                   View Notification
                                </a>
                            </div>

                            <p style="font-size: 14px; color: #999;">If the button above doesn't work, paste this link in your browser:</p>
                            <p style="font-size: 14px; color: #FFA73B; word-break: break-all;">
                                {{ $notificationData['url'] ?? '#' }}
                            </p>

                            <p style="margin-top: 40px;">Thank you,<br>GloryServant Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
