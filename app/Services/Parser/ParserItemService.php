<?php


namespace App\Services\Parser;



use Carbon\Carbon;

/**
 * @propperty $id
 * @propperty $dom
 **/
class ParserItemService extends ParserService
{

    public $id;
    protected $dom;

    public function __construct($dom)
    {
        parent::__construct();
        $this->dom = $dom;
        $this->id = time();
        sleep(1);
    }

    public function get() {
        return $this->dom;
    }

    public function getChildren(string $childrenClass = ''): ParserCollection {
        $nodeCollection = new ParserCollection();
        foreach ($this->dom->childNodes as $childNode) {
            if ($childNode instanceof \DOMElement) {
                if (!empty($childrenClass)) {
                    if (static::hasClass($childrenClass, $childNode)) {
                        $nodeCollection->push(new static($childNode));
                    }
                } else {
                    $nodeCollection->push(new static($childNode));
                }
            }
        }
        return $nodeCollection;
    }

    public function getAttrByName(string $attrName) {
        return $this->dom->getAttribute($attrName);
    }

    public static function hasClass(string $className, \DOMElement $domElement) {
        $classList = explode(' ', $domElement->getAttribute('class'));
        return in_array($className, $classList);
    }

    public function getTextContent () {
        return trim($this->dom->textContent);
    }

}
