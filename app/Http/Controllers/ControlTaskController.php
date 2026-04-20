<?php

namespace App\Http\Controllers;

use App\Models\ControlTask;
use App\Models\ControlAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ControlTaskController extends Controller
{
    

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ControlTask::with(['control', 'assignedTo']);
        
        // Filtrer selon le rôle
        if ($user->role_change === 'N1') {
            $query->where('assigned_to', $user->id);
        } elseif ($user->role_change === 'N2') {
            $query->whereHas('control', function($q) {
                $q->where('responsible_role', 'N1');
            })->where('status', 'completed');
        } elseif ($user->role_change === 'N3') {
            $query->whereHas('control', function($q) {
                $q->where('responsible_role', 'N2');
            })->where('status', 'completed');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $tasks = $query->orderBy('due_date', 'asc')->paginate(20);
        $stats = [
            'pending' => ControlTask::where('status', 'pending')->count(),
            'overdue' => ControlTask::where('status', 'pending')
                ->where('due_date', '<', now())->count(),
            'completed' => ControlTask::where('status', 'completed')->count(),
            'rejected' => ControlTask::where('status', 'rejected')->count()
        ];
        
        return view('controls.tasks.index', compact('tasks', 'stats'));
    }

    public function show(ControlTask $task)
    {
        $task->load(['control', 'assignedTo', 'validatedBy', 'attachments.uploadedBy']);
        $canValidate = auth()->user()->canValidateTask($task);
        
        return view('controls.tasks.show', compact('task', 'canValidate'));
    }

    public function updateStatus(Request $request, ControlTask $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected,need_complement',
            'conformity' => 'nullable|required_if:status,completed|in:conforme,non_conforme,en_attente',
            'criticality' => 'nullable|required_if:conformity,non_conforme|in:mineur,majeur,critique',
            'comment' => 'nullable|required_if:conformity,non_conforme|string'
        ]);
        
        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }
        
        $task->update($validated);
        
        return redirect()->route('controls.tasks.show', $task)
            ->with('success', 'Tâche mise à jour avec succès');
    }

    public function validateTask(Request $request, ControlTask $task)
    {
        if (!auth()->user()->canValidateTask($task)) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour valider cette tâche');
        }
        
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,need_complement',
            'comment' => 'nullable|required_if:action,reject,need_complement|string'
        ]);
        
        if ($validated['action'] === 'approve') {
            $task->update([
                'status' => 'completed',
                'validated_by' => auth()->id(),
                'validated_at' => now()
            ]);
            $message = 'Tâche validée avec succès';
        } elseif ($validated['action'] === 'reject') {
            $task->update([
                'status' => 'rejected',
                'comment' => $validated['comment']
            ]);
            $message = 'Tâche rejetée';
        } else {
            $task->update([
                'status' => 'need_complement',
                'comment' => $validated['comment']
            ]);
            $message = 'Compléments demandés';
        }
        
        return redirect()->route('controls.tasks.show', $task)
            ->with('success', $message);
    }

    public function uploadAttachment(Request $request, ControlTask $task)
    {
        $request->validate([
            'attachment' => 'required|file|max:10240|mimes:pdf,jpg,png,xlsx,xls,doc,docx,txt'
        ]);
        
        $file = $request->file('attachment');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('controls/' . $task->id, $filename, 'public');
        
        $attachment = ControlAttachment::create([
            'control_task_id' => $task->id,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'uploaded_by' => auth()->id(),
            'version' => $task->attachments()->max('version') + 1
        ]);
        
        return response()->json([
            'success' => true,
            'attachment' => $attachment,
            'url' => $attachment->url
        ]);
    }

    public function deleteAttachment(ControlAttachment $attachment)
    {
        $task = $attachment->task;
        $attachment->delete();
        
        return redirect()->route('controls.tasks.show', $task)
            ->with('success', 'Pièce jointe supprimée');
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'my_pending_tasks' => ControlTask::where('assigned_to', $user->id)
                ->where('status', 'pending')->count(),
            'my_overdue_tasks' => ControlTask::where('assigned_to', $user->id)
                ->where('status', 'pending')
                ->where('due_date', '<', now())->count(),
            'to_validate' => ControlTask::where('status', 'completed')
                ->whereNull('validated_by')->count(),
            'total_compliance_rate' => $this->getGlobalComplianceRate()
        ];
        
        $recentTasks = ControlTask::where('assigned_to', $user->id)
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();
        
        return view('controls.dashboard', compact('stats', 'recentTasks'));
    }

    private function getGlobalComplianceRate(): float
    {
        $total = ControlTask::whereNotNull('conformity')->count();
        if ($total === 0) return 0;
        
        $conforme = ControlTask::where('conformity', 'conforme')->count();
        return round(($conforme / $total) * 100, 2);
    }
}