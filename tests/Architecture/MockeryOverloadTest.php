<?php

use Symfony\Component\Finder\Finder;

it('never uses Mockery overload mocks in the test suite', function () {
    // `Mockery::mock('overload:'.Foo::class)` defines a class alias in the running PHP
    // process: it needs Foo not to be loaded yet, and once installed it replaces Foo for
    // every later test. phpunit.xml sets processIsolation="false", so nothing undoes it —
    // any test touching the real class then fails, and executionOrder="random" makes it
    // look like a flake. Fake the boundary instead (Bus::fake(), Event::fake(),
    // $this->mock()) rather than replacing the class.
    $offenders = collect(
        Finder::create()
            ->in(__DIR__.'/..')
            ->name('*.php')
            // this guard contains the needle itself
            ->notName(basename(__FILE__))
            ->files()
    )
        ->filter(fn ($file) => str_contains($file->getContents(), 'overload:'))
        ->map(fn ($file) => $file->getRelativePathname())
        ->values();

    expect($offenders->all())->toBe([], 'These test files install a Mockery overload mock, which leaks a class alias into every later test in the process. Fake the boundary instead — see CLAUDE.md.');
});
