<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_group_members', function (Blueprint $table) {
            $table->id();
            // The group identity — applications sharing a group_id were applied together and are
            // reviewed and decided as one, while each stays its own (compliance-scoped) application.
            $table->uuid('group_id');
            // Soft ref to eveapi applications.id (uuid) — no cross-package FK. One group per application.
            $table->uuid('application_id')->unique();
            $table->timestamps();

            $table->index('group_id');
        });
    }
};
