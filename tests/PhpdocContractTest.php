<?php

declare(strict_types=1);

namespace Tests\CodingStandard;

use Tests\CodingStandard\Support\EcsTestCase;

final class PhpdocContractTest extends EcsTestCase
{
    public function testForbiddenAnnotationsAreRemoved(): void
    {
        $this->skipWhenEcs13OnPhpBelow840();

        $path = $this->writeTempFixture(
            'ForbiddenAnnotations.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ForbiddenAnnotations
{
    /**
     * @api
     * @author Someone
     * @category Cat
     * @copyright 2020
     * @created 2020-01-01
     * @license MIT
     * @package Pkg
     * @since 1.0
     * @subpackage Sub
     * @version 1.0
     * @param int $value
     */
    public function run($value): void
    {
    }
}
PHP,
        );

        $fix = self::$runner->fix([$path]);
        \Tests\CodingStandard\Support\EcsRunner::assertNoPhpDiagnostics($fix);

        $contents = (string) file_get_contents($path);
        foreach (['@api', '@author', '@category', '@copyright', '@created', '@license', '@package', '@since', '@subpackage', '@version'] as $tag) {
            self::assertStringNotContainsString($tag, $contents);
        }
        self::assertStringContainsString('@param int $value', $contents);

        $this->fixThenAssertClean($path);
    }

    public function testGivenWhenThenOrder(): void
    {
        $this->skipWhenEcs13OnPhpBelow840();
        $this->requirePhpdocOrderConfigurable();

        $path = $this->writeTempFixture(
            'BehatOrder.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class BehatOrder
{
    /**
     * @Then
     * @Given
     * @When
     */
    public function run(): void
    {
    }
}
PHP,
        );

        $this->fixThenAssertContents(
            $path,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class BehatOrder
{
    /**
     * @Given
     * @When
     * @Then
     */
    public function run(): void
    {
    }
}
PHP,
        );
    }

    public function testGivenWhenThenAreOneGroup(): void
    {
        $this->skipWhenEcs13OnPhpBelow840();
        $this->requirePhpdocSeparationConfigurable();

        $path = $this->writeTempFixture(
            'BehatGroup.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class BehatGroup
{
    /**
     * @Given
     *
     * @When
     * @Then
     *
     * @param int $value
     */
    public function run($value): void
    {
    }
}
PHP,
        );

        $this->fixThenAssertContents(
            $path,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class BehatGroup
{
    /**
     * @Given
     * @When
     * @Then
     *
     * @param int $value
     */
    public function run($value): void
    {
    }
}
PHP,
        );
    }

    public function testParamAndReturnGroups(): void
    {
        $this->skipWhenEcs13OnPhpBelow840();
        $this->requirePhpdocSeparationConfigurable();

        $path = $this->writeTempFixture(
            'ParamReturnGroups.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ParamReturnGroups
{
    /**
     * @param int $bar
     * @phpstan-param string $baz
     * @psalm-param string $baz
     *
     * @return string[]
     * @phpstan-return string<int, string>
     * @psalm-return string<int, string>
     */
    public function run($bar, $baz)
    {
        return [$baz];
    }
}
PHP,
        );

        $fixed = self::$runner->fix([$path]);
        $contents = file_get_contents($path);
        self::assertStringContainsString('@param int $bar', $contents);
        self::assertStringContainsString('@phpstan-param string $baz', $contents);
        self::assertStringNotContainsString("@param int \$bar\n     * @phpstan-param", $contents);
        $this->fixThenAssertClean($path);
    }

    public function testVarGroup(): void
    {
        $this->skipWhenEcs13OnPhpBelow840();
        $this->requirePhpdocSeparationConfigurable();

        $path = $this->writeTempFixture(
            'VarGroup.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class VarGroup
{
    public function run($bar, $baz): array
    {
        /**
         * @var int $key
         *
         * @phpstan-var int $key
         * @psalm-var int $key
         */
        $key = --$bar;

        return [$key => $baz];
    }
}
PHP,
        );

        $this->fixThenAssertContents(
            $path,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class VarGroup
{
    public function run($bar, $baz): array
    {
        /**
         * @var int $key
         * @phpstan-var int $key
         * @psalm-var int $key
         */
        $key = --$bar;

        return [$key => $baz];
    }
}
PHP,
        );
    }

    public function testTemplateGroup(): void
    {
        $this->skipWhenEcs13OnPhpBelow840();
        $this->requirePhpdocSeparationConfigurable();

        $path = $this->writeTempFixture(
            'TemplateGroup.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

/**
 * @phpstan-template T
 *
 * @psalm-template T
 * @template T
 *
 * @template-extends \IteratorAggregate<T>
 * @extends \IteratorAggregate<T>
 *
 * @template-implements \IteratorAggregate<T>
 * @implements \IteratorAggregate<T>
 *
 * @template-covariant T
 * @psalm-template-covariant T
 */
final class TemplateGroup
{
}
PHP,
        );

        $fixed = self::$runner->fix([$path]);
        $contents = file_get_contents($path);
        self::assertStringNotContainsString("@template T\n *\n * @template-extends", $contents);
        $this->fixThenAssertClean($path);
    }
}
