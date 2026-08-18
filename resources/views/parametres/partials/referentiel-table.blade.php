@php
    $perPage = $perPage ?? 5;
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $title }}</h1>
        <a href="{{ $createUrl }}" class="inline-flex items-center px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white font-semibold rounded-md shadow-sm">
            {{ $createLabel }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" class="rounded border-gray-300" disabled>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rounded border-gray-300" disabled>
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-900 uppercase">{{ $item->nom }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->description ?: ('Direction '.mb_strtolower($item->nom)) }}</td>
                            <td class="px-4 py-3">
                                @if($item->actif)
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route($showRoute, $item) }}" class="text-gray-500 hover:text-indigo-600" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route($editRoute, $item) }}" class="text-gray-500 hover:text-amber-600" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route($destroyRoute, $item) }}" onsubmit="return confirm('Supprimer cet élément ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">Aucun enregistrement.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600">
            <form method="GET" class="flex items-center gap-2">
                <select name="per_page" onchange="this.form.submit()" class="rounded border-gray-300 text-sm">
                    @foreach([5, 10, 15, 25] as $size)
                        <option value="{{ $size }}" {{ (int) $perPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
            <div>
                Entrée {{ $items->firstItem() ?? 0 }} à {{ $items->lastItem() ?? 0 }} sur {{ $items->total() }} Entrées
                (Page {{ $items->currentPage() }} / {{ $items->lastPage() }})
            </div>
            <div>
                {{ $items->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>
