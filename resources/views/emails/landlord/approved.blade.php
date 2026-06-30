<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Landlord Application Approved</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color:#111;">
    <h2>Hi {{ $user->name }},</h2>

    <p>Good news 🎉 Your landlord application for <b>Smart Rental</b> has been <b style="color:green;">APPROVED</b>.</p>

    <p>You can now log in and access your landlord dashboard to list rooms.</p>

    <p>
        <a href="{{ url('/login') }}"
           style="display:inline-block; padding:10px 16px; background:#111; color:#fff; text-decoration:none; border-radius:8px;">
            Login Now
        </a>
    </p>

    <br>
    <p>Regards,<br><b>Smart Rental Team</b></p>
</body>
</html>
