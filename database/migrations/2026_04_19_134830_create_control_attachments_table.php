<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_task_id')->constrained('control_tasks')->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('size');
            $table->string('path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->integer('version')->default(1);
            $table->timestamps();
            
            $table->index('control_task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_attachments');
    }
};