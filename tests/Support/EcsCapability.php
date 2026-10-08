<?php

declare(strict_types=1);

namespace Tests\CodingStandard\Support;

final class EcsCapability
{
    /** @var array{phpdoc_order_configurable: bool, phpdoc_separation_configurable: bool}|null */
    private static $cache;

    /**
     * @return array{phpdoc_order_configurable: bool, phpdoc_separation_configurable: bool, ecs_major: int}
     */
    public static function detect(string $repoRoot): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $phpBinary = defined('PHP_BINARY') ? \PHP_BINARY : 'php';
        $script = $repoRoot . '/tests/Support/detect-phpdoc-capabilities.php';
        $command = escapeshellarg($phpBinary) . ' ' . escapeshellarg($script);

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes, $repoRoot);

        if (!is_resource($process)) {
            throw new \RuntimeException('Failed to start capability detection process.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new \RuntimeException(
                'Capability detection failed: ' . trim($stderr !== '' ? $stderr : $stdout),
            );
        }

        /** @var array{phpdoc_order_configurable: bool, phpdoc_separation_configurable: bool} $decoded */
        $decoded = json_decode(trim($stdout), true, 512, \JSON_THROW_ON_ERROR);
        self::$cache = $decoded;

        return self::$cache;
    }
}
