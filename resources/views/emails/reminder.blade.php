<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #444;
        }
        .content {
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Reminder for Your Upcoming Event</h1>
    </div>
    <div class="content">
        <p>Hello {{ $booking->attendee_name }},</p>
        <p>This is a friendly reminder for your upcoming event:</p>
        <p><strong>{{ $booking->event->name }}</strong></p>
        <p>Event Date: {{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}</p>
        <p>Event Time: {{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}</p>
        <p>If you have any questions, feel free to contact us.</p>
        <p>Thank you!</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</div>
</body>
</html>
