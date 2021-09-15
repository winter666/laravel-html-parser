<?php


namespace App\Services;


use App\Services\Parser\ParserCollection;
use App\Services\Parser\ParserItemService;
use App\Services\Parser\ParserService;
use App\Services\Request\RequestService;
use Illuminate\Support\Facades\Log;

class RBKParserService
{

    private $request;

    public function __construct()
    {
        $this->request = new RequestService('https://rt.rbc.ru/');
    }


    public function getBase() {
        $arrayNews = [];

        $response = $this->request->getPage();
        $parser = new ParserService($response);

        $newsBlockId = 'js_news_feed_banner';
        $newsListBlockClass = 'js-news-feed-list';
        $newsItemClass = 'news-feed__item';
        $newsItemTitleClass = $newsItemClass . '__title';
        $newsItemTimeClass = $newsItemClass . '__date';

        $newsBlock = $parser->elementById($newsBlockId)
            ->elementsByTagName('div')
            ->filterByClass($newsListBlockClass);

        $newsLinkList = $newsBlock->first()->getChildren($newsItemClass);
        if ($newsLinkList->count()) {
            $arrayNews = [];
            foreach ($newsLinkList as $newsLink) {
                $childrenCollection = $newsLink->getChildren();
                $spanTime = $childrenCollection->getItemByClass($newsItemTimeClass);
                $spanTimeTextContent = $this->getTextContentWrap($spanTime);
                $parsedSpanTimeBlock = $this->parseTimeBlock($spanTimeTextContent);

                $arrayNews[] = [
                    'source_link' => $newsLink->getAttrByName('href'),
                    'content' => $this->getTextContentWrap($childrenCollection->getItemByClass($newsItemTitleClass)),
                    'origin_name' => (isset($parsedSpanTimeBlock['content'])) ? $parsedSpanTimeBlock['content'] : null,
                    'external_date' => (isset($parsedSpanTimeBlock['date'])) ? $parsedSpanTimeBlock['date'] : null,
                    'external_time' => (isset($parsedSpanTimeBlock['time'])) ? $parsedSpanTimeBlock['time'] : null
                ];
            }
        }

        return $arrayNews;
    }


    public function parseTimeBlock(string $text): array {
        $arrValues = explode(',', $text);
        $arrRes = [];
        foreach ($arrValues as $explodedStr) {
            $content = trim($explodedStr);
            if (strlen($content)) {
                if (preg_match('/\d{2}:\d{2}/', $content)) {
                    $arrRes['time'] = $content;
                } elseif (preg_match('/\d{1,2}\s\W{1,5}/', $content)) {
                    $arrRes['date'] = $content;
                } else {
                    $arrRes['content'] = $content;
                }
            }
        }

        return $arrRes;
    }


    public function getTextContentWrap(?ParserItemService $parserItemService): ?string {
        if (!empty($parserItemService)) {
            return $parserItemService->getTextContent();
        }
        return null;
    }

}
