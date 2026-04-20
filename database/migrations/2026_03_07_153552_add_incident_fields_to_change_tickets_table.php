<?php
// database/migrations/2024_xx_xx_xxxxxx_add_incident_fields_to_change_tickets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIncidentFieldsToChangeTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('change_tickets', function (Blueprint $table) {
            $table->text('incident_description')->nullable()->after('incident_num');
            $table->text('incident_actions')->nullable()->after('incident_description');
            $table->datetime('incident_resolved_at')->nullable()->after('incident_actions');
            $table->string('incident_impact_residuel')->nullable()->after('incident_resolved_at');
            $table->json('incident_files')->nullable()->after('incident_impact_residuel');
        });
    }

    public function down()
    {
        Schema::table('change_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'incident_description',
                'incident_actions',
                'incident_resolved_at',
                'incident_impact_residuel',
                'incident_files'
            ]);
        });
    }
}