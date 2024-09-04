<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
</head>
<body>
<h1>Booking Confirmation</h1>
<p>Dear {{ $booking->attendee_name }},</p>

<p>Thank you for booking the event: <strong>{{ $booking->event->name }}</strong>.</p>

<p><strong>Booking Details:</strong></p>
<ul>
    <li>Date: {{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}</li>
    <li>Time: {{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}</li>
    <li>Time Zone: {{ $userTimeZone }}</li>
</ul>

<p>Please find the attached calendar invitation (.ics) file for your reference.</p>

<p>We look forward to seeing you at the event!</p>

<p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
