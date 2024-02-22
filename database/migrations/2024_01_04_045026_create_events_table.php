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
        Schema::create('events', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignId('company_id')->nullable(false);
            $table->string('code', 200)->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->string('description', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->timestamp('start_time')->nullable(false);
            $table->timestamp('end_time')->nullable(false);
            $table->text('email_template')->nullable();
            $table->text('cards_template')->nullable();
            $table->string('note', 255)->nullable();
            $table->string('status', 50)->default('NEW')->nullable(false);
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
                ->onDelete('restrict');
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
        Schema::dropIfExists('events');
    }
};
