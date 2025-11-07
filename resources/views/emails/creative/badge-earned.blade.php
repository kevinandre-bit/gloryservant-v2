<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Badge Earned</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #dc2626;">🏆 Congratulations!</h2>
        
        <p>Hi {{ $person->firstname }},</p>
        
        <p>You've earned a new badge!</p>
        
        <div style="background: #fef3c7; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 10px;">🏆</div>
            <h3 style="margin: 0 0 10px 0; color: #92400e;">{{ $badge->name }}</h3>
            <p style="margin: 0; color: #78350f;">{{ $badge->description }}</p>
        </div>
        
        <p>Keep up the great work!</p>
        
        <p>
            <a href="{{ url('/personal/creative/dashboard') }}" 
               style="background: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                View Dashboard
            </a>
        </p>
        
        <p>Best regards,<br>Creative Team</p>
    </div>
</body>
</html>