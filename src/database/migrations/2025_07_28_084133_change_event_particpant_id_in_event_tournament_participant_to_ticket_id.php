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

        Schema::table('event_tournament_participants', function (Blueprint $table) {
            $table->dropForeign('event_tournament_participants_event_participant_id_foreign');;

            $table->renameColumn('event_participant_id', 'ticket_id');

            $table->foreign('ticket_id')
                ->references('id')
                ->on('tickets')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);

            $table->renameColumn('ticket_id', 'event_participant_id');

            $table->foreign('event_participant_id')
                ->references('id')
                ->on('event_participants')
                ->onDelete('cascade');
    });
    }
};
