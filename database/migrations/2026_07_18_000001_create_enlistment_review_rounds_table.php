<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enlistment_review_rounds', function (Blueprint $table) {
            $table->id();
            // The posting is the enlistments row (PK = corporation_id); closing recruitment removes its rounds.
            $table->unsignedBigInteger('corporation_id');
            $table->foreign('corporation_id')
                ->references('corporation_id')
                ->on('enlistments')
                ->onDelete('cascade');
            $table->unsignedInteger('position');
            // Control-group (auth roles.id) whose members review this round. Null falls back to the
            // flat 'can accept or deny applications' permission (legacy behaviour). Soft ref, no cross-package FK.
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('label');
            $table->timestamps();

            $table->unique(['corporation_id', 'position']);
        });
    }
};
