<?php

namespace core\base\models;

use core\base\controllers\Singleton;

class Crypt
{

    use Singleton;

    private string $cryptMethod = 'AES-128-CBC';

    private string $hashAlgorithm = 'sha256';
    private int $hashLength = 32;

    public function encrypt(string $str): string
    {

        $ivLen = openssl_cipher_iv_length($this->cryptMethod);

        $iv = openssl_random_pseudo_bytes($ivLen);

        $cipherText = openssl_encrypt($str, $this->cryptMethod, CRYPT_KEY, OPENSSL_RAW_DATA, $iv);

        $hmac = hash_hmac($this->hashAlgorithm, $cipherText, CRYPT_KEY, true);

        return $this->encryptCombination($cipherText, $iv, $hmac);

    }

    public function decrypt(string $str): bool|string
    {

        $ivLen = openssl_cipher_iv_length($this->cryptMethod);

        $cryptData = $this->decryptCombination($str, $ivLen);

        $originalText = openssl_decrypt($cryptData['str'], $this->cryptMethod, CRYPT_KEY, OPENSSL_RAW_DATA, $cryptData['iv']);

        $calmac = hash_hmac($this->hashAlgorithm, $cryptData['str'],CRYPT_KEY,true);

        if (hash_equals($cryptData['hmac'], $calmac)) return $originalText;

        return false;

    }

    protected function encryptCombination(string $str, string $iv, string $hmac): string
    {

        $new_str = '';

        $sL = strlen($str);

        $counter = (int)ceil(strlen(CRYPT_KEY)/ ($sL + $this->hashLength));

        $progress = 1;

        if ($counter >= $sL) $counter = 1;

        for ($i = 0; $i < $sL; $i++) {

            if ($counter < $sL) {

                if ($counter === $i) {
                    // Нельзя использовать мультибайтовые функции, так как работа ведётся с байтовой длиной строки.
                    $new_str .= substr($iv,$progress - 1, 1);
                    $progress++;
                    $counter += $progress;

                }

            } else {

                break;

            }

            $new_str .= substr($str, $i, 1);

        }

        $new_str .= substr($str, $i);
        $new_str .= substr($iv, $progress - 1);

        $new_str_half = (int)ceil(strlen($new_str) / 2);

        $new_str = substr($new_str, 0, $new_str_half) . $hmac . substr($new_str, $new_str_half);

        return base64_encode($new_str);

    }

    protected function decryptCombination(string $str, int $ivLen): array
    {

        $cryptData = [];

        $str = base64_decode($str);

        // Получение позиции hash'a в строке.
        $hashPosistion = (int)ceil(strlen($str) / 2 - $this->hashLength / 2);

        $cryptData['hmac'] = substr($str, $hashPosistion, $this->hashLength);

        $str = str_replace($cryptData['hmac'], '', $str);

        $counter = (int)ceil(strlen(CRYPT_KEY) / (strlen($str) - $ivLen + $this->hashLength));

        if ($counter >= strlen($str) - $ivLen) $counter = 1;

        $progress = 2;

        $cryptData['str'] = '';
        $cryptData['iv'] = '';

        for ($i = 0; $i < strlen($str); $i++) {

            if ($ivLen + strlen($cryptData['str']) < strlen($str)) {

                if ($i === $counter) {

                    $cryptData['iv'] .= substr($str, $counter, 1);
                    $progress++;
                    $counter += $progress;

                } else {

                    $cryptData['str'] .= substr($str, $i, 1);

                }

            } else {

                $cryptDataLen = strlen($cryptData['str']);

                $cryptData['str'] .= substr($str, $i, strlen($str) - $ivLen - $cryptDataLen);

                $cryptData['iv'] .= substr($str,$i + (strlen($str) - $ivLen - $cryptDataLen));

                break;

            }

        }

        return $cryptData;

    }

}