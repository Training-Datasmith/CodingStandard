<?php

declare(strict_types=1);

namespace Tests\CodingStandard;

use Tests\CodingStandard\Support\EcsRunner;
use Tests\CodingStandard\Support\EcsTestCase;

final class ShippedFixturesTest extends EcsTestCase
{
    public function testCommittedFixturesAndConfigAreClean(): void
    {
        $root = self::$runner->repoRoot();
        $paths = [
            $root . '/ecs.php',
            $root . '/tests/Sample.php',
            $root . '/tests/SampleSpec.php',
            $root . '/tests/FooBar.php',
            $root . '/tests/BehatContext.php',
            $root . '/tests/Generics.php',
        ];

        if (self::$capabilities['ecs_major'] < 13) {
            $paths[] = $root . '/tests/Annotations.php';
        }

        $result = self::$runner->check($paths);
        EcsRunner::assertNoPhpDiagnostics($result);
        self::assertSame(0, $result->exitCode, $result->stdout . $result->stderr);
    }
}
