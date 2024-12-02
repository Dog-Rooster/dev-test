<x-guest-layout>
    <div class="container mx-auto py-8">
        <div class="mt-8 p-4 bg-white border rounded-lg">
            <h1 class="text-2xl font-bold mb-6">Book {{  $event->name }}</h1>
            <form action="{{ route('bookings.store', $event->id ) }}" method="POST" id="booking-form" x-data="bookingFormHandler()" @submit.prevent="confirmBookingDetails">
                @csrf
                {{-- Guest Details--}}
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <x-form-group :label="'Name'" :id="'name'" :inputData="array(
                        'type' => 'text',
                        'name' => 'attendee_name',
                        'id' => 'name',
                        'required' => 'true',
                        'value' => old('attendee_name'))" />
                    <x-form-group :label="'Email'" :id="'email'" :inputData="array(
                        'type' => 'email',
                        'name' => 'attendee_email',
                        'id' => 'email',
                        'required' => 'true',
                        'value' => old('attendee_email'))" />
                </div>

                {{-- Booking Details--}}
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <x-form-group :label="'Date'" :id="'date'" wrapperClass="sm:col-span-2 sm:col-start-1">
                        <livewire:booking.date-and-time-picker
                            :type="'date'"
                            :name="'booking_date'"
                            :id="'booking-date'"
                            :required="true"
                            :currentValue="old('booking_date')"
                            :validationErrors="$errors->first('booking_date') ? true : ''"
                            :message="$errors->first('booking_date') ?? ''" />
                    </x-form-group>

                    <x-form-group :label="'Time'" :id="'time'"  wrapperClass="sm:col-span-2">
                        <livewire:booking.date-and-time-picker
                            :type="'time'"
                            :name="'booking_time'"
                            :id="'booking-time'"
                            :required="true"
                            :currentValue="old('booking_time')"
                            :validationErrors="$errors->first('booking_time') ? true : ''"
                            :message="$errors->first('booking_time') ?? ''" />
                    </x-form-group>

                    <x-form-group :label="'Timezone'" :id="'timezone'"  wrapperClass="sm:col-span-2">
                        <div class="mt-2">
                            <select
                                id="timezone"
                                name="timezone"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" required>
                                <option value="">-- Select Timezone --</option>
                                @foreach (\DateTimeZone::listIdentifiers() as $timezone)
                                    <option value="{{ $timezone }}" {{ old('timezone') == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($errors->first('timezone'))
                            <x-alert :type="'danger'">
                                {{ $errors->first('timezone') }}
                            </x-alert>
                        @endif
                    </x-form-group>
                </div>

                @if($errors->has('schedule_conflict'))
                    <x-alert :type="'danger'">
                        {{ $errors->first('schedule_conflict') }}
                    </x-alert>
                @endif

                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <a href="{{ route('events.index') }}" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                </div>

                <!-- Confirmation Modal -->
                <div
                    x-show="showModal"
                    x-transition
                    x-cloak
                    class="fixed inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50"
                >
                    <div class="bg-white p-4 rounded-md shadow-md max-w-md w-full" @click.away="showModal = false">
                        <h2 class="text-lg font-bold mb-4">Confirm Your Booking</h2>
                        <p>Please check if all the details are correct.</p>
                        <ul class="my-4">
                            <li><strong>Event Name:</strong> <span>{{ $event->name }}</span></li>
                            <li><strong>Attendee Name:</strong> <span x-text="formValues.name"></span></li>
                            <li><strong>Attendee Email:</strong> <span x-text="formValues.email"></span></li>
                            <li><strong>Booking Date:</strong> <span x-text="formValues.date"></span></li>
                            <li><strong>Booking Time:</strong> <span x-text="formValues.time"></span></li>
                            <li><strong>Timezone:</strong> <span x-text="formValues.timezone"></span></li>
                        </ul>


                        <div class="flex justify-end space-x-2">
                            <button @click="showModal = false" type="button"  class="text-sm/6 font-semibold text-gray-900">Cancel</button>
                            <button @click="submitForm" type="button"  class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Confirm</button>
                        </div>
                    </div>
                </div>

                <x-form-group :label="''" :id="'event-duration'" :inputData="array('type' => 'hidden', 'name' => 'event_duration', 'id' => 'event-duration', 'value' => $event->duration)" />
            </form>
        </div>
    </div>

    <script>
        function bookingFormHandler() {
            return {
                showModal: false,
                formValues: {
                    name: '',
                    email: '',
                    date: '',
                    time: '',
                    timezone: '',
                },
                // Show a Modal to confirm booking details
                confirmBookingDetails() {
                    const form = document.querySelector('#booking-form');
                    this.formValues = {
                        name: form.querySelector('[name="attendee_name"]').value,
                        email: form.querySelector('[name="attendee_email"]').value,
                        date: form.querySelector('[name="booking_date"]').value,
                        time: form.querySelector('[name="booking_time"]').value,
                        timezone: form.querySelector('[name="timezone"]').value,
                    };
                    this.showModal = true;
                },
                submitForm() {
                    document.querySelector('#booking-form').submit();
                },
            };
        }
    </script>
</x-guest-layout>
