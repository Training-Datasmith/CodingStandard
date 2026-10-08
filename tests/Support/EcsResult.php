<?php

declare(strict_types=1);

namespace Tests\CodingStandard\Support;

final class EcsResult
{
    public int $exitCode;

    public string $stdout;

    public string $stderr;

    public function __construct(int $exitCode, string $stdout, string $stderr)
    {
        $this->exitCode = $exitCode;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }
}
