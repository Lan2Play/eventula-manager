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
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('owner_id')->nullable()->after('manager_id');

            $table->foreign('manager_id')
                ->references('id')->on('users')
                ->onDelete('set null');
            $table->foreign('owner_id')
                ->references('id')->on('users')
                ->onDelete('set null');
        });


        DB::table('tickets')->update([
            'manager_id' => DB::raw('user_id'),
            'owner_id'   => DB::raw('user_id'),
        ]);

        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable(false)->change();
            $table->unsignedBigInteger('owner_id')->nullable(false)->change();
        });
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
