<?php
/**
 * 微信支付类 小程序 公众号都可使用
 */
namespace Kuke\WeChat\Payment\Order;
use Kuke\WeChat\Payment\ServiceFactory;

class Client extends ServiceFactory
{

    /**
     * @param array $param = [
                'body' => '腾讯充值中心-QQ会员充值',
                'out_trade_no' => time().rand(0,199645),
                'total_fee' => 0.1,
                'spbill_create_ip' => '127.0.0.1',
                'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'openid' => 'oVz_s4k7tOs7_qz-wiTJd8ODY6Nc',
            ]
     * @return array|mixed|\Psr\Http\Message\ResponseInterface
     */

    public function unify(array $param)
    {

        $uri = '/pay/unifiedorder';

        //$post['appid'] = $this->config['appid'];
        $post['appid'] = $this->config['app_id'];  //服务商ID
//        $post['sub_appid'] = $this->config['sub_appid'];    //小程序ID
//        $post['sub_mch_id'] = $this->config['sub_mch_id'];//子商户
        $post['mch_id'] = $this->config['mch_id'];
        $post['body'] = $param['body'];
        $post['nonce_str'] = $this->createNoncestr();//随机字符串
        $post['notify_url'] = $param['notify_url'];
        $post['openid'] = $param['openid'];
        $post['out_trade_no'] = $param['out_trade_no'];
        $post['spbill_create_ip'] = $param['spbill_create_ip'];//服务器终端的ip
        $post['total_fee'] = $param['total_fee']*100; //总金额 最低为一分钱 必须是整数
        $post['trade_type'] = 'JSAPI';
        $sign = $this->makeSign($post);

        $post['sign']   =   $sign;

        $result = $this->client()->post($uri,[
            'body'  =>  $this->ToXml($post)
        ]);

        $result = $this->FromXml($result->getBody()->getContents());
        if ($result['return_code'] === "SUCCESS" && $result['return_msg'] === 'OK'){
            $return = [
                'appId'     => $this->config['app_id'],
                'timeStamp' => time().'',
                'nonceStr'  => $post['nonce_str'],
                'package'   => 'prepay_id='.$result['prepay_id'],
                'signType'  => 'MD5'
            ];
            //二次验签
            $return['paySign'] = $this->twoSign($return);

            return $return;
        }
        return $result;
    }

    /**
     * 微信退款接口
     * @param string $transactionId
     * @param string $refundNumber
     * @param int $totalFee
     * @param int $refundFee
     * @param array $config
     * @return mixed
     */
    public function refund(string $transactionId, string $refundNumber, int $totalFee, int $refundFee, array $config = [])
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

        $data = array_merge([
            'appid'             =>  $this->config['app_id'],
            'mch_id'            =>  $this->config['mch_id'],
            'nonce_str'         =>  $this->createNoncestr(),
            'out_trade_no'      =>  $transactionId,
            'out_refund_no'     =>  $refundNumber,
            'total_fee'         =>  $totalFee*100,
            'refund_fee'        =>  $refundFee*100
        ],$config);

        $data['sign']   =   $this->makeSign($data);

        $result = $this->request($url,$data,true);

        return $result;

    }

    /**
     * 二次验签
     * @return [type] [description]
     */
    private function twoSign($result)
    {
        return strtoupper(md5('appId='.$result['appId'].'&nonceStr='.$result['nonceStr'].'&package='.$result['package'].'&signType=MD5&timeStamp='.$result['timeStamp'].'&key='.$this->config['key']));
    }

    /**
     * 发起一个post请求
     * @param $url 请求 url
     * @param $array 请求参数
     * @param $setCert 是否是要证书 证书需要绝对路径
     * @return mixed
     */
    private function request($url,$array,$setCert = false)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置header
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        if ($setCert == true) {
            // 设置证书
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'pem');
            curl_setopt($curl, CURLOPT_SSLCERT,  'vendor/wechatPay/cert/apiclient_cert.pem');
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'pem');
            curl_setopt($curl, CURLOPT_SSLKEY, 'vendor/wechatPay/cert/apiclient_key.pem');
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'pem');
            curl_setopt($curl, CURLOPT_CAINFO, 'vendor/wechatPay/cert/rootca.pem');
        }
        //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, TRUE);       //发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->ToXml($array)); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);      // 设置超时限制防止死循环
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); //关闭CURL会话
        return $this->FromXml($tmpInfo);
    }
    /**
     * 生成随机数
     * @param int $len 随机数长度
     * @return string
     */
    private function createNoncestr($length = 32)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

    /**
     * 将 xml 转为数组
     * @param $xml
     * @return mixed
     */
    private function FromXml($xml)
    {
        //将XML转为array
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)),true);
    }

    /**
     * 将 数组 转为 xml
     * @param $arr
     * @return string
     */
    private function ToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 生成支付签名
     * @param $data
     * @return string
     */
    private function makeSign($paramArray,$isencode = false){

        $paramStr = '';

        ksort($paramArray);

        $i = 0;

        foreach ($paramArray as $key => $value)

        {

            if ($key == 'Signature'){

                continue;

            }

            if ($i == 0){

                $paramStr .= '';

            }else{

                $paramStr .= '&';

            }

            $paramStr .= $key . '=' . ($isencode?urlencode($value):$value);

            ++$i;

        }

        $stringSignTemp=$paramStr."&key=".$this->config['key'];

        $sign=strtoupper(md5($stringSignTemp));

        return $sign;
    }
}
