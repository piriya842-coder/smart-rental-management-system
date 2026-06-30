<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Landlord Application Rejected</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.6; color:#111;">
  <h2 style="margin:0 0 12px 0;">Smart Rental — Landlord Application Rejected</h2>

  <p>Hi {{ $landlord->name }},</p>

  <p>
    We’re sorry to inform you that your landlord application has been <strong>rejected</strong>.
  </p>

  <div style="padding:12px 14px; background:#fff2f2; border:1px solid #ffd0d0; border-radius:10px;">
    <strong>Reason:</strong><br>
    {{ $reason ?: 'No reason provided.' }}
  </div>

  <p style="margin-top:16px;">
    If you would like to appeal or update your application details, please contact the admin.
  </p>

  <p>Thank you,<br>Smart Rental Team</p>
</body>
</html>
