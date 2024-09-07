<x-guest-layout>
    <div class="mt-8 p-4 bg-white border rounded-lg">
        <h2 class="text-xl font-bold mb-4">Confirm To Update Your Booking</h2>
        <form action="{{ route('bookings.update', ['event' => $event->id, 'booking' => $booking->id]) }}" method="POST">
            @csrf
            <p><strong>Event:</strong> {{ $event->name }}</p>
            <p><strong>Date:</strong> {{ request('booking_date') }}</p>
            <p><strong>Time:</strong> {{ request('booking_time') }}</p>
            <p><strong>Timezone:</strong> {{ $selectedTimezoneName }} </p>
            <input type="hidden" name="booking_date" value="{{ request('booking_date') }}">
            <input type="hidden" name="booking_time" value="{{ request('booking_time') }}">
            <input type="hidden" name="timezone" value="{{ request('timezone') }}">

            <label for="attendee_name">Name:</label>
            <input type="text" name="attendee_name" id="attendee_name" value="{{ $booking->attendee_name ?? ""}}" required>

            <label for="attendee_email">Email:</label>
            <input type="email" name="attendee_email" id="attendee_email" value="{{ $booking->attendee_email ?? ""}}" required>


            <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">Confirm
                Booking</button>
        </form>
    </div>
</x-guest-layout>
