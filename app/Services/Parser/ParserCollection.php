<?php


namespace App\Services\Parser;


use Illuminate\Support\Collection;

class ParserCollection extends Collection
{
    public function getItemByClass($className): ?ParserItemService {
        foreach($this->items as $item) {
            if (ParserItemService::hasClass($className, $item->get())) {
                return $item;
            }
        }
        return null;
    }
}
