<?php

namespace App\Http\Controllers;

use App\Models\ControlTemplate;
use Illuminate\Http\Request;

class ControlTemplateController extends Controller
{
 

    public function index()
    {
        $templates = ControlTemplate::orderBy('created_at', 'desc')->paginate(15);
        $reviewTypes = ControlTemplate::getReviewTypes();
        return view('controls.templates.index', compact('templates', 'reviewTypes'));
    }

    public function create()
    {
        $reviewTypes = ControlTemplate::getReviewTypes();
        $frequencies = ControlTemplate::getFrequencies();
        
        return view('controls.templates.create', compact('reviewTypes', 'frequencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'review_type' => 'required|in:' . implode(',', array_keys(ControlTemplate::getReviewTypes())),
            'frequency' => 'required|in:' . implode(',', array_keys(ControlTemplate::getFrequencies())),
            'description' => 'nullable|string',
            'checklist' => 'nullable|array',
            'questions' => 'nullable|array',
            'required_attachments' => 'nullable|array',
            'is_active' => 'boolean'
        ]);
        
        ControlTemplate::create($validated);
        
        return redirect()->route('controls.templates.index')
            ->with('success', 'Template créé avec succès');
    }

    public function edit(ControlTemplate $template)
    {
        $reviewTypes = ControlTemplate::getReviewTypes();
        $frequencies = ControlTemplate::getFrequencies();
        
        return view('controls.templates.edit', compact('template', 'reviewTypes', 'frequencies'));
    }

    public function update(Request $request, ControlTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'review_type' => 'required|in:' . implode(',', array_keys(ControlTemplate::getReviewTypes())),
            'frequency' => 'required|in:' . implode(',', array_keys(ControlTemplate::getFrequencies())),
            'description' => 'nullable|string',
            'checklist' => 'nullable|array',
            'questions' => 'nullable|array',
            'required_attachments' => 'nullable|array',
            'is_active' => 'boolean'
        ]);
        
        $template->update($validated);
        
        return redirect()->route('controls.templates.index')
            ->with('success', 'Template mis à jour avec succès');
    }

    public function destroy(ControlTemplate $template)
    {
        $template->delete();
        
        return redirect()->route('controls.templates.index')
            ->with('success', 'Template supprimé avec succès');
    }

    public function details(ControlTemplate $template)
    {
        return response()->json([
            'description' => $template->description,
            'checklist' => $template->checklist ?? [],
            'questions' => $template->questions ?? [],
            'required_attachments' => $template->required_attachments ?? []
        ]);
    }
}