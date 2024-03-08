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
        Schema::create('log_send_mails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable();
            $table->string('client_id');
            $table->string('email');
            $table->string('subject');
            $table->text('content');
            $table->string('status');
            $table->string('error');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            /* INDEX */
            $table->index('campaign_id', 'idx_company_id');

            /* RELATIONSHIP */
            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_send_mails');
    }
};
