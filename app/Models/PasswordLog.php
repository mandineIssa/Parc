<?php
// ═══════════════════════════════════════════════════════════════════════════
// app/Models/PasswordLog.php
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordLog extends Model
{
    protected $fillable = ['password_id', 'user_id', 'action', 'details', 'ip_address'];

    public function password() { return $this->belongsTo(Password::class); }
    public function user()     { return $this->belongsTo(User::class); }
}