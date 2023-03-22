<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets
    $rectorConfig->sets([
        //SetList::CODE_QUALITY,
        LaravelSetList::LARAVEL_100,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        SetList::PHP_81,
    ]);
    // paths to refactor; solid alternative to CLI arguments
    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);

    // is your PHP version different from the one you refactor to? [default: your PHP version], uses PHP_VERSION_ID format
    $rectorConfig->phpVersion(PhpVersion::PHP_81);

    $rectorConfig->skip([
        RemoveExtraParametersRector::class => [ __DIR__ . '/src/Services/SsoSettings/UpdateOrCreateSsoSettings.php'],
        FirstClassCallableRector::class => [
            __DIR__ . '/src/Http/Controllers/AccessControl/ControlGroupsController.php',
            __DIR__ . '/src/Http/Controllers/Shared/ManualLocationController.php',
    ]
    ]);

    // register single rule
    //$rectorConfig->rule(TypedPropertyRector::class);
};
