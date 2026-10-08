<?php

declare(strict_types=1);

namespace Tests\CodingStandard\Support;

use PHPUnit\Framework\TestCase;

abstract class EcsTestCase extends TestCase
{
    /** @var EcsRunner */
    protected static $runner;

    /** @var array{phpdoc_order_configurable: bool, phpdoc_separation_configurable: bool, ecs_major: int} */
    protected static $capabilities;

    /** @var list<string> */
    private array $tempPaths = [];

    public static function setUpBeforeClass(): void
    {
        self::$runner = new EcsRunner();
        self::$capabilities = EcsCapability::detect(self::$runner->repoRoot());
    }

    protected function tearDown(): void
    {
        foreach ($this->tempPaths as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        $this->tempPaths = [];
    }

    protected function writeTempFixture(string $basename, string $contents): string
    {
        $path = sys_get_temp_dir() . '/sylius-cs-' . uniqid('', true) . '-' . $basename;
        file_put_contents($path, $contents);
        $this->tempPaths[] = $path;

        return $path;
    }

    protected function skipWhenEcs13OnPhpBelow840(): void
    {
        if (self::$capabilities['ecs_major'] >= 13 && PHP_VERSION_ID < 80400) {
            $this->markTestSkipped(
                'ECS 13 bundles PHPCS 4.x tokenizer constants that are unavailable on PHP 8.0 (see CONFLICTS.md).',
            );
        }
    }

    protected function requirePhpdocOrderConfigurable(): void
    {
        if (!self::$capabilities['phpdoc_order_configurable']) {
            $this->markTestSkipped(
                'ecs.php guard: PhpdocOrderFixer is configured only when '
                . 'is_a(PhpdocOrderFixer::class, ConfigurableFixerInterface::class, true)',
            );
        }
    }

    protected function requirePhpdocSeparationConfigurable(): void
    {
        if (!self::$capabilities['phpdoc_separation_configurable']) {
            $this->markTestSkipped(
                'ecs.php guard: PhpdocSeparationFixer is configured only when '
                . 'is_a(PhpdocSeparationFixer::class, ConfigurableFixerInterface::class, true)',
            );
        }
    }

    protected function normalizeFileContents(string $contents): string
    {
        return rtrim($contents, "\r\n") . "\n";
    }

    protected function fixThenAssertClean(string $path, ?string $configPath = null): void
    {
        $fix = self::$runner->fix([$path], $configPath);
        EcsRunner::assertNoPhpDiagnostics($fix);

        $check = self::$runner->check([$path], $configPath);
        EcsRunner::assertNoPhpDiagnostics($check);
        self::assertSame(0, $check->exitCode, $check->stdout . $check->stderr);
    }

    protected function fixThenAssertContents(string $path, string $expected, ?string $configPath = null): void
    {
        $fix = self::$runner->fix([$path], $configPath);
        EcsRunner::assertNoPhpDiagnostics($fix);
        self::assertSame(
            $this->normalizeFileContents($expected),
            $this->normalizeFileContents((string) file_get_contents($path)),
        );

        $check = self::$runner->check([$path], $configPath);
        EcsRunner::assertNoPhpDiagnostics($check);
        self::assertSame(0, $check->exitCode, $check->stdout . $check->stderr);
    }
}
