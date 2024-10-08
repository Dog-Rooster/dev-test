<x-guest-layout>
    <div class="container mx-auto py-8">
        @if (!request('booking_time'))
            <h1 class="text-2xl font-bold mb-6">Select a Time Slot for {{ $event->name }}</h1>

            <div class="mb-4">
                <form action="{{ route('bookings.create', $event->id) }}" method="GET">
                    <label for="booking_date" class="block font-medium text-gray-700">Select Date:</label>
                    <input type="date" name="booking_date" id="booking_date" class="border rounded p-2"
                        value="{{ $selectedDate }}" required>
                    <button type="submit" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded">Change Date</button>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($timeSlots as $time)
                    <div class="border p-4 rounded-lg 'bg-green-100' }}">
                        <span class="text-lg font-medium">{{ $time['time'] }}</span>
                        <form action="{{ route('bookings.create', $event->id) }}" method="GET" class="mt-2">
                            <input type="hidden" name="booking_date" value="{{ $selectedDate }}">
                            <input type="hidden" name="booking_time" value="{{ $time['time'] }}">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded">Select</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-8 p-4 bg-white border rounded-lg">
                <h2 class="text-xl font-bold mb-4">Confirm Your Booking</h2>
                <form action="{{ route('bookings.store', $event->id) }}" method="POST">
                    @csrf
                    <p><strong>Event:</strong> {{ $event->name }}</p>
                    <p><strong>Date:</strong> {{ request('booking_date') }}</p>
                    <p><strong>Time:</strong> {{ request('booking_time') }}</p>
                    <input type="hidden" name="booking_date" value="{{ request('booking_date') }}">
                    <input type="hidden" name="booking_time" value="{{ request('booking_time') }}">

                    <label for="attendee_name">Name:</label>
                    <input type="text" name="attendee_name" id="attendee_name" required>

                    <label for="attendee_email">Email:</label>
                    <input type="email" name="attendee_email" id="attendee_email" required>

                    <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">Confirm
                        Booking</button>
                </form>
            </div>
        @endif
    </div>
</x-guest-layout>
