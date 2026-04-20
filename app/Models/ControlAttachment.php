<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ControlAttachment extends Model
{
    protected $fillable = [
        'control_task_id', 'filename', 'original_name',
        'mime_type', 'size', 'path', 'uploaded_by', 'version'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(ControlTask::class, 'control_task_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function delete()
    {
        Storage::delete($this->path);
        return parent::delete();
    }
}