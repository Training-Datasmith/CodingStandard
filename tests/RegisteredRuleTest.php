<?php

declare(strict_types=1);

namespace Tests\CodingStandard;

use Tests\CodingStandard\Support\EcsRunner;
use Tests\CodingStandard\Support\EcsTestCase;

final class RegisteredRuleTest extends EcsTestCase
{
    public function testDeclareStrictTypesInserted(): void
    {
        $path = $this->writeTempFixture(
            'NoStrict.php',
            <<<'PHP'
<?php

namespace Fixture;

class NoStrict
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

class NoStrict
{
}
PHP,
        );
    }

    public function testUnusedImportRemovedAndImportsOrdered(): void
    {
        $path = $this->writeTempFixture(
            'Imports.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

use DateTime;
use Exception;

class Imports
{
    public function run(): void
    {
        new DateTime();
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

use DateTime;

class Imports
{
    public function run(): void
    {
        new DateTime();
    }
}
PHP,
        );
    }

    public function testIsNullBecomesComparison(): void
    {
        $path = $this->writeTempFixture(
            'IsNull.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class IsNull
{
    public function run($value): bool
    {
        return is_null($value);
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

class IsNull
{
    public function run($value): bool
    {
        return null === $value;
    }
}
PHP,
        );
    }

    public function testDirnameFileBecomesDirConstant(): void
    {
        $path = $this->writeTempFixture(
            'DirConstant.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class DirConstant
{
    public function run(): string
    {
        return dirname(__FILE__);
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

class DirConstant
{
    public function run(): string
    {
        return __DIR__;
    }
}
PHP,
        );
    }

    public function testShortScalarCast(): void
    {
        $path = $this->writeTempFixture(
            'Cast.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class Cast
{
    public function run($value): void
    {
        $a = (integer) $value;
        $b = (BOOLEAN) $value;
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

class Cast
{
    public function run($value): void
    {
        $a = (int) $value;
        $b = (bool) $value;
    }
}
PHP,
        );
    }

    public function testAliasFunction(): void
    {
        $path = $this->writeTempFixture(
            'Alias.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class Alias
{
    public function run(array $parts): string
    {
        return join(',', $parts);
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

class Alias
{
    public function run(array $parts): string
    {
        return implode(',', $parts);
    }
}
PHP,
        );
    }

    public function testErrorSuppressionAddsAtForUserDeprecated(): void
    {
        $path = $this->writeTempFixture(
            'ErrorSuppression.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class ErrorSuppression
{
    public function run(string $path): void
    {
        trigger_error('x', E_USER_DEPRECATED);
        @unlink($path);
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

class ErrorSuppression
{
    public function run(string $path): void
    {
        @trigger_error('x', \E_USER_DEPRECATED);
        @unlink($path);
    }
}
PHP,
        );
    }

    public function testPowToExponentiation(): void
    {
        $path = $this->writeTempFixture(
            'Pow.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class Pow
{
    public function run(float $a, float $b): float
    {
        return pow($a, $b);
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

class Pow
{
    public function run(float $a, float $b): float
    {
        return $a ** $b;
    }
}
PHP,
        );
    }

    public function testNotEqualsNormalized(): void
    {
        $path = $this->writeTempFixture(
            'NotEquals.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class NotEquals
{
    public function run($a, $b): bool
    {
        return $a <> $b;
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

class NotEquals
{
    public function run($a, $b): bool
    {
        return $a != $b;
    }
}
PHP,
        );
    }

    public function testClosingTagRemoved(): void
    {
        $path = $this->writeTempFixture(
            'ClosingTag.php',
            "<?php\n\ndeclare(strict_types=1);\n\nnamespace Fixture;\n\nclass ClosingTag\n{\n}\n?>\n",
        );

        $this->fixThenAssertContents(
            $path,
            "<?php\n\ndeclare(strict_types=1);\n\nnamespace Fixture;\n\nclass ClosingTag\n{\n}\n",
        );
    }

    public function testPropertyVarDropsNameAndIsOneLine(): void
    {
        $path = $this->writeTempFixture(
            'PropertyVar.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class PropertyVar
{
    /**
     * @var int $bar
     */
    public $bar;
}
PHP,
        );

        $this->fixThenAssertContents(
            $path,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class PropertyVar
{
    /** @var int */
    public $bar;
}
PHP,
        );
    }

    public function testInlineDocMustMatchAssignedVariable(): void
    {
        $path = $this->writeTempFixture(
            'InlineDoc.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Fixture;

class InlineDoc
{
    public function run(): void
    {
        /**
         * @var int $missing
         */
        $present = 1;
    }
}
PHP,
        );

        $check = self::$runner->check([$path]);
        EcsRunner::assertNoPhpDiagnostics($check);
        self::assertNotSame(0, $check->exitCode);
        self::assertStringContainsString('InlineDocCommentDeclarationSniff', $check->stdout . $check->stderr);
    }
}
