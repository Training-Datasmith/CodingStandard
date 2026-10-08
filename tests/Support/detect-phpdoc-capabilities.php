<?php

declare(strict_types=1);

/**
 * @deprecated Capability detection runs through ECS (see cap-ecs.php and EcsCapability).
 */
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Composer\InstalledVersions;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;

$ecsVersion = InstalledVersions::getVersion('symplify/easy-coding-standard');
$ecsMajor = $ecsVersion !== null ? (int) explode('.', $ecsVersion)[0] : 0;

echo json_encode([
    'phpdoc_order_configurable' => is_a(PhpdocOrderFixer::class, ConfigurableFixerInterface::class, true),
    'phpdoc_separation_configurable' => is_a(PhpdocSeparationFixer::class, ConfigurableFixerInterface::class, true),
    'ecs_major' => $ecsMajor,
], \JSON_THROW_ON_ERROR);
