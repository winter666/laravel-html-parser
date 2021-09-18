<?php

namespace App\Services\Request;

use App\Traits\JsonSerializerTrait;
use GuzzleHttp\Client;

class RequestService
{
    use JsonSerializerTrait;

    protected $page_url;

    private $client;
    private $ms_delay;

    public function __construct(string $page_url, int $ms_delay = 0)
    {
        $this->page_url = $page_url;
        $this->client = new Client();
        $this->ms_delay = $ms_delay;
    }


    public function get(array $options = []) {
        usleep($this->ms_delay);
        return $this->client->get($this->page_url, $options)->getBody();
    }

}
