<?php
// database/migrations/2024_xx_xx_xxxxxx_add_ticket_number_to_change_tickets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketNumberToChangeTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('change_tickets', function (Blueprint $table) {
            $table->string('ticket_number')->nullable()->after('ticket_id');
        });
    }

    public function down()
    {
        Schema::table('change_tickets', function (Blueprint $table) {
            $table->dropColumn('ticket_number');
        });
    }
}