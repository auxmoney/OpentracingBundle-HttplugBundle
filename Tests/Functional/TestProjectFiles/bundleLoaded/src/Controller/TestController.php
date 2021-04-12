<?php

declare(strict_types=1);

namespace App\Controller;

use Http\Client\HttpClient;
use GuzzleHttp\Psr7\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController extends AbstractController
{
    private $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function index(HttpClient $client): JsonResponse
    {
        $request = new Request('GET', 'https://github.com/auxmoney/OpentracingBundle-HttplugBundle');

        $contents = $this->client->sendRequest($request)->getBody()->getContents();

        return new JsonResponse([
            'nested' => true,
            'contentsLength' => strlen($contents),
        ]);
    }
}
