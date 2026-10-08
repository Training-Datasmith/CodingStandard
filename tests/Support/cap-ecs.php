<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $outputFile = getenv('ECS_CAP_OUTPUT');
    if ($outputFile === false || $outputFile === '') {
        throw new \RuntimeException('ECS_CAP_OUTPUT must be set when using cap-ecs.php.');
    }

    file_put_contents(
        $outputFile,
        json_encode([
            'phpdoc_order_configurable' => is_a(PhpdocOrderFixer::class, ConfigurableFixerInterface::class, true),
            'phpdoc_separation_configurable' => is_a(PhpdocSeparationFixer::class, ConfigurableFixerInterface::class, true),
        ], \JSON_THROW_ON_ERROR),
    );

    $ecsConfig->paths([__DIR__ . '/capability-stub.php']);
};
