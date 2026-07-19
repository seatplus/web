<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_round_reviews', function (Blueprint $table) {
            $table->id();
            // Soft ref to eveapi applications.id (uuid) — no cross-package FK.
            $table->uuid('application_id');
            $table->unsignedInteger('position');
            // The control-group the reviewer acted as (null = legacy flat-permission review).
            $table->unsignedBigInteger('role_id')->nullable();
            $table->nullableMorphs('causer');
            $table->enum('decision', ['accepted', 'rejected']);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('application_id');
        });
    }
};
