<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tracker_forms', function (Blueprint $table) {
            if (! Schema::hasColumn('tracker_forms', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->onDelete('cascade');
            }
        });

        // Assign existing records to a default user or admin (you need to decide)
        // This is an example - you should assign to an appropriate user
        if (DB::table('users')->exists()) {
            $adminUser = DB::table('users')->first();
            DB::table('tracker_forms')->whereNull('user_id')->update(['user_id' => $adminUser->id]);
        }

        // Now make user_id not nullable
        Schema::table('tracker_forms', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracker_forms', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
