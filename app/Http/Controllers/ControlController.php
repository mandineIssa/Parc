<?php

namespace App\Http\Controllers;
use App\Models\ControlTask; 
use App\Models\Control;
use App\Models\ControlTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ControlController extends Controller
{
   

    public function index(Request $request)
    {
        $query = Control::with('template');
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $controls = $query->orderBy('created_at', 'desc')->paginate(15);
        $stats = [
            'total' => Control::count(),
            'active' => Control::where('status', 'actif')->count(),
            'pending_tasks' => DB::table('control_tasks')->where('status', 'pending')->count(),
            'overdue_tasks' => DB::table('control_tasks')
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->count()
        ];
        
        return view('controls.index', compact('controls', 'stats'));
    }

    public function create()
    {
        $templates = ControlTemplate::where('is_active', true)->get();
        $users = User::all();
        $applications = $this->getApplicationsFromCartography();
        
        return view('controls.create', compact('templates', 'users', 'applications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:securite,exploitation,conformite,audit',
            'frequency' => 'required|in:quotidienne,hebdomadaire,mensuelle,ponctuelle',
            'description' => 'nullable|string',
            'template_id' => 'nullable|exists:control_templates,id',
            'associated_application' => 'nullable|string',
            'responsible_role' => 'required|in:N1,N2,N3',
            'parameters' => 'nullable|array'
        ]);
        
        $control = Control::create($validated);
        
        // Générer la première tâche
        $this->generateNextTask($control);
        
        return redirect()->route('controls.index')
            ->with('success', 'Contrôle créé avec succès');
    }

    public function show(Control $control)
    {
        $control->load(['tasks.assignedTo', 'tasks.attachments', 'template']);
        $conformityData = $this->getConformityStats($control);
        
        return view('controls.show', compact('control', 'conformityData'));
    }

    public function edit(Control $control)
    {
        $templates = ControlTemplate::where('is_active', true)->get();
        $users = User::all();
        $applications = $this->getApplicationsFromCartography();
        
        return view('controls.edit', compact('control', 'templates', 'users', 'applications'));
    }

    public function update(Request $request, Control $control)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:securite,exploitation,conformite,audit',
            'frequency' => 'required|in:quotidienne,hebdomadaire,mensuelle,ponctuelle',
            'status' => 'required|in:actif,inactif',
            'description' => 'nullable|string',
            'template_id' => 'nullable|exists:control_templates,id',
            'associated_application' => 'nullable|string',
            'responsible_role' => 'required|in:N1,N2,N3'
        ]);
        
        $control->update($validated);
        
        return redirect()->route('controls.show', $control)
            ->with('success', 'Contrôle mis à jour avec succès');
    }

    public function destroy(Control $control)
    {
        $control->delete();
        
        return redirect()->route('controls.index')
            ->with('success', 'Contrôle supprimé avec succès');
    }

    public function generateTasks(Control $control)
    {
        $this->generateNextTask($control);
        
        return redirect()->route('controls.show', $control)
            ->with('success', 'Tâches générées avec succès');
    }

    private function generateNextTask(Control $control)
    {
        $dueDate = $this->calculateNextDueDate($control->frequency);
        
        ControlTask::create([
            'control_id' => $control->id,
            'title' => $control->name . ' - ' . $dueDate->format('d/m/Y'),
            'description' => $control->description,
            'status' => 'pending',
            'due_date' => $dueDate,
            'assigned_to' => $this->getResponsibleUser($control->responsible_role)
        ]);
        
        $control->update(['next_run_at' => $dueDate]);
    }

    private function calculateNextDueDate(string $frequency): Carbon
    {
        return match($frequency) {
            'quotidienne' => now()->addDay(),
            'hebdomadaire' => now()->addWeek(),
            'mensuelle' => now()->addMonth(),
            'ponctuelle' => now()->addDays(7),
            default => now()->addWeek()
        };
    }

    private function getResponsibleUser(string $role): ?int
    {
        $user = User::where('role_change', $role)->first();
        return $user ? $user->id : null;
    }

    private function getConformityStats(Control $control): array
    {
        $tasks = $control->tasks;
        $total = $tasks->count();
        
        if ($total === 0) {
            return ['conforme' => 0, 'non_conforme' => 0, 'en_attente' => 0];
        }
        
        return [
            'conforme' => round(($tasks->where('conformity', 'conforme')->count() / $total) * 100, 2),
            'non_conforme' => round(($tasks->where('conformity', 'non_conforme')->count() / $total) * 100, 2),
            'en_attente' => round(($tasks->where('conformity', 'en_attente')->count() / $total) * 100, 2)
        ];
    }

    private function getApplicationsFromCartography(): array
    {
        // Récupérer les applications depuis le fichier Excel ou la base
        // À adapter selon votre stockage des données
        return [
            'APP-01' => 'FLEXCUBE',
            'APP-02' => 'REPORT SN',
            'APP-03' => 'IT SN',
            'APP-04' => 'CREDIT FLOW',
            'APP-05' => 'COFILAB-REIS',
            'APP-06' => 'SI-RH',
            'APP-07' => 'NAFA',
            'APP-08' => 'COFIHELPDESK',
            'APP-09' => 'ODOO',
            'APP-10' => 'COFINA MOBILE+',
            'APP-11' => 'CREDIT BACKE',
            'APP-12' => 'YAS/NANO_CREDIT_LEBALMA',
            'APP-13' => 'WAVE',
            'APP-14' => 'USSD ORANGE MONEY',
            'APP-15' => 'OAS',
            'APP-16' => 'WAVE COFFRE',
            'APP-17' => 'WAVE NANO CREDIT',
            'APP-19' => 'GEFA',
            'APP-20' => 'TABLETTE FILE BACK',
            'APP-21' => 'GRC'
        ];
    }
}