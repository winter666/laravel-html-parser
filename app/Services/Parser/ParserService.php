<?php


namespace App\Services\Parser;


use DOMDocument;
use Illuminate\Support\Facades\Log;

class ParserService
{
    protected $dom;


    public function __construct(string $html = '') {
        if ($html) {
            $this->dom = new DOMDocument();
            libxml_use_internal_errors(true);
            if (!$this->dom->loadHTML($html)) {
                $this->printErrors();
            }
        }
    }

    public function elementById(string $elemId): ParserItemService {
        $this->dom = $this->dom->getElementById($elemId);
        return (new ParserItemService($this->dom));
    }

    public function get() {
        return $this->dom;
    }


    private function printErrors() {
        $errors = "";
        foreach (libxml_get_errors() as $error) {
            $errors .= $error->message . "\n";
        }
        libxml_clear_errors();
        Log::debug("libxml errors:<br>$errors");
    }

}
