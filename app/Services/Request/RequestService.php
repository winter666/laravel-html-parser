<?php

namespace App\Services\Request;

use App\Traits\JsonSerializerTrait;
use GuzzleHttp\Client;

class RequestService
{
    use JsonSerializerTrait;

    protected $page_url;

    private $client;

    public function __construct(string $page_url)
    {
        $this->page_url = $page_url;
        $this->client = new Client();
    }


    public function getPage() {
        return $this->client->get($this->page_url)->getBody();
    }

}
