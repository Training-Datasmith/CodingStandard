<?php

declare(strict_types=1);

namespace Tests\CodingStandard\Support;

use Composer\InstalledVersions;

final class EcsCapability
{
    /** @var array{phpdoc_order_configurable: bool, phpdoc_separation_configurable: bool, ecs_major: int}|null */
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
        $ecsBinary = $repoRoot . '/vendor/bin/ecs';
        $configPath = $repoRoot . '/tests/Support/cap-ecs.php';
        $outputFile = sys_get_temp_dir() . '/sylius-cs-ecs-cap-' . uniqid('', true) . '.json';

        $command = implode(' ', [
            escapeshellarg($phpBinary),
            escapeshellarg($ecsBinary),
            'check',
            '--clear-cache',
            '--config',
            escapeshellarg($configPath),
        ]);

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $env = array_merge(
            getenv() ?: [],
            ['ECS_CAP_OUTPUT' => $outputFile],
        );

        $process = proc_open($command, $descriptorSpec, $pipes, $repoRoot, $env);

        if (!is_resource($process)) {
            throw new \RuntimeException('Failed to start ECS capability detection process.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if (!is_file($outputFile)) {
            throw new \RuntimeException(
                'ECS capability detection did not write output: '
                . trim($stderr !== '' ? $stderr : $stdout),
            );
        }

        $raw = file_get_contents($outputFile);
        unlink($outputFile);

        /** @var array{phpdoc_order_configurable: bool, phpdoc_separation_configurable: bool} $decoded */
        $decoded = json_decode(trim((string) $raw), true, 512, \JSON_THROW_ON_ERROR);

        if ($exitCode !== 0 && $exitCode !== 1 && $exitCode !== 2) {
            throw new \RuntimeException(
                'ECS capability detection failed (exit ' . $exitCode . '): '
                . trim($stderr !== '' ? $stderr : $stdout),
            );
        }

        $ecsVersion = InstalledVersions::getVersion('symplify/easy-coding-standard');
        $ecsMajor = $ecsVersion !== null ? (int) explode('.', $ecsVersion)[0] : 0;

        self::$cache = [
            'phpdoc_order_configurable' => (bool) $decoded['phpdoc_order_configurable'],
            'phpdoc_separation_configurable' => (bool) $decoded['phpdoc_separation_configurable'],
            'ecs_major' => $ecsMajor,
        ];

        return self::$cache;
    }
}
