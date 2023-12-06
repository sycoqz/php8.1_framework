<?php

namespace core\admin\controllers;

use core\base\controllers\BaseMethods;

class CreatesitemapController extends BaseAdmin
{

    use BaseMethods;

    protected array $linkArr = [];

    protected string $parsingLogFile = 'parsing_log.txt';

    protected array $fileArrExtensions = ['jpg', 'png', 'jpeg', 'gif', 'xls', 'xlsx', 'pdf', 'mp4', 'mp3', 'mpeg'];

    protected array $filterArr = [
        'url' => ['order'],
        'get' => [],
    ];

    protected function inputData(): void
    {

        if (!function_exists('curl_init')) {

            $this->writeLog('Отсутствует библиотека CURL');
            $_SESSION['result']['answer'] =
                '<div class="error">The CURL library is missing. The sitemap creation was rejected.</div>';
            $this->redirect();

        }

        set_time_limit(0);

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . PATH . 'log/' . $this->parsingLogFile))
            @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . 'log/' . $this->parsingLogFile);

        $this->parsing(SITE_URL);

        $this->createSitemap();

        !isset($_SESSION['result']['answer']) && $_SESSION['result']['answer'] =
            '<div class="success">Sitemap is create</div>';

    }

    protected function parsing(string $url, int $index = 0): void
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_RANGE, 0 - 4194304);

        // Используйте 1 для проверки существования общего имени в сертификате SSL.
        // Используйте 2 для проверки существования общего имени и также его совпадения с указанным хостом.
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);

        // FALSE для остановки cURL от проверки сертификата узла сети.
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, true);

        $out = curl_exec($curl);

        curl_close($curl);

//      /u - поиск по кодировкам (utf-8) для русских букв и тд.
//      /i - делает поиск по выражению регистронезависимым. Нет разницы между заглавным и прописным символом
//      /U - модификатор указывает на то, что результатом поиска должен быть самый короткий отрывок,
//      удовлетворяющий маске поиска. Рекомендую всегда использовать данный модификатор
//      /m - этот модификатор позволяет искать отрезок текста только внутри одной строки
//      /s - поиск идёт всему тексту, не обращая внимания на переносы строк
//      /x - игнорируются пробельные символы, в том числе символы табуляции и перевода строки
        if (!preg_match('/Content-Type:\s+text\/html/ui', $out)) {

            unset($this->linkArr[$index]);

            $this->linkArr = array_values($this->linkArr);

            return;

        }

//        \d - цифра.
//        \s - пробел.
//        * - 0 и более раз
//        ? - неизвестно символ есть или нет.
        if (!preg_match('/HTTP\/\d\.?\d?\s+20\d/ui', $out)) {

            $this->writeLog('Некорректная ссылка при парсинге - ' . $url, $this->parsingLogFile);

            unset($this->linkArr[$index]);

            $this->linkArr = array_values($this->linkArr);

            $_SESSION['result']['answer'] =
                '<div class="error">Incorrect URL in the scraping - ' . $url . '<br>Sitemap is created</div>';

            return;

        }

//        [^>]*? - любые символы кроме знака больше.
//        (.+?) - любые символы 1 или более раз.
//        $ - конец строки.
        preg_match_all('/<a\s*?[^>]*?href\s*?=(["\'])(.+?)\1[^>]*?>/uis', $out, $links);

        if (isset($links[2])) {

            foreach ($links[2] as $link) {

                if ($link === '/' || $link === SITE_URL . '/') continue;

                foreach ($this->fileArrExtensions as $extension) {

                    if (isset($extension)) {

                        $extension = addslashes($extension);
                        $extension = str_replace('.', '\.', $extension);

                        if (preg_match('/' . $extension . '\s*?$|\?[^/]/uis', $link)) {

                            continue 2;

                        }
                    }
                }

                if (str_starts_with($link, '/')) {

                    $link = SITE_URL . $link;

                }

                if (!in_array($link, $this->linkArr) && $link !== '#' && str_starts_with($link, SITE_URL)) {

                    if ($this->filter($link)) {

                        $this->linkArr[] = $link;
                        $this->parsing($link, count($this->linkArr) - 1);

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

                            if (preg_match('/^[^?]*' . $item . '/ui', $link)) return false;

                        }

                        if ($type === 'get') {

                            if (preg_match('/(\?|&amp;|=|&)'. $item . '(=|&amp;|&|$)/ui', $link, $matches))
                                return false;

                        }
                    }
                }
            }
        }

        return true;

    }

    protected function createSitemap()
    {



    }

}