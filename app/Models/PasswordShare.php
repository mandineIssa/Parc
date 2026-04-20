<?php
// ═══════════════════════════════════════════════════════════════════════════
// app/Models/PasswordShare.php
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordShare extends Model
{
    protected $fillable = ['password_id', 'user_id', 'pole', 'droit', 'expiration', 'permanent'];

    protected $casts = [
        'expiration' => 'date',
        'permanent'  => 'boolean',
    ];

    public function password() { return $this->belongsTo(Password::class); }
    public function user()     { return $this->belongsTo(User::class); }
}
