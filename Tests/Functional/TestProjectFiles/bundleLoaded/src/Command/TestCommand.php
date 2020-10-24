<?php

declare(strict_types=1);

namespace App\Command;

use Auxmoney\OpentracingBundle\Internal\Opentracing;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use const OpenTracing\Formats\TEXT_MAP;

class TestCommand extends Command
{
    private $client;
    private $opentracing;

    public function __construct(HttpClient $client, Opentracing $opentracing)
    {
        parent::__construct('test:httplug');
        $this->setDescription('some fancy command description');
        $this->client = $client;
        $this->opentracing = $opentracing;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new Request('GET', '/');

        $this->client->sendRequest($request)->getBody()->getContents();

        $carrier = [];
        $this->opentracing->getTracerInstance()
            ->inject(
                $this->opentracing->getTracerInstance()
                    ->getActiveSpan()
                    ->getContext()
                ,
                TEXT_MAP,
                $carrier
            );

        $output->writeln(current($carrier));

        return 0;
    }
}
