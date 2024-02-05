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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable();
            $table->boolean('is_default')
                ->default(false)
                ->nullable(false);
            $table->string('code', 200)
                ->nullable(false)
                ->unique();
            $table->string('name', 255)
                ->nullable(false);
            $table->string('contact_email', 255)
                ->nullable();
            $table->string('contact_phone', 255)
                ->nullable();
            $table->string('website', 255)
                ->nullable();
            $table->string('address', 255)
                ->nullable();
            $table->string('city', 255)
                ->nullable();
            $table->integer('limited_users')
                ->nullable();
            $table->integer('limited_events')
                ->nullable();
            $table->integer('limited_campaigns')
                ->nullable();
            $table->string('status', 50)
                ->default('NEW')
                ->nullable(false);
            $table->foreignId('created_by')
                ->nullable();
            $table->foreignId('updated_by')
                ->nullable();
            $table->timestamps();

            /* INDEX */
            $table->index('id', 'idx_id');
            $table->index('parent_id', 'idx_parent_id');
            $table->index('created_by', 'idx_created_by');
            $table->index('updated_by', 'idx_updated_by');

            /* RELATIONSHIP */
            $table->foreign('parent_id')
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
        Schema::dropIfExists('companies');
    }
};
