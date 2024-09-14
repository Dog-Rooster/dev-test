<x-guest-layout>
    <div class="container mx-auto py-8">
        @if (!request('booking_time'))
            <h1 class="text-2xl font-bold mb-6">Select a Time Slot for {{ $event->name }}</h1>

            <form method="GET" action="{{ route('bookings.create', $event->id) }}">
                <div class="flex items-center space-x-4 mb-5">
                    <div>
                        <label for="booking_date" class="block font-medium text-gray-700">Select Date:</label>
                        <input type="date" name="booking_date" id="booking_date" class="border rounded p-2" value="{{ $selectedDate }}" required>
                    </div>
                
                    <div>
                        <label for="time_zone" class="block font-medium text-gray-700">Timezone:</label>
                        <select name="time_zone" id="time_zone" class="border rounded p-2 w-48">
                            @foreach ($timeZones as $timeZone)
                            <option value="{{ $timeZone }}" @if(request('time_zone', 'UTC') === $timeZone) selected @endif>
                                {{ $timeZone }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 mt-5 bg-blue-600 text-white rounded self-center">Change Date</button>
                </div>
            </form>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($timeSlots as $time)
                    <div class="border p-4 rounded-lg 'bg-green-100' text-center">
                        <span class="text-lg font-medium">{{ $time['time'] }}</span>
                        <form action="{{ route('bookings.create', $event->id) }}" method="GET" class="mt-2">
                            <input type="hidden" name="booking_date" value="{{ $selectedDate }}">
                            <input type="hidden" name="booking_time" value="{{ $time['time'] }}">
                            <input type="hidden" name="time_zone" value="{{ request('time_zone', 'UTC') }}">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-blue-600 {{ $time['opacity'] }} text-white rounded" {{ $time['available'] }}>{{ $time['text'] }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            @if ($errors->any())
                <div class="bg-red-500 text-white p-4 rounded mb-4">
                    <strong>Notice:</strong>
                    <ul class="mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mt-8 p-4 bg-white border rounded-lg">
                <h2 class="text-xl font-bold mb-4">Confirm Your Booking</h2>
                <form action="{{ route('bookings.store', $event->id) }}" method="POST">
                    @csrf
                    <p><strong>Event:</strong> {{ $event->name }}</p>
                    <p><strong>Date:</strong> {{ request('booking_date') }}</p>
                    <p><strong>Time:</strong> {{ request('booking_time') }}</p>
                    
                    <input type="hidden" name="booking_date" value="{{ request('booking_date') }}">
                    <input type="hidden" name="booking_time" value="{{ request('booking_time') }}">
                    <input type="hidden" name="time_zone" value="{{ request('time_zone') }}">

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
