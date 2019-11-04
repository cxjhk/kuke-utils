<?php declare(strict_types = 1);
namespace Kuke\WeChat\MiniProgram;

use Swoole\Exception;

/**
 * Class Encryptor
 * @package Kuke\WeChat\MiniProgram
 */
class Encryptor extends ServiceFactory
{

    public function decryptData($session, $iv, $encryptedData)
    {
        $decrypted = AES::decrypt(
            base64_decode($encryptedData, false), base64_decode($session, false), base64_decode($iv, false)
        );

        $decrypted = json_decode($this->pkcs7Unpad($decrypted), true);

        if (!$decrypted) {
            throw new Exception('The given payload is invalid.',50100);
        }

        return $decrypted;
    }

    /**
     * PKCS#7 unpad.
     *
     * @param string $text
     *
     * @return string
     */
    public function pkcs7Unpad(string $text): string
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > $this->blockSize) {
            $pad = 0;
        }

        return substr($text, 0, (strlen($text) - $pad));
    }
}
