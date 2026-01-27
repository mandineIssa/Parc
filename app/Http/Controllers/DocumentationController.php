<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * Afficher la page de documentation principale
     */
    public function index()
    {
        return view('documentation.index');
    }
    
    /**
     * Afficher la documentation par section
     */
    public function show($section)
    {
        $sections = [
            'utilisateur' => 'Guide Utilisateur',
            'admin' => 'Guide Administrateur',
            'agent-it' => 'Guide Agent IT',
            'api' => 'Documentation API',
            'installation' => 'Guide d\'Installation',
        ];
        
        if (!array_key_exists($section, $sections)) {
            abort(404);
        }
        
        return view('documentation.show', [
            'section' => $section,
            'title' => $sections[$section]
        ]);
    }
    
    /**
     * Télécharger la documentation en PDF
     */
    public function download($format = 'pdf')
    {
        $filename = 'documentation_parc_informatique.' . $format;
        $path = storage_path('app/public/documentation/' . $filename);
        
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Documentation non disponible');
        }
        
        return response()->download($path);
    }
}