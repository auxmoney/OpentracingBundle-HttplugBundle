<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Functional;

use Auxmoney\OpentracingBundle\Tests\Functional\JaegerConsoleFunctionalTest;
use Symfony\Component\Process\Process;

class NoHandlerStackTest extends JaegerConsoleFunctionalTest
{
    public function testExceptionWithoutHandlerStack(): void
    {
        $this->copyTestProjectFiles('bundleLoaded');

        $this->composerDumpAutoload();

        $process = new Process(['symfony', 'console', 'cache:clear'], self::BUILD_TESTPROJECT);
        $returnCode = $process->run();

        self::assertSame(0, $returnCode);
    }
}
