@extends('layouts.app')

@section('title', 'Paramètres planification batch EOD')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-900">Paramètres — planification batch</h1>
        <a href="{{ route('eod.planning.index') }}" class="text-sm text-[#C8102E] font-medium hover:underline">← Retour au planning</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif

    <form action="{{ route('eod.planning.settings.update') }}" method="POST" class="bg-white rounded-xl shadow-md p-6 space-y-6">
        @csrf

        <fieldset class="space-y-3">
            <legend class="text-sm font-bold text-gray-800 uppercase border-b pb-2 w-full">Notifications à la publication</legend>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="notify_on_publish" value="1" @checked(old('notify_on_publish', $settings->notify_on_publish)) class="rounded">
                <span class="text-sm">Notifier la personne désignée pour le batch</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="notify_supervisor_on_publish" value="1" @checked(old('notify_supervisor_on_publish', $settings->notify_supervisor_on_publish)) class="rounded">
                <span class="text-sm">Notifier aussi le superviseur batch (si compte GPI lié)</span>
            </label>
        </fieldset>

        <fieldset class="space-y-3">
            <legend class="text-sm font-bold text-gray-800 uppercase border-b pb-2 w-full">Rappels automatiques</legend>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="reminder_enabled" value="1" @checked(old('reminder_enabled', $settings->reminder_enabled)) class="rounded">
                <span class="text-sm">Activer les rappels automatiques (cron)</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="reminder_day_before" value="1" @checked(old('reminder_day_before', $settings->reminder_day_before)) class="rounded">
                <span class="text-sm">Rappel la veille</span>
            </label>
            <div class="ml-6">
                <label class="block text-xs text-gray-600 mb-1">Heure rappel veille</label>
                <input type="time" name="reminder_day_before_time" value="{{ old('reminder_day_before_time', substr($settings->reminder_day_before_time, 0, 5)) }}"
                    class="rounded border-gray-300">
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="reminder_same_day" value="1" @checked(old('reminder_same_day', $settings->reminder_same_day)) class="rounded">
                <span class="text-sm">Rappel le jour du batch</span>
            </label>
            <div class="ml-6">
                <label class="block text-xs text-gray-600 mb-1">Heure rappel jour J</label>
                <input type="time" name="reminder_same_day_time" value="{{ old('reminder_same_day_time', substr($settings->reminder_same_day_time, 0, 5)) }}"
                    class="rounded border-gray-300">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Nombre max. de rappels par jour</label>
                <input type="number" name="max_reminders_per_day" min="1" max="5"
                    value="{{ old('max_reminders_per_day', $settings->max_reminders_per_day) }}"
                    class="w-24 rounded border-gray-300">
            </div>
        </fieldset>

        <fieldset class="space-y-3">
            <legend class="text-sm font-bold text-gray-800 uppercase border-b pb-2 w-full">Valeurs par défaut</legend>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superviseur batch (libellé)</label>
                <input type="text" name="default_supervisor_name" required
                    value="{{ old('default_supervisor_name', $settings->default_supervisor_name) }}"
                    class="w-full rounded border-gray-300 uppercase" placeholder="NDICK/MAR">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Superviseur batch (compte GPI optionnel)</label>
                <select name="default_supervisor_user_id" class="w-full rounded border-gray-300">
                    <option value="">— Aucun —</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(old('default_supervisor_user_id', $settings->default_supervisor_user_id) == $u->id)>{{ $u->prenom }} {{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </fieldset>

        <div class="pt-4 border-t flex justify-end">
            <button type="submit" class="px-6 py-3 bg-[#C8102E] text-white font-semibold rounded-lg hover:bg-[#a00d24]">
                Enregistrer les paramètres
            </button>
        </div>
    </form>

    <p class="mt-6 text-xs text-gray-500">
        Les rappels automatiques nécessitent le cron : <code class="bg-gray-100 px-1">php artisan schedule:run</code>
        et la commande <code class="bg-gray-100 px-1">gpi:remind-eod-batch-planning</code> (planifiée en semaine).
    </p>
</div>
@endsection
