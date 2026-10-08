<?php

declare(strict_types=1);

namespace Tests\CodingStandard;

use Tests\CodingStandard\Support\EcsTestCase;

final class ConfiguredRuleTest extends EcsTestCase
{
    /**
     * @return iterable<string, array{0: string, 1: string}>
     */
    public function configuredRuleCases(): iterable
    {
        yield 'shortArray' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ShortArray
{
    public function run(): void
    {
        $items = array(1, 2);
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ShortArray
{
    public function run(): void
    {
        $items = [1, 2];
    }
}
PHP,
        ];

        yield 'singleLineClosureKept' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class SingleLineClosureKept
{
    public function run(): void
    {
        $fn = function () { return 1; };
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class SingleLineClosureKept
{
    public function run(): void
    {
        $fn = function () { return 1; };
    }
}
PHP,
        ];

        yield 'singleItemExtends' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

final class Foo
implements
Bar
{
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

final class Foo implements Bar
{
}
PHP,
        ];

        yield 'concatOneSpace' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ConcatOneSpace
{
    public function run(): string
    {
        return 'a'.'b';
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ConcatOneSpace
{
    public function run(): string
    {
        return 'a' . 'b';
    }
}
PHP,
        ];

        yield 'lowercaseConstantsOnly' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class LowercaseConstantsOnly
{
    public const BAR = 1;

    public function run(): void
    {
        if (TRUE) {
            $x = NULL;
        }
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class LowercaseConstantsOnly
{
    public const BAR = 1;

    public function run(): void
    {
        if (true) {
            $x = null;
        }
    }
}
PHP,
        ];

        yield 'preIncrementStatementOnly' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class PreIncrementStatementOnly
{
    public function run(): int
    {
        $i = 0;
        $i++;

        return $i++;
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class PreIncrementStatementOnly
{
    public function run(): int
    {
        $i = 0;
        ++$i;

        return $i++;
    }
}
PHP,
        ];

        yield 'shortList' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ShortList
{
    public function run(array $x): void
    {
        list($a, $b) = $x;
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ShortList
{
    public function run(array $x): void
    {
        [$a, $b] = $x;
    }
}
PHP,
        ];

        yield 'noExtraBlankLineUseImport' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

use DateTime;

use ArrayObject;

class NoExtraBlankLineUseImport
{
    public function run(): DateTime
    {
        return new DateTime((string) new ArrayObject());
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

use ArrayObject;
use DateTime;

class NoExtraBlankLineUseImport
{
    public function run(): DateTime
    {
        return new DateTime((string) new ArrayObject());
    }
}
PHP,
        ];

        yield 'noExtraBlankLineAfterArrayOpen' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class NoExtraBlankLineAfterArrayOpen
{
    public function run(): array
    {
        return [

            1,
            2,
        ];
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class NoExtraBlankLineAfterArrayOpen
{
    public function run(): array
    {
        return [
            1,
            2,
        ];
    }
}
PHP,
        ];

        yield 'printBecomesEcho' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class PrintBecomesEcho
{
    public function run(): void
    {
        print 'x';
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class PrintBecomesEcho
{
    public function run(): void
    {
        echo 'x';
    }
}
PHP,
        ];

        yield 'mixedTagKept' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class MixedTagKept
{
    /**
     * @param mixed $value
     */
    public function run($value): void
    {
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class MixedTagKept
{
    /**
     * @param mixed $value
     */
    public function run($value): void
    {
    }
}
PHP,
        ];

        yield 'booleanBreakOnly' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class BooleanBreakOnly
{
    public function run(bool $a, bool $b, string $x, string $y): bool
    {
        return $a
            && $b;

        $z = $x
            . $y;

        return true;
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class BooleanBreakOnly
{
    public function run(bool $a, bool $b, string $x, string $y): bool
    {
        return $a &&
            $b;

        $z = $x
            . $y;

        return true;
    }
}
PHP,
        ];

        yield 'nullLastNoAlphaSort' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class NullLastNoAlphaSort
{
    /**
     * @param null|string|int $value
     */
    public function run($value): void
    {
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class NullLastNoAlphaSort
{
    /**
     * @param string|int|null $value
     */
    public function run($value): void
    {
    }
}
PHP,
        ];

        yield 'hashComment' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class HashComment
{
    public function run(): void
    {
        # comment
        /* note */
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class HashComment
{
    public function run(): void
    {
        // comment
        /* note */
    }
}
PHP,
        ];

        yield 'trailingCommas' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class TrailingCommas
{
    public function run($a, $b): void
    {
        $items = [
            1,
            2
        ];
        $this->helper(
            $a,
            $b
        );
    }

    private function helper(
        $first,
        $second
    ): void {
        $single = [1, 2,];
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class TrailingCommas
{
    public function run($a, $b): void
    {
        $items = [
            1,
            2,
        ];
        $this->helper(
            $a,
            $b,
        );
    }

    private function helper(
        $first,
        $second,
    ): void {
        $single = [1, 2];
    }
}
PHP,
        ];

        yield 'visibilityOnConstPropertyMethod' => [
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class VisibilityOnConstPropertyMethod
{
    const BAR = 1;

    var $foo;

    function run(): void
    {
    }
}
PHP,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class VisibilityOnConstPropertyMethod
{
    public const BAR = 1;

    public $foo;

    public function run(): void
    {
    }
}
PHP,
        ];
    }

    /**
     * @dataProvider configuredRuleCases
     */
    public function testConfiguredRule(string $input, string $expected): void
    {
        $path = $this->writeTempFixture('Configured.php', $input);
        $this->fixThenAssertContents($path, $expected);
    }

    public function testMultiLineExtends(): void
    {
        $path = $this->writeTempFixture(
            'MultiLineExtends.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

interface Foo extends Bar,
    Baz
{
}
PHP,
        );

        $this->fixThenAssertContents(
            $path,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

interface Foo extends
    Bar,
    Baz
{
}
PHP,
        );
    }

    public function testSpecSkipIsPerRule(): void
    {
        $path = $this->writeTempFixture(
            'FooSpec.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class FooSpec
{
    const BAR = 1;

    var $foo;

    function run(): void
    {
        $items = array(1);
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

class FooSpec
{
    const BAR = 1;

    var $foo;

    function run(): void
    {
        $items = [1];
    }
}
PHP,
        );
    }
}
