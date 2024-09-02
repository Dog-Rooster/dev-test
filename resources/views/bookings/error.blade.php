<x-guest-layout>
    <div class="flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 max-w-lg w-full">
            <h1 class="text-3xl font-bold mb-4 text-center text-red-600">Failed!</h1>
            <p class="text-lg mb-4 text-gray-700 text-center">
                Your booking has been failed.
            </p>
            <p class="text-lg mb-4 text-gray-700 text-center">
                {{$errorMessage}}
            </p>
            <div class="text-center">
                <a href="{{ route('events') }}"
                    class="inline-block px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    View Events
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
