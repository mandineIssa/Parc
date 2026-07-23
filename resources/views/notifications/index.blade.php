@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <x-page-header title="Notifications" subtitle="Alertes et confirmations GPI">
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Tout marquer comme lu</button>
        </form>
    </x-page-header>

    <div class="bg-white rounded-xl shadow divide-y">
        @forelse($notifications as $n)
            <div class="p-4 {{ $n->read_at ? 'opacity-70' : 'bg-red-50/30' }}">
                <div class="flex justify-between gap-4">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $n->title }}</p>
                        <p class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ $n->message }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $n->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        @if($n->action_url)
                            <a href="{{ $n->action_url }}" class="text-sm text-[#C8102E] font-medium">Ouvrir</a>
                        @endif
                        @if(! $n->read_at)
                            <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-gray-800">Marquer lu</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-gray-500">Aucune notification.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
