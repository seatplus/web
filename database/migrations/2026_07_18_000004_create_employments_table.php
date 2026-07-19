<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            // The observed subject mirrors an application's applicationable: a whole User (account-wide)
            // or a single CharacterInfo.
            $table->morphs('subject');
            $table->unsignedBigInteger('corporation_id');
            // Provenance — the application this hire came from. Null for pre-existing members who never
            // applied (reconciled from corporation membership); their compliance is observed all the same.
            $table->uuid('application_id')->nullable();
            $table->enum('status', ['active', 'suspended', 'alumni'])->default('active');
            $table->timestamp('hired_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['corporation_id', 'status']);
        });
    }
};
