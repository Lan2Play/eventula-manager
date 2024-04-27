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
        Schema::create('event_ticket_groups', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->string('name');
            $table->integer('tickets_per_user')->unsigned();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::table('event_tickets', function(Blueprint $table) {
            $table->integer('event_ticket_group_id')->unsigned()->nullable()->after('no_tickets_per_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('event_ticket_groups');

        Schema::table('event_tickets', function (Blueprint $table) {
            $table->dropColumn('event_ticket_group_id');
        });
    }
};
