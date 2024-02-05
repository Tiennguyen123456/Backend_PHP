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
        Schema::table('users', function (Blueprint $table) {
            $table->after('id', function ($table) {
                $table->foreignId('company_id')
                    ->nullable();
                $table->foreignId('event_id')
                    ->nullable();
            });

            $table->unique(['company_id', 'username']);

            /* INDEX */
            $table->index('id', 'idx_id');
            $table->index('company_id', 'idx_company_id');
            $table->index('event_id', 'idx_event_id');
            $table->index('username', 'idx_username');
            $table->index('email', 'idx_email');

            /* RELATIONSHIP */
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('restrict');
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['event_id']);
            $table->dropUnique(['company_id', 'username']);
            $table->dropColumn(['company_id', 'event_id']);
        });
    }
};
