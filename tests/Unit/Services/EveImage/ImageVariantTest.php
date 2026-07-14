<?php

use Seatplus\Web\Services\EveImage\ImageVariant;

it('maps an inventory category to its EVE Image Server variation', function (?int $categoryId, string $expected) {
    expect(ImageVariant::forCategory($categoryId))->toBe($expected);
})->with([
    'ship (6) → render' => [6, 'render'],
    'blueprint (9) → bp' => [9, 'bp'],
    'other category → icon' => [17, 'icon'],
    'unknown/null → icon' => [null, 'icon'],
]);
