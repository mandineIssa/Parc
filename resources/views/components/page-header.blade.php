@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6']) }}>
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($slot) && ! $slot->isEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
