<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation</title>
</head>
<body>
    <p>Dear {{ $booking->attendee_name }},</p>

    <p>Thank you for booking with us.</p>

    <h3 style="margin: 10px 0px;">Event Details</h3>
    <p style="margin: 10px 0px;">
        <strong>Event:</strong> {{ $event->name }}<br>
        <strong>Event Description:</strong> {{ $event->description }}<br>
        <strong>Event Duration:</strong> {{ $event->duration }} minutes
    </p>
    <br>
    <h3 style="margin: 10px 0px;">Booking Details</h3>
    <p style="margin: 10px 0px;">
        <strong>Attendee Name: </strong> {{$booking->attendee_name}} <br>
        <strong>Date: </strong> {{date('F j, Y',strtotime($booking->booking_date))}} <br>
        <strong>Time: </strong> {{date('g:i A',strtotime($booking->booking_time))}} <br>
        <strong>Booked at: </strong> {{date('F j, Y g:i A',strtotime($booking->created_at))}} <br>
    </p>
    <p>Please find your booking details attached as an .ics file.</p>

    
    <br>
    <p>Best regards,<br>Demo Booking</p>
</body>
</html>
