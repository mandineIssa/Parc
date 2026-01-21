<?php

namespace App\View\Components;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public $href;
    public $active;
    public $disabled;
    public $icon;
    public $text;
    public $requiredRole;
    
    /**
     * Create a new component instance.
     */
    public function __construct($href = '#', $active = false, $disabled = false, $icon = null, $text = '', $requiredRole = null)
    {
        $this->href = $href;
        $this->active = $active;
        $this->disabled = $disabled;
        $this->icon = $icon;
        $this->text = $text;
        $this->requiredRole = $requiredRole;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // Vérifier si l'utilisateur a le rôle requis
        $hasPermission = true;
        if ($this->requiredRole) {
            $userRole = Auth::check() ? Auth::user()->role : 'user';
            $requiredRoles = is_array($this->requiredRole) ? $this->requiredRole : [$this->requiredRole];
            $hasPermission = in_array($userRole, $requiredRoles);
        }
        
        // Si pas de permission, rendre le bouton désactivé ou invisible
        if (!$hasPermission && $this->disabled) {
            return view('components.button-disabled', [
                'icon' => $this->icon,
                'text' => $this->text,
                'active' => $this->active,
            ]);
        }
        
        // Si pas de permission et pas disabled, ne rien afficher
        if (!$hasPermission) {
            return '';
        }
        
        return view('components.button', [
            'href' => $this->href,
            'active' => $this->active,
            'icon' => $this->icon,
            'text' => $this->text,
        ]);
    }
}