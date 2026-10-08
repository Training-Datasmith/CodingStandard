<?php

declare(strict_types=1);

namespace Tests\CodingStandard\Support;

final class EcsRunner
{
    private const TIMEOUT_SECONDS = 120;

    private string $repoRoot;

    private string $ecsBinary;

    private string $defaultConfigPath;

    public function __construct(?string $repoRoot = null)
    {
        $this->repoRoot = $repoRoot ?? dirname(__DIR__, 2);
        $this->ecsBinary = $this->repoRoot . '/vendor/bin/ecs';
        $this->defaultConfigPath = $this->repoRoot . '/ecs.php';
    }

    public function check(array $absolutePaths, ?string $configPath = null): EcsResult
    {
        return $this->run(false, $absolutePaths, $configPath);
    }

    public function fix(array $absolutePaths, ?string $configPath = null): EcsResult
    {
        return $this->run(true, $absolutePaths, $configPath);
    }

    public function repoRoot(): string
    {
        return $this->repoRoot;
    }

    public function defaultConfigPath(): string
    {
        return $this->defaultConfigPath;
    }

    public static function assertNoPhpDiagnostics(EcsResult $result): void
    {
        $combined = $result->stdout . "\n" . $result->stderr;
        $patterns = [
            'Deprecated:',
            'Warning:',
            'Notice:',
            'Fatal error',
            'uncaught exception',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($combined, $pattern)) {
                throw new \PHPUnit\Framework\AssertionFailedError(
                    sprintf('ECS output contained PHP diagnostic "%s": %s', $pattern, $combined),
                );
            }
        }
    }

    /**
     * @param list<string> $absolutePaths
     */
    private function run(bool $fix, array $absolutePaths, ?string $configPath): EcsResult
    {
        $config = $configPath ?? $this->defaultConfigPath;
        $phpBinary = defined('PHP_BINARY') ? \PHP_BINARY : 'php';

        $command = array_merge(
            [$phpBinary, $this->ecsBinary, 'check', '--clear-cache', '--config', $config],
            $fix ? ['--fix'] : [],
            $absolutePaths,
        );

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes, $this->repoRoot);

        if (!is_resource($process)) {
            throw new \RuntimeException('Failed to start ECS process.');
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], true);
        stream_set_blocking($pipes[2], true);

        $stdout = '';
        $stderr = '';

        while ($stdout === '' && $stderr === '' && proc_get_status($process)['running']) {
            usleep(10_000);
        }

        $readStart = time();
        while (proc_get_status($process)['running']) {
            $stdout .= stream_get_contents($pipes[1]);
            $stderr .= stream_get_contents($pipes[2]);

            if ((time() - $readStart) >= self::TIMEOUT_SECONDS) {
                proc_terminate($process);
                proc_close($process);

                throw new \RuntimeException(
                    sprintf('ECS timed out after %d seconds.', self::TIMEOUT_SECONDS),
                );
            }

            usleep(50_000);
        }

        $stdout .= stream_get_contents($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $status = proc_get_status($process);
        $exitCode = proc_close($process);

        if ($exitCode === -1 && $status['exitcode'] >= 0) {
            $exitCode = $status['exitcode'];
        }

        $combined = $stdout . $stderr;

        if (str_contains($combined, '[ERROR]')) {
            $exitCode = 1;
        } elseif ($exitCode === -1 && str_contains($combined, '[OK]')) {
            $exitCode = 0;
        }

        return new EcsResult($exitCode, $stdout, $stderr);
    }
}
