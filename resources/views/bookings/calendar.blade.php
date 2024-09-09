<x-guest-layout>
    <div class="container mx-auto py-8">
        @if (!request('booking_time'))
            <h1 class="text-2xl font-bold mb-6">Select a Time Slot for {{ $event->name }}</h1>

            <div class="mb-4">
                <form action="{{ route('bookings.create', $event->id) }}" method="GET" id="booking-form">
                    <label for="booking_date" class="block font-medium text-gray-700">Select Date:</label>
                    <input type="date" name="booking_date" id="booking_date" class="border rounded p-2 w-full"
                        value="{{ $selectedDate }}" min="{{date('Y-m-d')}}"required>

                    <label for="timezone" class="block mt-4 font-medium text-gray-700">Select Time Zone:</label>
                    <select name="timezone" id="timezone" class="border rounded p-2 w-full" required>
                        @foreach ($timeZones as $timezone)
                            <option value="{{ $timezone }}" 
                                @if($selectedTimeZone == $timezone) selected @endif>
                                {{ $timezone }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="my-3 mx-auto px-4 py-2 bg-blue-600 text-white rounded">Change Date</button>
                </form>
            </div>
            @if(empty($timeSlots))
                <div class="w-full text-center">
                    <span class="text-lg font-medium">No Booking Slots</span>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($timeSlots as $time)
                            <div class="border p-4 rounded-lg text-center">
                                <span class="text-lg font-medium">{{ date('g:i A',strtotime($time['time'])) }}</span>
                                <form action="{{ route('bookings.create', $event->id) }}" method="GET" class="mt-2">
                                    <input type="hidden" name="booking_date" value="{{ $selectedDate }}">
                                    <input type="hidden" name="booking_time" value="{{ date('H:i',strtotime($time['time'])) }}">
                                    <input type="hidden" name="timezone" value="{{ request('timezone') }}">
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded time-slot-button">Select</button>
                                </form>
                            </div>
                        @endforeach
                </div>
            @endif
        @else
            <div class="mt-8 p-4 bg-white border rounded-lg">
                <h2 class="text-xl font-bold mb-4">Confirm Your Booking</h2>
                <form action="{{ route('bookings.store', $event->id) }}" method="POST">
                    @csrf
                    <p><strong>Event:</strong> {{ $event->name }}</p>
                    <p><strong>Date:</strong> {{ date('F j, Y', strtotime(request('booking_date'))) }}</p>
                    <p><strong>Time:</strong> {{ date('g:i A',strtotime(request('booking_time'))) }}</p>
                    <input type="hidden" name="booking_date" value="{{ request('booking_date') }}">
                    <input type="hidden" name="booking_time" value="{{ request('booking_time') }}">
                    <input type="hidden" name="timezone" value="{{ $selectedTimeZone }}">
                    <div class="mt-5">
                        <label for="attendee_name" class="block font-medium text-gray-700 w-3/12">Name:</label>
                        <input type="text" name="attendee_name" id="attendee_name" class="border rounded p-2 w-full" required>

                        <label for="attendee_email" class="block font-medium text-gray-700 w-3/12 mt-2">Email:</label>
                        <input type="email" name="attendee_email" id="attendee_email"  class="border rounded p-2 w-full" required>

                        <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">Confirm
                            Booking</button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <script>
        document.getElementById('timezone').addEventListener('change', function() {
            document.getElementById('booking-form').submit();

            const buttons = document.querySelectorAll('.time-slot-button');
            buttons.forEach(button => {
                button.disabled = true;
                button.classList.add('bg-gray-400');
            });
        });
    </script>
</x-guest-layout>
