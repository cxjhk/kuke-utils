<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/28
 * Time: 18:10
 */

namespace Kuke\WeChat\Payment\Order;


use Hyperf\Utils\Arr;
use Kuke\Utils\ArrayHelper;
use Kuke\WeChat\Payment\ServiceFactory;

class Client extends ServiceFactory
{
    public function unify(array $body)
    {
        $uri = '/pay/unifiedorder';

        $nonce_str = $this->createNoncestr();

        $parameters = array(
            'appid' => $this->config['app_id'], //小程序ID
            'mch_id' => $this->config['mch_id'], //商户号
            'nonce_str' => $nonce_str, //随机字符串
            'body' => $body['body'],//商品描述
            'out_trade_no'=> $body['out_trade_no'], //商户订单号
            'total_fee' => $body['total_fee'],
            'spbill_create_ip' => '127.0.0.1', //终端IP
            'notify_url' => $body['notify_url'], //通知地址  确保外网能正常访问
            'openid' => $body['openid'], //用户id
            'trade_type' => 'JSAPI'//交易类型
        );

        $parameters['sign'] = $this->sign($parameters);

        $response = $this->client()->post($uri,[
           'body'    =>  $this->toXml($parameters)
        ]);

        $result = $this->xmlToArray($response->getBody()->getContents());

        if ($result['return_code'] === 'SUCCESS' && $result['return_msg']['OK'])
        {
            $params = [
                'appid'     => $this->config["app_id"],
                'partnerid' => $this->config['mch_id'],
                'prepayid'  => 'prepay_id='.$result['prepay_id'],
                'package'   => 'Sign=JSAPI',
                'noncestr'  => $nonce_str,
                'timestamp' => time(),
            ];
            $params['sign'] = $this->sign($params);

            return $params;
        }

        return $result;
    }

    /**
     * @param $data
     * @return string
     */
    private function sign($data)
    {
        foreach ($data as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
        $String = $String . "&key=" . $this->config['key'];
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
    }

    ///作用：格式化参数，签名过程需要使用
    private function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }

        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * 随机字符串
     * @return string
     */
    private function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 数组转换成xml
     * @param $arr
     * @return string
     */
    private function toXml($arr) {
        $xml = "<root>";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . $this->arrayToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</root>";
        return $xml;
    }


    /**
     * xml转换成数组
     * @param $xml
     * @return mixed
     */
    private function xmlToArray($xml) {


        //禁止引用外部xml实体


        libxml_disable_entity_loader(true);


        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);


        $val = json_decode(json_encode($xmlstring), true);


        return $val;
    }
}
