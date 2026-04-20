<?php
// app/Models/Password.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Password extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'categorie','nom','nom_exi','adresse_ip','nom_vm','adresse_ip_vm',
        'protocole','compte','mot_de_passe','instance','type_equipement',
        'site','date_expiration','duree_renouvellement','description',
        'champs_libres','created_by','updated_by','is_active',
    ];

    protected $casts = [
        'mot_de_passe'    => 'encrypted',
        'date_expiration' => 'date',
        'is_active'       => 'boolean',
        'champs_libres'   => 'array',
    ];

    protected $hidden = ['mot_de_passe'];

    public function creator()  { return $this->belongsTo(User::class,'created_by'); }
    public function updater()  { return $this->belongsTo(User::class,'updated_by'); }
    public function shares()   { return $this->hasMany(PasswordShare::class); }
    public function logs()     { return $this->hasMany(PasswordLog::class); }
    public function fichiers() { return $this->hasMany(PasswordFichier::class); }

    public function scopeActive($q)              { return $q->where('is_active',true); }
    public function scopeByCategorie($q,$c)      { return $q->where('categorie',$c); }
    public function scopeExpiresSoon($q,$days=30){ return $q->whereNotNull('date_expiration')->whereBetween('date_expiration',[now(),now()->addDays($days)]); }

    public function getStatutExpirationAttribute(): string
    {
        if (!$this->date_expiration) return 'Aucune';
        if ($this->date_expiration->isPast()) return 'Expiré';
        if ($this->date_expiration->diffInDays(now()) <= 30) return 'Bientôt';
        return 'Valide';
    }

    public function logAction(string $action, string $details = null): void
    {
        $this->logs()->create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'details'    => $details,
            'ip_address' => request()->ip(),
        ]);
    }

    public static function categories(): array
    {
        return ['Serveur','Réseau','Base de données','Sécurité électronique','Active Directory','Modem/WiFi'];
    }

    public static function sites(): array
    {
        return ['AGP','TOUBA','TAMBA','ZIG','PIKINE','DAKAR','AUTRE'];
    }
}


