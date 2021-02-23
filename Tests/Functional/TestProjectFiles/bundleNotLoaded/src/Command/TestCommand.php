<?php

declare(strict_types=1);

namespace App\Command;

use Auxmoney\OpentracingBundle\Internal\TracingId;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    private $client;
    private $tracingId;

    public function __construct(HttpClient $client, TracingId $tracingId)
    {
        parent::__construct('test:httplug');
        $this->setDescription('some fancy command description');    
        $this->client = $client;
        $this->tracingId = $tracingId;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new Request('GET', '/');

        $this->client->sendRequest($request)->getBody()->getContents();

        $output->writeln($this->tracingId->getAsString());
        return 0;
    }
}
