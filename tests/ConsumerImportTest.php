<?php

declare(strict_types=1);

namespace Tests\CodingStandard;

use Tests\CodingStandard\Support\EcsTestCase;

final class ConsumerImportTest extends EcsTestCase
{
    public function testImportedConfigAppliesArraySyntaxAndSpecSkip(): void
    {
        $consumerConfig = self::$runner->repoRoot() . '/tests/Support/consumer-ecs.php';

        $specPath = $this->writeTempFixture(
            'ImportedSpec.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Imported;

class ImportedSpec
{
    function run(): void
    {
        $items = array(1);
    }
}
PHP,
        );

        $this->fixThenAssertContents(
            $specPath,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Imported;

class ImportedSpec
{
    function run(): void
    {
        $items = [1];
    }
}
PHP,
            $consumerConfig,
        );

        self::assertStringNotContainsString('public function run', file_get_contents($specPath));
    }
}
