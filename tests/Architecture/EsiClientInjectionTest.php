<?php

use Symfony\Component\Finder\Finder;

it('never resolves EsiClient from the container in production code', function () {
    $offenders = collect(Finder::create()->in(__DIR__.'/../../src')->name('*.php')->files())
        ->filter(fn ($file) => preg_match(
            '/app\(\s*\\\\?(Seatplus\\\\EsiClient\\\\)?EsiClient::class/',
            $file->getContents()
        ) === 1)
        ->map(fn ($file) => $file->getRelativePathname())
        ->values();

    expect($offenders)->toBeEmpty();
});
