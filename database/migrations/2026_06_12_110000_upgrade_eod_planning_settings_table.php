<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('eod_planning_settings')) {
            return;
        }

        Schema::table('eod_planning_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('eod_planning_settings', 'notify_on_publish')) {
                $table->boolean('notify_on_publish')->default(true)->after('id');
            }
            if (! Schema::hasColumn('eod_planning_settings', 'notify_supervisor_on_publish')) {
                $table->boolean('notify_supervisor_on_publish')->default(false)->after('notify_on_publish');
            }
            if (! Schema::hasColumn('eod_planning_settings', 'reminder_same_day')) {
                $table->boolean('reminder_same_day')->default(true)->after('reminder_enabled');
            }
            if (! Schema::hasColumn('eod_planning_settings', 'reminder_same_day_time')) {
                $table->time('reminder_same_day_time')->default('08:00')->after('reminder_same_day');
            }
            if (! Schema::hasColumn('eod_planning_settings', 'reminder_day_before')) {
                $table->boolean('reminder_day_before')->default(true)->after('reminder_same_day_time');
            }
            if (! Schema::hasColumn('eod_planning_settings', 'reminder_day_before_time')) {
                $table->time('reminder_day_before_time')->default('17:00')->after('reminder_day_before');
            }
            if (! Schema::hasColumn('eod_planning_settings', 'default_supervisor_user_id')) {
                $table->foreignId('default_supervisor_user_id')->nullable()->after('reminder_day_before_time')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('eod_planning_settings', 'max_reminders_per_day')) {
                $table->unsignedTinyInteger('max_reminders_per_day')->default(2)->after('default_supervisor_name');
            }
        });

        // Migrer les anciennes colonnes si présentes
        if (Schema::hasColumn('eod_planning_settings', 'notify_on_assign')) {
            \DB::table('eod_planning_settings')->update([
                'notify_on_publish' => \DB::raw('notify_on_assign'),
            ]);
        }
        if (Schema::hasColumn('eod_planning_settings', 'notify_supervisor')) {
            \DB::table('eod_planning_settings')->update([
                'notify_supervisor_on_publish' => \DB::raw('notify_supervisor'),
            ]);
        }
        if (Schema::hasColumn('eod_planning_settings', 'reminder_time')) {
            \DB::table('eod_planning_settings')->update([
                'reminder_same_day_time' => \DB::raw('reminder_time'),
            ]);
        }
    }

    public function down(): void
    {
        // Pas de rollback destructif
    }
};
