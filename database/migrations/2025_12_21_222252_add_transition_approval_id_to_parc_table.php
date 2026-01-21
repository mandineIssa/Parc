<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('parc', function (Blueprint $table) {
            $table->foreignId('transition_approval_id')->nullable()->constrained('transition_approvals');
        });
    }

    public function down()
    {
        Schema::table('parc', function (Blueprint $table) {
            $table->dropForeign(['transition_approval_id']);
            $table->dropColumn('transition_approval_id');
        });
    }
};