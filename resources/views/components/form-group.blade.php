@props(['id', 'label', 'inputData', 'wrapperClass'])
<div class="{{$wrapperClass ?? 'sm:col-span-3'}}">
    <x-input-label for="{{ $id }}" :value="$label" />
    @php($inputName = $inputData['name'] ?? '')
    @if(isset($inputData))
        <div class="mt-2">
            <input type="{{ $inputData['type'] ?? 'text' }}" name="{{ $inputData['name'] ?? $id }}" id="{{ $inputData['id'] ?? $id }}" autocomplete="name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" {{ isset($inputData['required']) ? ' required' : '' }} value="{{ $inputData['value'] ?? '' }}">
        </div>
    @else
        {{ $slot }}
    @endif

    @if( $errors->has($inputName) )
        <x-alert :type="'danger'">
            {{ $errors->first($inputName) }}
        </x-alert>
    @endif
</div>
