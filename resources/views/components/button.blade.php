<div>
    <!-- If you do not have a consistent goal in life, you can not live it in a consistent way. - Marcus Aurelius -->
     <a href="{{ $href }}" 
   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ $active ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
    @if($icon)
    <x-dynamic-component :component="'icons.' . $icon" class="w-5 h-5 mr-3" />
    @endif
    <span>{{ $text }}</span>
</a>
</div>