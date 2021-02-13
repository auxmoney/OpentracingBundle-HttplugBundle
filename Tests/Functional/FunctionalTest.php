<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Functional;

use Auxmoney\OpentracingBundle\Tests\Functional\JaegerWebFunctionalTest;
use Symfony\Component\Process\Process;

class FunctionalTest extends JaegerWebFunctionalTest
{
    /**
     * @dataProvider provideProjectSetups
     */
    public function testNestedSpansAndHeaderPropagation(string $projectSetup): void
    {
        $this->setUpTestProject($projectSetup);

        $p = new Process(['symfony', 'console', 'test:httplug'], self::BUILD_TESTPROJECT);
        $p->mustRun();
        $output = $p->getOutput();
        $traceId = substr($output, 0, strpos($output, ':'));
        self::assertNotEmpty($traceId);

        $spans = $this->getSpansFromTrace($this->getTraceFromJaegerAPI($traceId));
        $traceAsYAML = $this->getSpansAsYAML($spans, '[].{operationName: operationName, startTime: startTime, spanID: spanID, references: references, tags: tags[?key==\'http.status_code\' || key==\'command.exit-code\' || key==\'http.url\' || key==\'http.method\' || key==\'auxmoney-opentracing-bundle.span-origin\'].{key: key, value: value}}');
        file_put_contents(sprintf(__DIR__ . '/ExpectedSpans/%s.actual.yaml', $projectSetup), $traceAsYAML);

        self::assertStringEqualsFile(sprintf(__DIR__ . '/ExpectedSpans/%s.yaml', $projectSetup), $traceAsYAML);
    }

    public function provideProjectSetups(): array
    {
        return [
            'Symfony bundle not loaded' => ['bundleNotLoaded'],
            'Symfony bundle loaded' => ['bundleLoaded'],
        ];
    }
}
