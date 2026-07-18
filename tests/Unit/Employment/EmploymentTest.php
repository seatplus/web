<?php

use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Enums\EmploymentStatus;
use Seatplus\Web\Models\Employment\Employment;

it('creates an active account-wide employment by default', function () {
    $employment = Employment::factory()->create();

    expect($employment->status)->toBe(EmploymentStatus::Active)
        ->and($employment->subject)->toBeInstanceOf(User::class)
        ->and($employment->corporation)->toBeInstanceOf(CorporationInfo::class)
        ->and($employment->application_id)->toBeNull()
        ->and($employment->hired_at)->not->toBeNull();
});

it('supports a single-character subject', function () {
    $employment = Employment::factory()->character()->create();

    expect($employment->subject)->toBeInstanceOf(CharacterInfo::class);
});

it('scopes active and alumni employments', function () {
    Employment::factory()->count(2)->create();
    Employment::factory()->alumni()->create();

    expect(Employment::active()->count())->toBe(2)
        ->and(Employment::alumni()->count())->toBe(1);

    $alumnus = Employment::alumni()->first();

    expect($alumnus->status)->toBe(EmploymentStatus::Alumni)
        ->and($alumnus->ended_at)->not->toBeNull();
});
