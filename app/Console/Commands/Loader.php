<?php

namespace App\Console\Commands;

use App\Interfaces\PageParser;
use App\Services\LoadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Loader extends Command
{

    protected $signature = 'load:news {--service=}';

    protected $description = 'Load Contents';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = $this->option('service');
        if ($service) {
            // Если сервис указан то подгружаем по определенному сервису
            $loadService = LoadService::getServiceByKey($service);
            if ($loadService) {
                // Если этот сервис найден
                $this->printStart($service);
                $this->runOne(new $loadService(), $service);
            } else {
                $this->error('Undefined service ' . $service);
                return 1;
            }
        } else {
            // Если домен не указан - то подгружаем по всему списку
            $loadServices = LoadService::ALLOW_LOAD_SERVICES;

            foreach ($loadServices as $serviceKey => $serviceClass) {
                $this->runOne(new $serviceClass(), $serviceKey);
            }
        }
        return 0;
    }

    private function runOne(PageParser $pageParser, $serviceKey) {
        $this->printStart($serviceKey);
        try {
            $pageParser->run();
            $pageParser->saveData();
            $this->printEnd($serviceKey);
        } catch (\Exception $e) {
            $this->printFailed($serviceKey);
            Log::error('On sync ' . $serviceKey . " " . $e->getMessage());
        }
    }

    private function printStart($service) {
        $this->info('Run for '.$service.' service');
    }

    private function printEnd($service) {
        $this->info('End for '.$service.' service');
    }

    private function printFailed($service) {
        $this->error('Failed for '.$service.' service');
    }
}
