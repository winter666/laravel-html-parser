<?php


namespace App\Services;


class LoadService
{

    // For run command
    public const ALLOW_LOAD_SERVICES = [
        RBKParserService::SERVICE_KEY => RBKParserService::class
    ];

    // For display on Front
    public const ALLOW_SERVICES_NAMES = [
        RBKParserService::SERVICE_KEY => RBKParserService::SERVICE_NAME
    ];


    public static function getServiceByKey($serviceKey) {
        return (isset(self::ALLOW_LOAD_SERVICES[$serviceKey])) ?
            self::ALLOW_LOAD_SERVICES[$serviceKey] : null;
    }

    public static function getName($serviceKey) {
        return (isset(self::ALLOW_SERVICES_NAMES[$serviceKey])) ?
            self::ALLOW_SERVICES_NAMES[$serviceKey] : null;
    }

}
