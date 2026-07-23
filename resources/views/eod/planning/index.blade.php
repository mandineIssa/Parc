@extends('layouts.app')

@section('title', 'Planification batch EOD')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#C8102E] uppercase tracking-wide">Planning traitement batch de la semaine</h1>
            <p class="text-sm text-gray-600 mt-1">
                Semaine du {{ $weekStart->format('d/m/Y') }} au {{ $weekStart->copy()->addDays(4)->format('d/m/Y') }}
                @if($week->isPublished())
                    <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded">Publié</span>
                @else
                    <span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-semibold rounded">Brouillon</span>
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('eod.planning.index', ['week' => $prevWeek]) }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">← Semaine préc.</a>
            <a href="{{ route('eod.planning.index') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Semaine actuelle</a>
            <a href="{{ route('eod.planning.index', ['week' => $nextWeek]) }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Semaine suiv. →</a>
            @if($canManage)
                <a href="{{ route('eod.planning.settings') }}" class="px-3 py-2 bg-gray-800 text-white rounded-lg text-sm hover:bg-gray-900">Paramètres</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">{{ session('error') }}</div>
    @endif

    @if($myAssignments->isNotEmpty() && ! $canManage)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="font-semibold text-blue-900">Vos affectations cette semaine :</p>
            <ul class="mt-2 text-sm text-blue-800 list-disc list-inside">
                @foreach($myAssignments as $a)
                    <li>{{ $a->dayLabel() }} — Superviseur : {{ $a->supervisorDisplayName() }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $weekdays = [];
        $cursor = $weekStart->copy();
        for ($dow = 1; $dow <= 5; $dow++) {
            $weekdays[$dow] = ['date' => $cursor->copy(), 'dow' => $dow];
            $cursor->addDay();
        }
        $assignmentByDow = $week->assignments->keyBy('day_of_week');
    @endphp

    @if($canManage && ! $week->isPublished())
    <form action="{{ route('eod.planning.store') }}" method="POST" class="mb-6">
        @csrf
        <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
    @endif

    <div class="overflow-hidden rounded-xl shadow-lg border-2 border-black">
        <div class="bg-yellow-300 px-4 py-3 text-center border-b-2 border-black">
            <span class="text-xl font-extrabold text-[#C8102E] uppercase">Planning traitement batch de la semaine</span>
        </div>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-black text-white text-sm uppercase">
                    <th class="px-4 py-3 text-left border-r border-gray-600 w-2/5">Prénom et nom</th>
                    <th class="px-4 py-3 text-left border-r border-gray-600 w-1/4">Date du jour</th>
                    <th class="px-4 py-3 text-left w-1/3">Superviseur batch</th>
                    @if($canManage && $week->isPublished())
                        <th class="px-4 py-3 text-center w-24">Rappel</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($weekdays as $dow => $day)
                    @php
                        $assignment = $assignmentByDow->get($dow);
                        $bg = config('eod.planning.day_colors.'.$dow, '#fff');
                        $dayLabel = config('eod.planning.day_labels.'.$dow, 'JOUR').' '.$day['date']->format('d/m/Y');
                        $isMine = $assignment && (int) $assignment->assignee_user_id === (int) auth()->id();
                    @endphp
                    <tr style="background-color: {{ $bg }};" class="{{ $isMine ? 'ring-2 ring-inset ring-[#C8102E]' : '' }}">
                        <td class="px-4 py-4 border-t border-black font-semibold text-gray-900 uppercase">
                            @if($canManage && ! $week->isPublished())
                                <select name="assignments[{{ $dow }}][assignee_user_id]" required
                                    class="w-full px-3 py-2 border border-gray-400 rounded bg-white/80 text-sm font-semibold uppercase">
                                    <option value="">— Sélectionner —</option>
                                    @foreach($users as $u)
                                        @php $label = strtoupper(trim($u->prenom.' '.$u->name)); @endphp
                                        <option value="{{ $u->id }}" @selected(old("assignments.{$dow}.assignee_user_id", $assignment?->assignee_user_id) == $u->id)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            @else
                                {{ $assignment?->assigneeDisplayName() ?? '—' }}
                            @endif
                        </td>
                        <td class="px-4 py-4 border-t border-l border-black font-bold text-gray-800">
                            {{ $dayLabel }}
                        </td>
                        <td class="px-4 py-4 border-t border-l border-black font-semibold text-gray-900 uppercase">
                            @if($canManage && ! $week->isPublished())
                                <input type="text" name="assignments[{{ $dow }}][supervisor_name]"
                                    value="{{ old("assignments.{$dow}.supervisor_name", $assignment?->supervisor_name ?? $settings->default_supervisor_name) }}"
                                    class="w-full px-3 py-2 border border-gray-400 rounded bg-white/80 text-sm font-semibold uppercase mb-1"
                                    placeholder="Ex: NDICK/MAR">
                                <select name="assignments[{{ $dow }}][supervisor_user_id]"
                                    class="w-full px-3 py-2 border border-gray-300 rounded bg-white/80 text-xs">
                                    <option value="">Superviseur (compte GPI optionnel)</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" @selected(old("assignments.{$dow}.supervisor_user_id", $assignment?->supervisor_user_id ?? $settings->default_supervisor_user_id) == $u->id)>{{ $u->prenom }} {{ $u->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                {{ $assignment?->supervisorDisplayName() ?? '—' }}
                            @endif
                        </td>
                        @if($canManage && $week->isPublished() && $assignment)
                        <td class="px-2 py-4 border-t border-l border-black text-center">
                            <form action="{{ route('eod.planning.remind', $assignment) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs px-2 py-1 bg-[#C8102E] text-white rounded hover:bg-[#a00d24]" title="Envoyer un rappel maintenant">🔔</button>
                            </form>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($canManage && ! $week->isPublished())
        <div class="flex flex-wrap gap-3 mt-6 justify-end">
            <button type="submit" class="px-6 py-3 bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-800">
                Enregistrer (brouillon)
            </button>
        </div>
    </form>

    @if($week->assignments->isNotEmpty())
    <form action="{{ route('eod.planning.publish') }}" method="POST" class="mt-3 flex justify-end"
          onsubmit="return confirm('Publier le planning et envoyer les notifications aux personnes désignées ?');">
        @csrf
        <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
        <button type="submit" class="px-6 py-3 bg-[#C8102E] text-white font-semibold rounded-lg hover:bg-[#a00d24]">
            Publier & notifier
        </button>
    </form>
    @endif
    @endif

    @if($week->isPublished())
    <div class="mt-6 p-4 bg-gray-50 rounded-lg text-sm text-gray-600">
        <p><strong>Notifications :</strong>
            @if($settings->notify_on_publish) envoi à la publication @endif
            @if($settings->reminder_enabled)
                · Rappels :
                @if($settings->reminder_day_before) veille à {{ substr($settings->reminder_day_before_time, 0, 5) }} @endif
                @if($settings->reminder_same_day) jour J à {{ substr($settings->reminder_same_day_time, 0, 5) }} @endif
            @endif
        </p>
        <p class="mt-1">Publié le {{ $week->published_at?->format('d/m/Y H:i') ?? '—' }}</p>
    </div>
    @endif
</div>
@endsection
