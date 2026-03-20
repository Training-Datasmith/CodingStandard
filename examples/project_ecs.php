<?php

declare(strict_types=1);

/**
 * Example: Use sylius-labs/coding-standard in your project's ecs.php.
 *
 * Place this file at the project root as `ecs.php` and run:
 *   vendor/bin/ecs check src/
 *   vendor/bin/ecs fix src/
 */

use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    // 1. Load the shared Sylius Labs rule set.
    $ecsConfig->import(__DIR__ . '/vendor/sylius-labs/coding-standard/ecs.php');

    // 2. Specify which directories to lint.
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // 3. Add project-specific skip rules on top of the shared config.
    // For example, skip a specific file from a particular fixer:
    // $ecsConfig->skip([
    //     \PhpCsFixer\Fixer\Basic\BracesFixer::class => [__DIR__ . '/src/Legacy/OldFile.php'],
    // ]);
};
