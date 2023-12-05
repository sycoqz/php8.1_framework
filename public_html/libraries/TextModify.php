<?php

namespace libraries;

class TextModify
{

    protected array $translitArr = [ 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
    'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
    'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
    'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
    'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => 'y', 'ы' => 'y',
    'ь' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', ' ' => '-',
    ];

    protected array $lowelLetter = ['а', 'е', 'и', 'о', 'у', 'э'];

    public function translit(string $str)
    {

        $str = mb_strtolower($str);
        $tempArr = [];

        for ($i = 0; $i < mb_strlen($str); $i++) {

            $tempArr[] = mb_substr($str, $i, 1);

        }

        $link = '';

        if ($tempArr) {

            foreach ($tempArr as $key => $char) {

                if (array_key_exists($char, $this->translitArr)) {

                    switch ($char) {

                        case 'ъ':
                            if ($tempArr[$key+1] == 'е') $link .= 'y';
                            break;

                        case 'ы':
                            if ($tempArr[$key+1] == 'й') $link .= 'i';
                                else $link .= $this->translitArr[$char];
                            break;

                        case 'ь':
                            if (isset($tempArr[$key+1]) && $tempArr[$key+1] != count($tempArr) && in_array($tempArr[$key+1],$this->lowelLetter)) {

                                $link .= $this->translitArr[$char];

                            }
                            break;

                        default:
                            $link .= $this->translitArr[$char];
                            break;

                    }

                } else {

                    $link .= $char;

                }
            }
        }

        if (!empty($link)) {

            $link = preg_replace('/[^a-z0-9_-]/iu', '', $link);
            $link = preg_replace('/-{2,}/iu', '-', $link);
            $link = preg_replace('/_{2,}/iu', '_', $link);
            $link = preg_replace('/(^[-_]+)|([-_]+$)/iu','', $link);

        }

        return $link;

    }

}