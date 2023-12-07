<?php

namespace core\admin\controllers;

use core\base\controllers\BaseMethods;
use core\base\exceptions\DbException;
use DateTime;
use domDocument;
use DOMException;
use JetBrains\PhpStorm\NoReturn;
use function libraries\mb_str_replace;

class CreatesitemapController extends BaseAdmin
{

    use BaseMethods;

    protected array $all_links = [];

    protected array $temp_links = [];

    protected int $maxLinks = 5000;

    protected string $parsingLogFile = 'parsing_log.txt';

    protected array $fileArrExtensions = ['jpg', 'png', 'jpeg', 'gif', 'xls', 'xlsx', 'pdf', 'mp4', 'mp3', 'mpeg'];

    protected array $filterArr = [
        'url' => [],
        'get' => [],
    ];

    /**
     * @throws DOMException
     * @throws DbException
     */
    #[NoReturn] protected function inputData($linkCounter = 1): void
    {

        if (!function_exists('curl_init')) {

            $this->cancel('The CURL library is missing. The sitemap creation was rejected.',
                '', 0, true);

        }

        if (!isset($this->userID)) $this->executeBase();

        if (!$this->checkParsingTable()) {

            $this->cancel('Some trouble with DB Table {parsing_data}', '', 0, true);

        }

        set_time_limit(0);

        $reserve = $this->model->read('parsing_data')[0] ?? NULL;

        if (isset($reserve)) {

            foreach ($reserve as $name => $item) {

                if ($item) $this->$name = json_decode($item);
                else $this->$name = [SITE_URL];

            }

        }

        $this->maxLinks = (int)$linkCounter > 1 ? ceil($this->maxLinks / $linkCounter) : $this->maxLinks;

        while ($this->temp_links) {

            $temp_linksCounter = count($this->temp_links);

            $links = $this->temp_links;

            $this->temp_links = [];

            if ($temp_linksCounter > $this->maxLinks) {

                $links = array_chunk($links, ceil($temp_linksCounter / $this->maxLinks));

                $countChunks = count($links);

                for ($i = 0; $i < $countChunks; $i++) {

                    $this->parsing($links[$i]);

                    unset($links[$i]);

                    if ($links) {

                        $this->model->update('parsing_data', [
                            'fields' => [
                                'temp_links' => json_encode(array_merge(...$links)),
                                'all_links' => json_encode($this->all_links)
                            ]
                        ]);

                    }

                }

            } else {

                $this->parsing($links);

            }

            $this->model->update('parsing_data', [
                'fields' => [
                    'temp_links' => json_encode($this->temp_links),
                    'all_links' => json_encode($this->all_links)
                ]
            ]);

        }

        $this->model->update('parsing_data', [
            'fields' => [
                'temp_links' => '',
                'all_links' => ''
            ]
        ]);
        
        if ($this->all_links) {

            foreach ($this->all_links as $key => $link) {

                if (!$this->filter($link)) unset($this->all_links[$key]);

            }

        }

        $this->createSitemap();

        !isset($_SESSION['result']['answer']) && $_SESSION['result']['answer'] =
            '<div class="success">Sitemap is created</div>';

        $this->redirect();

    }

