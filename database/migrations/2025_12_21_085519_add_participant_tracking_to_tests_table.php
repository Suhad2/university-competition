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
        Schema::table('tests', function (Blueprint $table) {
            if (!Schema::hasColumn('tests', 'ready_participants')) {
                $table->json('ready_participants')
                      ->nullable()
                      ->after('current_question_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            if (Schema::hasColumn('tests', 'ready_participants')) {
                $table->dropColumn('ready_participants');
            }
        });
    }
};
