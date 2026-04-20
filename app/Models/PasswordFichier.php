<?php
// app/Models/PasswordFichier.php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PasswordFichier extends Model
{
    protected $table = 'password_fichiers';
    protected $fillable = ['password_id','nom_original','chemin','taille','mime','uploaded_by'];

    public function password()  { return $this->belongsTo(Password::class); }
    public function uploader()  { return $this->belongsTo(User::class,'uploaded_by'); }

    public function getTailleFormateeAttribute(): string
    {
        $b = $this->taille;
        if ($b < 1024)       return $b.' B';
        if ($b < 1048576)    return round($b/1024,1).' KB';
        return round($b/1048576,1).' MB';
    }
}
