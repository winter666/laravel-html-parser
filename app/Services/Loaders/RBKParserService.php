<?php


namespace App\Services\Loaders;


use App\Interfaces\PageParser;
use App\Models\Parser\News;
use App\Models\Parser\NewsDetail;
use App\Services\Parser\ParserItemService;
use App\Services\Parser\ParserService;
use App\Services\Request\RequestService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RBKParserService implements PageParser
{

    private $request;
    private $news;
    private $newsData;

    const SERVICE_KEY = 'rbk';
    const SERVICE_NAME = 'РБК';

    public function __construct()
    {
        $this->request = new RequestService('https://rt.rbc.ru/');
    }


    public function run () {
        $arrayNews = [];

        $response = $this->request->get();
        $parser = new ParserService($response);

        $newsBlockId = 'js_news_feed_banner';
        $newsListBlockClass = 'js-news-feed-list';
        $newsItemClass = 'news-feed__item';
        $newsItemTitleClass = $newsItemClass . '__title';
        $newsItemTimeClass = $newsItemClass . '__date';

        $newsBlock = $parser->elementById($newsBlockId)
            ->getChildren($newsListBlockClass)
            ->first();
        $newsLinkList = $newsBlock->getChildren($newsItemClass)->orderById();

        if ($newsLinkList->count()) {
            $arrayNews = [];
            foreach ($newsLinkList as $newsLink) {
                $childrenCollection = $newsLink->getChildren();
                $spanTime = $childrenCollection->findFirstByClass($newsItemTimeClass);
                $spanTimeTextContent = $this->getTextContentWrap($spanTime);
                $parsedSpanTimeBlock = $this->parseTimeBlock($spanTimeTextContent);

                $arrayNews[] = [
                    'source_link' => $newsLink->getAttrByName('href'),
                    'content' => $this->getTextContentWrap($childrenCollection->findFirstByClass($newsItemTitleClass)),
                    'topic' => (isset($parsedSpanTimeBlock['content'])) ? $parsedSpanTimeBlock['content'] : null,
                    'external_date' => (isset($parsedSpanTimeBlock['date'])) ? $parsedSpanTimeBlock['date'] : null,
                    'external_time' => (isset($parsedSpanTimeBlock['time'])) ? $parsedSpanTimeBlock['time'] : null,
                    'load_service' => self::SERVICE_KEY
                ];
            }
        }
        $this->news = $arrayNews;

        return $arrayNews;
    }


    private function parseTimeBlock(string $text): array {
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


    private function getTextContentWrap(?ParserItemService $parserItemService): ?string {
        if (!empty($parserItemService)) {
            return $parserItemService->getTextContent();
        }
        return null;
    }


    public function saveData(int $limit = 15) {
        $count = 0;
        foreach ($this->news as $key => $item) {
            if ($count < $limit) {
                $count++;
            } else {
                break;
            }

            $validator = Validator::make($item, [
                'source_link' => 'required',
                'content' => 'required|string|max:255',
                'topic' => 'nullable|string',
                'external_date' => 'nullable|string',
                'external_time' => 'string',
                'load_service' => 'string'
            ]);

            if ($validator->fails()) {
                Log::error('Error on Validation content from ' . self::SERVICE_KEY . ': ' . print_r($item, true));
                unset($this->news[$key]);
                continue;
            }

            $this->serializeBeforeSave($item);
            $news = News::updateOrCreate([
                'source_link' => $item['source_link'],
                'content' => $item['content']
            ], $item);

            $this->loadDetailPage($news);
        }
    }

    private function serializeBeforeSave(&$item) {
        $dateTimeStr = '';
        if (!empty($item['external_date'])) {
            $dateTimeStr .= $item['external_date'];
        }
        if (!empty($item['external_time'])) {
            $dateTimeStr .= (strlen($dateTimeStr) > 0) ?  ', ' . $item['external_time'] : $item['external_time'];
        }
        unset($item['external_date']);
        unset($item['external_time']);
        $item['external_datetime'] = $dateTimeStr;
    }

    private function loadDetailPage(News $news) {
        try {
            $requestService = new RequestService($news->source_link, 200);
            $response = $requestService->get();
            $parser = new ParserService($response);
            $contents = $parser->elementsByTagName('p');
            if ($contents->count()) {
                $contentForSave = [];
                foreach ($contents as $content) {
                    $textContent = $this->getTextContentWrap($content);
                    if ($textContent) {
                        $contentForSave[] = trim($textContent);
                    }
                }
                $content = implode(" <br/> ", $contentForSave);

                $attachments = [];
                $imgWrap = $parser->elementsByClassName('article__main-image__wrap')->first();
                // Если есть картинка в новости - сохраняем её
                if ($imgWrap) {
                    $imgTag = $imgWrap->getChildren()->first();
                    $imgHref = $imgTag->getAttrByName('src');
                    $imgRes = (new RequestService($imgHref))->get();
                    $attachments = [
                        [
                            'name' => time() . "_" . $news->load_service,
                            'src' => $imgRes
                        ]
                    ];
                }
                (new NewsDetail())->createData($news, $content, $attachments);
            } else {
                throw new \Exception('Ни одного поля не найдено');
            }
        } catch (\Exception $e) {
            Log::error('Failed to try load or save source for ' . $news->load_service . " link: " . $news->source_link . " " . $e->getMessage());
        }
    }

}
