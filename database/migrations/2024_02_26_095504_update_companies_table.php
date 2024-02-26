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
        Schema::table('companies', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedSmallInteger('limited_users')->default(0)->change();
            $table->unsignedSmallInteger('limited_events')->default(0)->change();
            $table->unsignedSmallInteger('limited_campaigns')->default(0)->change();
            $table->string('tax_code', 50)->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
