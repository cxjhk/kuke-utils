<?php declare(strict_types = 1);
namespace Kuke\WeChat\MiniProgram;

use Swoole\Exception;

/**
 * Class Encryptor
 * @package Kuke\WeChat\MiniProgram
 */
class Encryptor extends ServiceFactory
{
    private $block_size = 16;


    public function decryptData($session, $iv, $encryptedData)
    {
        $this->decrypt($encryptedData, $iv, $session, $data);

        return $data;
    }

    public function decrypt($encryptedData, $iv,$sessionKey, &$data)
    {
        if (strlen($sessionKey) != 24) throw new Exception('session_key 长度不正确',50100);

        $aesKey=base64_decode($sessionKey);

        if (strlen($iv) != 24) throw new Exception('iv 长度不正确',50100);

        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result = $this->decryptCode($aesKey,$aesCipher,$aesIV);

        if ($result[0] != 0) {
            return $result[0];
        }

        $dataObj=json_decode( $result[1] );

        if( $dataObj  == NULL ) throw new Exception('解密失败');

        if( $dataObj->watermark->appid != $this->config['app_id'] ) throw new Exception('appid不正确');
        $data = $result[1];

        return 'ok';
    }

    /**
     * 对密文进行解密
     * @param $aesKey
     * @param $aesCipher
     * @param $aesIV
     * @return array
     * @throws Exception
     */
    public function decryptCode( $aesKey,$aesCipher, $aesIV )
    {
        try {

            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

            mcrypt_generic_init($module, $aesKey, $aesIV);
            //解密
            $decrypted = mdecrypt_generic($module, $aesCipher);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        }catch (Exception $exception) {
            throw new Exception($exception->getMessage(),50100);
        }
        try {
            //去除补位字符
            $result = $this->decode($decrypted);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(),50100);
        }
        return array(0, $result);
    }

    /**
     * 对需要加密的明文进行填充补位
     * @param $text
     * @return string
     */
    function encode( $text )
    {
        $block_size = $this->block_size;
        $text_length = strlen( $text );
        //计算需要填充的位数
        $amount_to_pad = $this->block_size - ( $text_length % $this->block_size );
        if ( $amount_to_pad == 0 ) {
            $amount_to_pad = $this->block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr( $amount_to_pad );
        $tmp = "";
        for ( $index = 0; $index < $amount_to_pad; $index++ ) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param $text
     * @return bool|string
     */
    function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }
}
