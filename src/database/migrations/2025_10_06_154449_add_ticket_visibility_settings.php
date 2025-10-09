<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->tinyInteger('tickettype_hide_policy')
                ->after('event_ticket_group_id')
                ->default(-1);
        });

        Schema::table('events', function (Blueprint $table) {
           $table->tinyInteger('tickettype_hide_policy')
               ->after('no_tickets_per_user')
               ->default(-1);
        });

        DB::table('settings')->insert([
            'setting' => 'tickettype_hide_policy',
            'value' => '0',
            'default' => 1,
            'description' => 'The Policy what tickets to hide.'
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn('hide_policy');
        });

        Schema::table('events', function (Blueprint $table) {$table->dropColumn('tickettype_hide_policy');
        });

        DB::table('settings')->where('setting', 'tickettype_hide_policy')->delete();
    }
};
