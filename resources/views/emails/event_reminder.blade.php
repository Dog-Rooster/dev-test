<!DOCTYPE html>
<html>
<head>
    <title>Event Reminder</title>
</head>
<body>
    <h1>Event Reminder</h1>
    <p>Hello {{ $booking->attendee_name }},</p>
    <p>This is a reminder that your event is starting in 1 hour:</p>
    <p>Event: {{ $booking->event->name }}</p>
    <p>Date: {{ $booking->booking_date }}</p>
    <p>Time: {{ $booking->booking_time }}</p>
    <p>Location: {{ $booking->event->location }}</p>
    <p>Thank you!</p>
</body>
</html>