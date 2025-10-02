<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up(): void
{
Schema::rename('event_tickets', 'ticket_types');

Schema::rename('event_participants', 'tickets');
Schema::rename('event_ticket_groups', 'ticket_groups');
}

public function down(): void
{
    Schema::rename('ticket_types', 'event_tickets' );

    Schema::rename('tickets', 'event_participants');
    Schema::rename('ticket_groups', 'event_ticket_groups');}
};
