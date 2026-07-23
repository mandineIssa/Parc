<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eod_batch_weeks', function (Blueprint $table) {
            $table->id();
            $table->date('week_start')->unique();
            $table->string('status', 20)->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('eod_batch_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('week_id')->constrained('eod_batch_weeks')->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->unsignedTinyInteger('day_of_week');
            $table->foreignId('assignee_user_id')->constrained('users');
            $table->foreignId('supervisor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('supervisor_name')->nullable();
            $table->timestamp('assignment_notified_at')->nullable();
            $table->timestamp('last_reminder_at')->nullable();
            $table->unsignedSmallInteger('reminder_count')->default(0);
            $table->timestamps();

            $table->unique(['week_id', 'scheduled_date']);
        });

        Schema::create('eod_planning_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('notify_on_publish')->default(true);
            $table->boolean('notify_supervisor_on_publish')->default(false);
            $table->boolean('reminder_enabled')->default(true);
            $table->time('reminder_same_day_time')->default('08:00');
            $table->boolean('reminder_same_day')->default(true);
            $table->boolean('reminder_day_before')->default(true);
            $table->time('reminder_day_before_time')->default('17:00');
            $table->foreignId('default_supervisor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('default_supervisor_name')->default('NDICK/MAR');
            $table->unsignedTinyInteger('max_reminders_per_day')->default(2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eod_planning_settings');
        Schema::dropIfExists('eod_batch_assignments');
        Schema::dropIfExists('eod_batch_weeks');
    }
};
