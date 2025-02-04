<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Please click the link below to verify your email:</p>
    <a href="{{ url('/api/verify-email/' . $user->email_verification_code) }}">Verify Email</a>
    <p>If you did not register, you can ignore this email.</p>
</body>
</html>