    protected function parsing(array $urls): void
    {

        if (!$urls) return;

        $curlMulti = curl_multi_init();

        $curl = [];

        foreach ($urls as $i => $url) {

            $curl[$i] = curl_init();

            curl_setopt($curl[$i], CURLOPT_URL, $urls);
            curl_setopt($curl[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl[$i], CURLOPT_HEADER, true);
            curl_setopt($curl[$i], CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl[$i], CURLOPT_TIMEOUT, 120);
            curl_setopt($curl[$i], CURLOPT_ENCODING, 'gzip,deflate');

            // Используйте 1 для проверки существования общего имени в сертификате SSL.
            // Используйте 2 для проверки существования общего имени и также его совпадения с указанным хостом.
            curl_setopt($curl[$i],CURLOPT_SSL_VERIFYHOST, 2);

            // FALSE для остановки cURL от проверки сертификата узла сети.
            curl_setopt($curl[$i],CURLOPT_SSL_VERIFYPEER, true);

            curl_multi_add_handle($curlMulti, $curl[$i]);

        }

        do {

            $status = curl_multi_exec($curlMulti, $active);
            $info = curl_multi_info_read($curlMulti);

            if (false !== $info) {

                if ($info['result'] !== 0) {

                    $i = array_search($info['handle'], $curl);

                    $error = curl_errno($curl[$i]);
                    $message = curl_error($curl[$i]);
                    $header = curl_getinfo($curl[$i]);

                    if ($error != 0) {

                        $this->cancel('Error' . $header['url'] . ' http code: ' .
                            $header['http_code'] . ' error: ' . 'message' . $message,
                            '' );

                    }
                }
            }

            if ($status > 0) {

                $this->cancel(curl_multi_strerror($status), '' );

            }

        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

        $result = [];

        foreach ($urls as $i => $url) {

            $result[$i] = curl_multi_getcontent($curl[$i]);
            curl_multi_remove_handle($curlMulti, $curl[$i]);
            curl_close($curl[$i]);

//      /u - поиск по кодировкам (utf-8) для русских букв и тд.
//      /i - делает поиск по выражению регистронезависимым. Нет разницы между заглавным и прописным символом
//      /U - модификатор указывает на то, что результатом поиска должен быть самый короткий отрывок,
//      удовлетворяющий маске поиска. Рекомендую всегда использовать данный модификатор
//      /m - этот модификатор позволяет искать отрезок текста только внутри одной строки
//      /s - поиск идёт всему тексту, не обращая внимания на переносы строк
//      /x - игнорируются пробельные символы, в том числе символы табуляции и перевода строки
            if (!preg_match('/Content-Type:\s+text\/html/ui', $result[$i])) {

                $this->cancel('Incorrect content type', '');

                continue;

            }

//        \d - цифра.
//        \s - пробел.
//        * - 0 и более раз
//        ? - неизвестно символ есть или нет.
            if (!preg_match('/HTTP\/\d\.?\d?\s+20\d/ui', $result[$i])) {

                $this->cancel('Incorrect server code','');

                continue;

            }

            $this->createLinks($result[$i]);

        }

        curl_multi_close($curlMulti);

    }

    protected function createLinks(string $content): void
    {

        if ($content) {

//        [^>]*? - любые символы кроме знака больше.
//        (.+?) - любые символы 1 или более раз.
//        $ - конец строки.
            preg_match_all('/<a\s*?[^>]*?href\s*?=(["\'])(.+?)\1[^>]*?>/uis', $content, $links);

            if (isset($links[2])) {

                foreach ($links[2] as $link) {

                    if ($link === '/' || $link === SITE_URL . '/') continue;

                    foreach ($this->fileArrExtensions as $extension) {

                        if (isset($extension)) {

                            $extension = addslashes($extension);
                            $extension = str_replace('.', '\.', $extension);

                            if (preg_match('/' . $extension . '(\s*?$|\?[^\/]*$)/uis', $link)) {

                                continue 2;

                            }
                        }
                    }

                    if (str_starts_with($link, '/')) {

                        $link = SITE_URL . $link;

                    }

                    $siteUrl = mb_str_replace('.','\.',
                        mb_str_replace('/','\/', SITE_URL));

                    if (!in_array($link, $this->all_links) &&
                        !preg_match('/^(' . $siteUrl . ')?\/?#[^\/]*?$/ui',$link) &&
                        str_starts_with($link, SITE_URL)) {

                        $this->temp_links[] = $link;
                        $this->all_links[] = $link;

                    }
                }
            }
        }



    }

    protected function filter(string $link): bool
    {

        if (isset($this->filterArr)) {

            foreach ($this->filterArr as $type => $values) {

                if (isset($values)) {

                    foreach ($values as $item) {

                        $item = str_replace('/', '\/', addslashes($item));

                        if ($type === 'url') {

                            if (preg_match('/^[^\?]*' . $item . '/ui', $link)) return false;

                        }

                        if ($type === 'get') {

                            if (preg_match('/(\?|&amp;|=|&)'. $item . '(=|&amp;|&|$)/ui', $link))
                                return false;

                        }
                    }
                }
            }
        }

        return true;

    }

    protected function checkParsingTable(): bool
    {

        $tables = $this->model->showTables();

        if (!in_array('parsing_data', $tables)) {

            $query = 'CREATE TABLE parsing_data (all_links text, temp_links text)';

            if (!$this->model->query($query, 'c') ||
                !$this->model->create('parsing_data', ['fields' => ['all_links' => '', 'temp_links' => '']])) {

                return false;

            }
        }

        return true;

    }

    protected function cancel(string $message, string $log_message, int $success = 0, $exit = false): void
    {

        $exitArr = [];
        $exitArr['success'] = $success;
        $exitArr['message'] = $message ?: 'ERROR SCRAPING';
        $log_message = $log_message ?: $exitArr['message'];

        $class = 'success';

        if (!$exitArr['success']) {

            $class = 'error';

            $this->writeLog($log_message, 'parsing_log.txt');

        }

        if ($exit) {

            $exitArr['message'] = '<div class="' . $class . '">' . $exitArr['message'] . '</div>';
            exit(json_encode($exitArr));

        }

    }

    /**
     * @throws DOMException
     */
    protected function createSitemap(): void
    {

        $dom = new domDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('urlset');
        $root->setAttribute('xmlns', 'https://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'https://w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'https://www.sitemaps.org/schemas/sitemap/0.9 
        https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        $dom->appendChild($root);

        $sxe = simplexml_import_dom($dom);

        if ($this->all_links) {

            $date = new DateTime();
            $lastMod = $date->format('Y-m-d') . 'T' . $date->format('H:i:s+01:00');

            foreach ($this->all_links as $item) {

                $element = trim(mb_substr($item, mb_strlen(SITE_URL), '/'));
                $element = explode('/', $element);

                $count = '0.' . (count($element) - 1);

                $priority = 1 - (float)$count;

                if ($priority == 1) $priority = '1.0';

                $urlMain = $sxe->addChild('url');

                $urlMain->addChild('loc', htmlspecialchars($item));
                $urlMain->addChild('lastmod', $lastMod);
                $urlMain->addChild('changefreq', 'weekly');
                $urlMain->addChild('priority', $priority);

            }

        }

        $dom->save($_SERVER['DOCUMENT_ROOT'] . PATH . 'sitemap.xml');

    }

}