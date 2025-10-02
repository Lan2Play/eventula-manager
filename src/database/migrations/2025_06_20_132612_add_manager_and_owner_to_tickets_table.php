<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedInteger('manager_id')->nullable()->index()->after('user_id');
            $table->unsignedInteger('owner_id')->nullable()->index()->after('manager_id');
        
            $table->foreign('manager_id')
                ->references('id')->on('users')
                ->onDelete('set null');
            $table->foreign('owner_id')
                ->references('id')->on('users')
                ->onDelete('set null');
        });

        // Überprüfe zuerst die Existenz der Benutzer
        DB::statement('
        UPDATE tickets t
        INNER JOIN users u ON t.user_id = u.id
        SET t.manager_id = t.user_id,
            t.owner_id = t.user_id
        WHERE t.user_id IS NOT NULL
        ');

        // Überprüfe auf NULL-Werte vor der Änderung
        if (DB::table('tickets')->whereNull('manager_id')->orWhereNull('owner_id')->exists()) {
            throw new \RuntimeException('Tickets left with NULL-Values for manager_id or owner_id');
        }

        // Foreign Key Constraints wieder aktivieren
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['manager_id', 'owner_id']);
        });
    }
};