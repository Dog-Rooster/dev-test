<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-semibold mb-2">{{ $event->name }}</h2>
    <p class="text-gray-700 mb-2">
        <strong>Duration:</strong> {{ $event->duration }} minutes
    </p>
    <p class="text-gray-600 mb-4">{{ $event->description }}</p>
    <div class="flex items-center justify-between">
        <a href="{{ route('bookings.create', $event->id) }}" class="text-blue-500 hover:underline">Book</a>
    </div>
</div>
