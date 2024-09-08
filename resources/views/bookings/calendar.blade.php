<x-guest-layout>
    <div class="container mx-auto py-8">
        @if (!request('booking_time'))
            <h1 class="text-2xl font-bold mb-6">Select a Time Slot for {{ $event->name }}</h1>

            <form id="booking_details_form" action="{{ route('bookings.create', $event->id) }}" method="GET">
                <div class="mb-4">
                    <label for="booking_timezone" class="block font-medium text-gray-700">Select Timezone:</label>
                    <select name="booking_timezone" id="booking_timezone" class="border rounded p-2" 
                        onchange="document.getElementById('booking_details_form').submit()">
                        @foreach ($timezones as $timezone)
                            <option value="{{ $timezone }}" {{ ($timezone === request('booking_timezone', 'UTC')) ? 'selected' : '' }}>
                                {{ $timezone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="booking_date" class="block font-medium text-gray-700">Select Date:</label>
                    <input type="date" name="booking_date" id="booking_date" class="border rounded p-2"
                        value="{{ $selectedDate }}" required 
                        onchange="document.getElementById('booking_details_form').submit()">
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($timeSlots as $time)
                    <div class="border p-4 rounded-lg 'bg-green-100' }}">
                        <span class="text-lg font-medium">{{ $time['time'] }}</span>
                        <form action="{{ route('bookings.create', $event->id) }}" method="GET" class="mt-2">
                            <input type="hidden" name="booking_date" value="{{ $selectedDate }}">
                            <input type="hidden" name="booking_timezone" value="{{ $selectedTimezone }}">
                            <input type="hidden" name="booking_time" value="{{ $time['time'] }}">
                            <button type="submit"
                                class="w-full px-4 py-2 rounded bg-blue-600 text-white {{ ($time['booked'] || $time['past']) ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                {{ ($time['booked'] || $time['past']) ? 'disabled' : '' }}>Select</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-8 p-4 bg-white border rounded-lg">
                <h2 class="text-xl font-bold mb-4">Confirm Your Booking</h2>
                <form action="{{ route('bookings.store') }}" method="POST">
                    @csrf
                    <p><strong>Event:</strong> {{ $event->name }}</p>
                    <p><strong>Date:</strong> {{ request('booking_date') }}</p>
                    <p><strong>Time:</strong> {{ request('booking_time') }}</p>
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <input type="hidden" name="booking_date" value="{{ request('booking_date') }}">
                    <input type="hidden" name="booking_time" value="{{ request('booking_time') }}">
                    <input type="hidden" name="booking_timezone" value="{{ request('booking_timezone') }}">

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
