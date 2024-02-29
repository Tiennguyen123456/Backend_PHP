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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('company_id')->nullable();
            $table->foreignId('event_id')->nullable(false);
            $table->timestamp('run_time')->nullable(false);
            $table->text('filter_client')->nullable()
                ->comment('Filter of client');
            $table->string('status', 10)->default('NEW')->nullable(false)
                ->comment('NEW, RUNNING, PAUSED, STOPPED, FINISHED, DELETED');
            $table->text('mail_content')->nullable(false);
            $table->string('mail_subject', 100)->nullable(false);
            $table->string('sender_email', 100)->nullable(false);
            $table->string('sender_name', 100)->nullable(false);

            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();

            /* INDEX */
            $table->index('id', 'idx_id');
            $table->index('company_id', 'idx_company_id');
            $table->index('created_by', 'idx_created_by');
            $table->index('updated_by', 'idx_updated_by');

            /* RELATIONSHIP */
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
