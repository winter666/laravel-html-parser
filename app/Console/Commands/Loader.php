<?php

namespace App\Console\Commands;

use App\Interfaces\PageParser;
use App\Services\LoadService;
use Illuminate\Console\Command;

class Loader extends Command
{

    protected $signature = 'load:news {--domain=}';

    protected $description = 'Load Contents';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $domain = $this->option('domain');
        if ($domain) {
            // Если домен указан то подгружаем по определенному сервису
            $loadService = LoadService::getServiceByKey($domain);
            if ($loadService) {
                // Если этот сервис найден
                $this->printStart($domain);
                $this->runOne(new $loadService());
            } else {
                $this->error('Undefined service ' . $domain);
                return 1;
            }
        } else {
            // Если домен не указан - то подгружаем по всему списку
            $loadServices = LoadService::ALLOW_LOAD_SERVICES;

            foreach ($loadServices as $domain => $service) {
                $this->printStart($domain);
                $this->runOne(new $service());
            }
        }
        return 0;
    }

    private function runOne(PageParser $pageParser) {
        $pageParser->run();
        $pageParser->saveData();
    }

    private function printStart($domain) {
        $this->alert('Run for '.$domain.' service');
    }
}
