<x-guest-layout>
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold mb-6">Event List</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($events as $event)
                <x-events.card :$event />
            @endforeach
        </div>
    </div>
</x-guest-layout>
