<!DOCTYPE html>
<html>
<head>
    <title>Welcome to GloryServant</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}?v={{ filemtime(public_path('/assets/css/bootstrap.min.css')) }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/style_adm.css') }}?v={{ filemtime(public_path('/assets/css/style_adm.css')) }}">
</head>
<body>
    <p>Welcome to GloryServant! We're excited to introduce you to our new volunteer coordination system designed specifically for the Tabernacle of Glory Ministries.</p>

    <p>Your account has been successfully created. Here are your login details:</p>

    <p>🌟 <strong>Email:</strong> {{ $email }}<br>
    🔑 <strong>Password:</strong> {{ $password }}<br>
    🆔 <strong>IDNo:</strong> {{ $idno }}</p>

    <p>Log in here: <a href="https://gloryservant.com/login">GloryServant Volunteer Hub</a>. Once logged in, please change your password for security.</p>

    <p>GloryServant is your central hub for managing your volunteer activities, including:</p>
    <ul>
        <li>Profile management & team coordination</li>
        <li>Shift scheduling & clocking in</li>
        <li>Leave requests & attendance tracking</li>
        <li>Access to event planning & reports</li>
    </ul>

    <p>This platform is designed to help you serve with excellence and stay organized. If you have any questions, feel free to reach out to our support team.</p>

    <p>Thank you for being part of the GloryServant family!</p>

    <p>Warm regards,<br>
    The GloryServant Team</p>

    <p><strong>P.S.</strong> Don’t forget to change your password once you log in for added security! 😊</p>
</body>
</html>
