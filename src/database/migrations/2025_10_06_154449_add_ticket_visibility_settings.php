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
            $table->enum('visibility_policy', ['always', 'hide', 'inherit'])
                ->after('event_ticket_group_id')
                ->default('inherit');
        });

        Schema::table('events', function (Blueprint $table) {
           $table->boolean('hide_non_purchasable')->default(false)->after('no_tickets_per_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn('visibility_policy');
        });

        Schema::table('events', function (Blueprint $table) {$table->dropColumn('hide_non_purchasable');
        });
    }
};
