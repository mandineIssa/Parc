@extends('layouts.app')

@section('title', 'Gestion des Templates')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Templates de contrôle</h1>
            <p class="text-gray-500 mt-1">Modèles réutilisables pour standardiser vos contrôles</p>
        </div>
        <a href="{{ route('controls.templates.create') }}" 
           class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau template
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <select name="review_type" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous les types de revue</option>
                    @foreach($reviewTypes as $value => $label)
                    <option value="{{ $value }}" {{ request('review_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition">
                    Filtrer
                </button>
                <a href="{{ route('controls.templates.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition inline-block">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des templates -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="p-4 border-b bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $template->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $template->review_type_label }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $template->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Fréquence: {{ $template->frequency_label }}
                    </div>
                </div>
                
                @if($template->description)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $template->description }}</p>
                @endif

                <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                    <span>📋 Checklist: {{ count($template->checklist ?? []) }} items</span>
                    <span>❓ Questions: {{ count($template->questions ?? []) }}</span>
                    <span>📎 Justificatifs: {{ count($template->required_attachments ?? []) }}</span>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <a href="{{ route('controls.templates.edit', $template) }}" class="text-yellow-600 hover:text-yellow-800 text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                    <button onclick="deleteTemplate({{ $template->id }})" class="text-red-600 hover:text-red-800 text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-lg shadow p-12 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>Aucun template trouvé</p>
            <a href="{{ route('controls.templates.create') }}" class="mt-2 inline-block text-red-600 hover:text-red-800">
                Créer votre premier template
            </a>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $templates->links() }}
    </div>
</div>

@push('scripts')
<script>
function deleteTemplate(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce template ? Cette action est irréversible.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('controls.templates.index') }}/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection