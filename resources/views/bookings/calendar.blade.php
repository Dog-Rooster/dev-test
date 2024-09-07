<x-guest-layout>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Select a Time Slot for {{ $event->name }}</h1>

        <div class="mb-4">
            <!-- Visible date input -->
            <label for="booking_date" class="block font-medium text-gray-700">Select Date:</label>
            <input type="date" name="booking_date" id="booking_date" class="border rounded p-2"
                value="{{ $selectedDate }}" required>

            <!-- Hidden input to hold the date for submission -->
            <input type="hidden" name="booking_date_hidden" id="booking_date_hidden" value="{{ $selectedDate }}">
            {{-- <button type="submit" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded">Change Date</button> --}}

            <!-- Timezone Picker -->
            <label for="timezone" class="block font-medium text-gray-700 mt-4">Select Timezone:</label>
            <select id="timezone_picker" class="border rounded p-2 w-full" name="timezone" required></select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="timeslot_container">
            @foreach ($timeSlots as $time)
                <div class="border p-4 rounded-lg">
                    <span class="text-lg font-medium">{{ $time['time'] }}</span>
                    <form action="{{ route($routeName, $routeParams) }}" method="GET" class="mt-2">
                        <!-- Hidden inputs for date and time -->
                        <input type="hidden" name="booking_date" id="booking_date_timeslot" value="{{ $selectedDate }}">
                        <input type="hidden" name="booking_time" value="{{ $time['time'] }}">
                        <input type="hidden" name="timezone" value="">

                        <!-- Disable the button if the time is already booked -->
                        <button type="submit"
                            class="w-full px-4 py-2 rounded text-white {{ $time['is_booked'] ? 'bg-gray-400' : 'bg-blue-600' }}"
                            @disabled($time['is_booked'])>
                            {{ $time['is_booked'] ? 'Booked' : 'Select' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

    </div>

    <script type="module">
        $(document).ready(function() {
            const defaultTimezone = '{{ isset($booking) ? $booking->timezone : "254" }}'; // Use stored timezone if in edit state, otherwise use Asia/Manila
            const timezonePicker = $('#timezone_picker');
            const selectedTimezoneInput = $('input[type="hidden"][name="timezone"]');

            // Fetch the timezones from the controller
            function fetchTimezones() {
                $.ajax({
                    url: '/timezones', // Assuming this route maps to the generateTimeZones method
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        data.forEach(timezone => {
                            let option = document.createElement('option');
                            option.value = timezone.value; // Adjust this according to your data structure
                            option.text = timezone.text; // Adjust this according to your data structure
                            timezonePicker.append(option);
                        });

                        // Set the default timezone
                        timezonePicker.val(defaultTimezone).trigger('change');
                        selectedTimezoneInput.val(defaultTimezone).trigger('change');

                        // Initialize Select2
                        timezonePicker.select2({
                            placeholder: "Select a timezone",
                        });

                        // Handle selection change
                        timezonePicker.on('change', function() {
                            selectedTimezoneInput.val(timezonePicker.val());
                        });
                    },
                    error: function(error) {
                        console.error('Error fetching timezones:', error);
                    }
                });
            }

            // Initialize timezones
            fetchTimezones();

            // Get the booking date
            $('#booking_date').on('change', function() {
                // Get the newly selected date from the date input field
                const selectedDate = $('#booking_date').val();

                // Update all hidden date inputs in the looped forms
                $('input[type="hidden"][name="booking_date"]').val(selectedDate);

                // Fetch the time slots for the selected date
                $.ajax({
                    url: '/timeslots', // Update this URL to match your route
                    type: 'GET',
                    data: { date: selectedDate },
                    success: function(response) {
                        const timeSlots = response;

                        // Clear the current time slots
                        $('#timeslot_container').empty();

                        // Loop through the new time slots and add them to the container
                        timeSlots.forEach(function(slot) {
                            // Determine if the slot is disabled (already booked)
                            let isDisabled = slot.is_booked ? 'disabled' : '';

                            // Add the new time slot to the UI
                            const timeSlotHtml = `
                                <div class="border p-4 rounded-lg">
                                    <span class="text-lg font-medium">${slot.time}</span>
                                    <form action="{{ route($routeName, $routeParams) }}" method="GET" class="mt-2">
                                        <input type="hidden" name="booking_date" value="${selectedDate}">
                                        <input type="hidden" name="booking_time" value="${slot.time}">
                                        <input type="hidden" name="timezone" value="${timezonePicker.val()}">
                                        <button type="submit" class="w-full px-4 py-2 rounded text-white ${isDisabled ? 'bg-gray-400' : 'bg-blue-600'}">
                                            ${isDisabled ? 'Booked' : 'Select'}
                                        </button>
                                    </form>
                                </div>
                            `;

                            // Append the time slot to the container
                            $('#timeslot_container').append(timeSlotHtml);
                        });

                        // Re-apply the values in timezone input hidden
                        // selectedTimezoneInput.val(timezonePicker.val());

                    },
                    error: function(error) {
                        console.error('Error fetching time slots:', error);
                    }
                });
            });
        });

    </script>
</x-guest-layout>
