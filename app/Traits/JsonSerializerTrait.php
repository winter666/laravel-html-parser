<?php


namespace App\Traits;


trait JsonSerializerTrait
{

    public function jsonParse($json)
    {
        return json_decode($json);
    }

}
