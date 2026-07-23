<?php

namespace App\Http\Controllers;

use App\Models\EodBatchAssignment;
use App\Models\EodBatchWeek;
use App\Models\EodPlanningSetting;
use App\Models\User;
use App\Services\EodBatchPlanningNotifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EodBatchPlanningController extends Controller
{
    public function __construct(
        private readonly EodBatchPlanningNotifier $notifier
    ) {}

    public function index(Request $request)
    {
        $this->authorizeEodAccess();

        $weekStart = $this->resolveWeekStart($request->input('week'));
        $week = $this->findOrCreateWeek($weekStart);
        $week->load(['assignments.assignee', 'assignments.supervisor', 'creator']);

        $settings = EodPlanningSetting::current();
        $canManage = $this->canManagePlanning();
        $users = $canManage ? $this->batchEligibleUsers() : collect();

        $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');

        $myAssignments = $week->assignments->where('assignee_user_id', Auth::id());

        return view('eod.planning.index', compact(
            'week',
            'weekStart',
            'settings',
            'canManage',
            'users',
            'prevWeek',
            'nextWeek',
            'myAssignments'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $weekStart = $this->resolveWeekStart($request->input('week_start'));
        $validated = $request->validate([
            'week_start' => 'required|date',
            'assignments' => 'required|array|min:1',
            'assignments.*.assignee_user_id' => 'required|exists:users,id',
            'assignments.*.supervisor_user_id' => 'nullable|exists:users,id',
            'assignments.*.supervisor_name' => 'nullable|string|max:120',
        ]);

        $settings = EodPlanningSetting::current();

        DB::transaction(function () use ($weekStart, $validated, $settings) {
            $week = EodBatchWeek::query()->firstOrCreate(
                ['week_start' => $weekStart->toDateString()],
                ['created_by' => Auth::id(), 'status' => 'draft']
            );

            if ($week->isPublished()) {
                abort(422, 'Cette semaine est déjà publiée. Créez une nouvelle semaine ou contactez N+3.');
            }

            $week->assignments()->delete();

            foreach ($this->weekdays($weekStart) as $day) {
                $row = $validated['assignments'][$day['key']] ?? null;
                if (! $row || empty($row['assignee_user_id'])) {
                    continue;
                }

                EodBatchAssignment::create([
                    'week_id' => $week->id,
                    'scheduled_date' => $day['date'],
                    'day_of_week' => $day['dow'],
                    'assignee_user_id' => $row['assignee_user_id'],
                    'supervisor_user_id' => $row['supervisor_user_id'] ?? $settings->default_supervisor_user_id,
                    'supervisor_name' => $row['supervisor_name'] ?? $settings->default_supervisor_name,
                ]);
            }
        });

        return redirect()
            ->route('eod.planning.index', ['week' => $weekStart->format('Y-m-d')])
            ->with('success', 'Planification enregistrée (brouillon).');
    }

    public function publish(Request $request)
    {
        $this->authorizeManage();

        $weekStart = $this->resolveWeekStart($request->input('week_start'));
        $week = EodBatchWeek::query()
            ->with('assignments')
            ->where('week_start', $weekStart->toDateString())
            ->firstOrFail();

        if ($week->assignments->isEmpty()) {
            return back()->with('error', 'Aucune affectation à publier pour cette semaine.');
        }

        $settings = EodPlanningSetting::current();

        DB::transaction(function () use ($week) {
            $week->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
        });

        $sent = $this->notifier->notifyWeekPublished($week->fresh(['assignments.assignee', 'assignments.supervisor']), $settings);

        return redirect()
            ->route('eod.planning.index', ['week' => $weekStart->format('Y-m-d')])
            ->with('success', "Planning publié. {$sent} notification(s) envoyée(s).");
    }

    public function settings()
    {
        $this->authorizeManage();

        $settings = EodPlanningSetting::current();
        $users = $this->batchEligibleUsers();

        return view('eod.planning.settings', compact('settings', 'users'));
    }

    public function updateSettings(Request $request)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'notify_on_publish' => 'nullable|boolean',
            'notify_supervisor_on_publish' => 'nullable|boolean',
            'reminder_enabled' => 'nullable|boolean',
            'reminder_same_day' => 'nullable|boolean',
            'reminder_same_day_time' => 'required|date_format:H:i',
            'reminder_day_before' => 'nullable|boolean',
            'reminder_day_before_time' => 'required|date_format:H:i',
            'default_supervisor_user_id' => 'nullable|exists:users,id',
            'default_supervisor_name' => 'required|string|max:120',
            'max_reminders_per_day' => 'required|integer|min:1|max:5',
        ]);

        $settings = EodPlanningSetting::current();
        $settings->update([
            'notify_on_publish' => $request->boolean('notify_on_publish'),
            'notify_supervisor_on_publish' => $request->boolean('notify_supervisor_on_publish'),
            'reminder_enabled' => $request->boolean('reminder_enabled'),
            'reminder_same_day' => $request->boolean('reminder_same_day'),
            'reminder_same_day_time' => $validated['reminder_same_day_time'],
            'reminder_day_before' => $request->boolean('reminder_day_before'),
            'reminder_day_before_time' => $validated['reminder_day_before_time'],
            'default_supervisor_user_id' => $validated['default_supervisor_user_id'] ?? null,
            'default_supervisor_name' => $validated['default_supervisor_name'],
            'max_reminders_per_day' => $validated['max_reminders_per_day'],
        ]);

        return redirect()
            ->route('eod.planning.settings')
            ->with('success', 'Paramètres de planification enregistrés.');
    }

    public function remindNow(Request $request, EodBatchAssignment $assignment)
    {
        $this->authorizeManage();

        if (! $assignment->week?->isPublished()) {
            return back()->with('error', 'Le planning de cette semaine n\'est pas publié.');
        }

        if ($this->notifier->sendReminder($assignment)) {
            return back()->with('success', 'Rappel envoyé à '.$assignment->assigneeDisplayName().'.');
        }

        return back()->with('error', 'Impossible d\'envoyer le rappel.');
    }

    private function resolveWeekStart(?string $input): Carbon
    {
        if ($input) {
            $date = Carbon::parse($input)->startOfDay();
        } else {
            $date = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }

        return $date->startOfWeek(Carbon::MONDAY);
    }

    private function findOrCreateWeek(Carbon $weekStart): EodBatchWeek
    {
        return EodBatchWeek::query()->firstOrCreate(
            ['week_start' => $weekStart->toDateString()],
            ['created_by' => Auth::id(), 'status' => 'draft']
        );
    }

    /**
     * @return array<int, array{key: string, date: string, dow: int, label: string}>
     */
    private function weekdays(Carbon $weekStart): array
    {
        $days = [];
        $cursor = $weekStart->copy();

        for ($dow = 1; $dow <= 5; $dow++) {
            $days[] = [
                'key' => (string) $dow,
                'date' => $cursor->toDateString(),
                'dow' => $dow,
                'label' => config('eod.planning.day_labels.'.$dow, 'JOUR').' '.$cursor->format('d/m/Y'),
            ];
            $cursor->addDay();
        }

        return $days;
    }

    private function batchEligibleUsers()
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('name')
            ->orderBy('prenom')
            ->get();
    }

    private function authorizeEodAccess(): void
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return;
        }

        $allowed = $user->role_change === 'N1'
            || $user->role_change === 'N2'
            || $user->canAccessEodAsN3()
            || $user->canSignEodControllerSlot();

        if (! $allowed) {
            abort(403, 'Accès réservé aux profils EOD.');
        }
    }

    private function authorizeManage(): void
    {
        if (! $this->canManagePlanning()) {
            abort(403, 'Seuls N+3, Controller EOD ou Super Admin peuvent gérer la planification batch.');
        }
    }

    private function canManagePlanning(): bool
    {
        $user = Auth::user();

        return $user->role === 'super_admin'
            || $user->canAccessEodAsN3()
            || $user->canSignEodControllerSlot();
    }
}
