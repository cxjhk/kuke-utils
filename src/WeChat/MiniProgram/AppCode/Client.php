<?php declare(strict_types = 1);

namespace Kuke\WeChat\MiniProgram\AppCode;


use Kuke\Utils\ArrayHelper;
use Kuke\WeChat\MiniProgram\ServiceFactory;

class Client extends ServiceFactory
{
    private $img;

    /**
     *  适用于需要的码数量较少的业务场景
     * @param string $path
     * @param array $optional
     * @return string
     */
    public function get(string $path, array $optional = [])
    {
        $uri = '/wxa/getwxacode?access_token='.$this->token['access_token'];

        $params = array_merge([
            'path' => $path,
        ], $optional);

        $response = $this->client()->post($uri,[
            'json'   =>  $params
        ]);

        return $this->save($response->getBody()->getContents(),'image/png');
    }

    /**
     * 获取小程序码，适用于需要的码数量极多的业务场景。通过该接口生成的小程序码，永久有效，数量暂无限制
     * @param string $scene
     * @param array $optional
     * @return string
     */
    public function getUnlimit(string $scene, array $optional = [])
    {
        $uri = '/wxa/getwxacodeunlimit?access_token='.$this->token['access_token'];

        $params = array_merge([
            'scene' => $scene,
        ], $optional);

        $response = $this->client()->post($uri,[
            'json'   =>  $params
        ]);

        return $this->save($response->getBody()->getContents(),'image/png');
    }

    /**
     * 获取小程序二维码，适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制
     * @param string $path
     * @param int|null $width
     * @return string
     */
    public function getQrCode(string $path, int $width = null)
    {
        $uri = '/cgi-bin/wxaapp/createwxaqrcode?access_token='.$this->token['access_token'];

        $response = $this->client()->post($uri,[
            'json'   =>  [
                'path'  => $path,
                'width' => $width ?? 430
            ]
        ]);

        return $this->save($response->getBody()->getContents(),'image/png');
    }
    /**
     * create base64
     * @param $contents
     * @param $mime
     * @return string
     */
    private function save($contents, $mime)
    {
        $base64   = base64_encode($contents);
        return ('data:' . $mime . ';base64,' . $base64);
    }

}
