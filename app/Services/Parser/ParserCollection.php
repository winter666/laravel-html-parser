<?php


namespace App\Services\Parser;


use Illuminate\Support\Collection;

class ParserCollection extends Collection
{
    public function findFirstByClass($className): ?ParserItemService {
        foreach($this->items as $item) {
            if (ParserItemService::hasClass($className, $item->get())) {
                return $item;
            }
        }
        return null;
    }

    public function parseCollection(?\DOMNodeList $list): ParserCollection {
        foreach ($list as $item) {
            if ($item instanceof \DOMElement) {
                $this->push(new ParserItemService($item));
            }
        }
        return $this;
    }

    public function orderById() {
        $sorted = $this->sortBy([['id', 'desc']]);
        return $sorted->values();
    }

}
