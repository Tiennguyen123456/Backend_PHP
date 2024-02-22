<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <h2>{{ $mailData['title'] }}</h2>

    <p>Dear User,</p>

    <p>To proceed with the password reset process, please follow the instructions below:</p>

    <ol>
        <li>Visit the password reset page by clicking on the following link: <a href="{{ $mailData['reset_password_url'] }}">Password Reset Link</a></li>
        <li>Once you're on the password reset page, you will be prompted to enter a new password for your account.</li>
    </ol>

    <p>If you did not initiate this password reset request or believe it was made in error, please disregard this email. Your current password will remain unchanged.</p>

    <p>For security purposes, please ensure that your new password is unique and not easily guessable.</p>

    <p>If you have any questions or require further assistance, please don't hesitate to contact our support team at <a href="mailto:[Your Support Email]">[Your Support Email]</a> or by replying to this email.</p>

    <p>Thank you for your attention to this matter.</p>

    <p>Best regards,</p>

    <p><em>Note: Please avoid sharing your password with anyone and ensure to keep it confidential.</em></p>
</body>
</html>