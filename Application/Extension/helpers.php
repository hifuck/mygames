<?php

use App\Exceptions\WxPay\WxComPay;

/**
 * 获取当前登录的用户
 */
if (!function_exists('currentUser')) {
    function currentUser()
    {
        return request()->has('game_user') ? request('game_user') : null;
    }
}

/**
 * 简单的hppt get请求
 *
 * @param       $baseURL
 * @param array $keysArr
 *
 * @return bool|mixed|string
 */
function http_gets($baseURL, $keysArr = array())
{
    $url = combineURL($baseURL, $keysArr);
    $ch = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        if (!extension_loaded("openssl")) {
            return ('请开启您PHP环境的openssl');
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($ch);
    $aStatus = curl_getinfo($ch);
    curl_close($ch);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

/**
 *简单的http post 请求
 *
 * @param       $url
 * @param array $keysArr
 *
 * @return bool|mixed|string
 */
function http_post($url, $keysArr = array())
{
    $ch = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        if (!extension_loaded("openssl")) {
            return ('请开启您PHP环境的openssl');
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
    }

    $aPOST = array();
    foreach ($keysArr as $key => $val) {
        $aPOST[] = $key . "=" . urlencode($val);
    }
    $strPOST = implode("&", $aPOST);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $strPOST);
    $sContent = curl_exec($ch);
    $aStatus = curl_getinfo($ch);
    curl_close($ch);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

/**
 * url拼接
 *
 * @param $baseURL
 * @param $keysArr
 *
 * @return string|unknown
 */
function combineURL($baseURL, $keysArr)
{
    if (empty($keysArr) || !is_array($keysArr))
        return $baseURL;

    $combined = $baseURL . "?";
    $valueArr = array();
    foreach ($keysArr as $key => $val) {
        $valueArr[] = "$key=" . urlencode($val);
    }
    $keyStr = implode("&", $valueArr);
    $combined .= ($keyStr);
    return $combined;
}


/**
 * http get 请求
 *
 * @param $url
 *
 * @return array|string[]|unknown[]
 */
function ihttp_get($url)
{
    return ihttp_request($url);
}

/**
 * http post 请求
 *
 * @param $url
 * @param $data
 *
 * @return array|string[]|unknown[]
 */
function ihttp_post($url, $data)
{
    $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
    return ihttp_request($url, $data, $headers);
}


/**
 * http 请求
 *
 * @param string $url
 * @param string $post
 * @param array  $extra
 * @param int    $timeout
 *
 * @return array|string[]|unknown[]
 */
function ihttp_request($url = '', $post = '', $extra = array(), $timeout = 60)
{
    $urlset = parse_url($url);
    if (empty($urlset['path'])) {
        $urlset['path'] = '/';
    }
    if (!empty($urlset['query'])) {
        $urlset['query'] = "?{$urlset['query']}";
    }
    if (empty($urlset['port'])) {
        $urlset['port'] = $urlset['scheme'] == 'https' ? '443' : '80';
    }
    if (strexists($url, 'https://') && !extension_loaded('openssl')) {
        if (!extension_loaded("openssl")) {
            message('请开启您PHP环境的openssl');
        }
    }
    if (function_exists('curl_init') && function_exists('curl_exec')) {
        $ch = curl_init();
        if (ver_compare(phpversion(), '5.6') >= 0) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        }
        if (!empty($extra['ip'])) {
            $extra['Host'] = $urlset['host'];
            $urlset['host'] = $extra['ip'];
            unset($extra['ip']);
        }
        curl_setopt($ch, CURLOPT_URL, $urlset['scheme'] . '://' . $urlset['host'] . ($urlset['port'] == '80' ? '' : ':' . $urlset['port']) . $urlset['path'] . $urlset['query']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        @curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        if ($post) {
            if (is_array($post)) {
                $filepost = false;
                foreach ($post as $name => $value) {
                    if ((is_string($value) && substr($value, 0, 1) == '@') || (class_exists('CURLFile') && $value instanceof CURLFile)) {
                        $filepost = true;
                        break;
                    }
                }
                if (!$filepost) {
                    $post = http_build_query($post);
                }
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        if (defined('CURL_SSLVERSION_TLSv1')) {
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
        if (!empty($extra) && is_array($extra)) {
            $headers = array();
            foreach ($extra as $opt => $value) {
                if (strexists($opt, 'CURLOPT_')) {
                    curl_setopt($ch, constant($opt), $value);
                } elseif (is_numeric($opt)) {
                    curl_setopt($ch, $opt, $value);
                } else {
                    $headers[] = "{$opt}: {$value}";
                }
            }
            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
        }
        $data = curl_exec($ch);
        $status = curl_getinfo($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($errno || empty($data)) {
            return error(1, $error);
        } else {
            return ihttp_response_parse($data);
        }
    }
    $method = empty($post) ? 'GET' : 'POST';
    $fdata = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\n";
    $fdata .= "Host: {$urlset['host']}\r\n";
    if (function_exists('gzdecode')) {
        $fdata .= "Accept-Encoding: gzip, deflate\r\n";
    }
    $fdata .= "Connection: close\r\n";
    if (!empty($extra) && is_array($extra)) {
        foreach ($extra as $opt => $value) {
            if (!strexists($opt, 'CURLOPT_')) {
                $fdata .= "{$opt}: {$value}\r\n";
            }
        }
    }
    $body = '';
    if ($post) {
        if (is_array($post)) {
            $body = http_build_query($post);
        } else {
            $body = urlencode($post);
        }
        $fdata .= 'Content-Length: ' . strlen($body) . "\r\n\r\n{$body}";
    } else {
        $fdata .= "\r\n";
    }
    if ($urlset['scheme'] == 'https') {
        $fp = fsockopen('ssl://' . $urlset['host'], $urlset['port'], $errno, $error);
    } else {
        $fp = fsockopen($urlset['host'], $urlset['port'], $errno, $error);
    }
    stream_set_blocking($fp, true);
    stream_set_timeout($fp, $timeout);
    if (!$fp) {
        return error(1, $error);
    } else {
        fwrite($fp, $fdata);
        $content = '';
        while (!feof($fp))
            $content .= fgets($fp, 512);
        fclose($fp);
        return ihttp_response_parse($content, true);
    }
}


/**
 * 判断字段是否包含···
 *
 * @param $string
 * @param $find
 *
 * @return bool
 */
function strexists($string, $find)
{
    return !(strpos($string, $find) === FALSE);
}

/**
 * 版本比较
 *
 * @param $version1
 * @param $version2
 *
 * @return mixed
 */
function ver_compare($version1, $version2)
{
    $version1 = str_replace('.', '', $version1);
    $version2 = str_replace('.', '', $version2);
    $oldLength = istrlen($version1);
    $newLength = istrlen($version2);
    if (is_numeric($version1) && is_numeric($version2)) {
        if ($oldLength > $newLength) {
            $version2 .= str_repeat('0', $oldLength - $newLength);
        }
        if ($newLength > $oldLength) {
            $version1 .= str_repeat('0', $newLength - $oldLength);
        }
        $version1 = intval($version1);
        $version2 = intval($version2);
    }
    return version_compare($version1, $version2);
}

/**
 * 编码
 *
 * @param        $string
 * @param string $charset
 *
 * @return int
 */
function istrlen($string, $charset = '')
{
    global $_W;
    if (empty($charset)) {
        $charset = $_W['charset'];
    }
    if (strtolower($charset) == 'gbk') {
        $charset = 'gbk';
    } else {
        $charset = 'utf8';
    }
    if (function_exists('mb_strlen')) {
        return mb_strlen($string, $charset);
    } else {
        $n = $noc = 0;
        $strlen = strlen($string);

        if ($charset == 'utf8') {

            while ($n < $strlen) {
                $t = ord($string[$n]);
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $n += 2;
                    $noc++;
                } elseif (224 <= $t && $t <= 239) {
                    $n += 3;
                    $noc++;
                } elseif (240 <= $t && $t <= 247) {
                    $n += 4;
                    $noc++;
                } elseif (248 <= $t && $t <= 251) {
                    $n += 5;
                    $noc++;
                } elseif ($t == 252 || $t == 253) {
                    $n += 6;
                    $noc++;
                } else {
                    $n++;
                }
            }
        } else {

            while ($n < $strlen) {
                $t = ord($string[$n]);
                if ($t > 127) {
                    $n += 2;
                    $noc++;
                } else {
                    $n++;
                    $noc++;
                }
            }
        }

        return $noc;
    }
}

/**
 * 远程接口请求 并返回数据
 *
 * @param      $data
 * @param bool $chunked
 *
 * @return array
 */
function ihttp_response_parse($data, $chunked = false)
{
    $rlt = array();
    $headermeta = explode('HTTP/', $data);
    if (count($headermeta) > 2) {
        $data = 'HTTP/' . array_pop($headermeta);
    }
    $pos = strpos($data, "\r\n\r\n");
    $split1[0] = substr($data, 0, $pos);
    $split1[1] = substr($data, $pos + 4, strlen($data));

    $split2 = explode("\r\n", $split1[0], 2);
    preg_match('/^(\S+) (\S+) (\S+)$/', $split2[0], $matches);
    $rlt['code'] = $matches[2];
    $rlt['status'] = $matches[3];
    $rlt['responseline'] = $split2[0];
    $header = explode("\r\n", $split2[1]);
    $isgzip = false;
    $ischunk = false;
    foreach ($header as $v) {
        $pos = strpos($v, ':');
        $key = substr($v, 0, $pos);
        $value = trim(substr($v, $pos + 1));
        if (is_array($rlt['headers'][$key])) {
            $rlt['headers'][$key][] = $value;
        } elseif (!empty($rlt['headers'][$key])) {
            $temp = $rlt['headers'][$key];
            unset($rlt['headers'][$key]);
            $rlt['headers'][$key][] = $temp;
            $rlt['headers'][$key][] = $value;
        } else {
            $rlt['headers'][$key] = $value;
        }
        if (!$isgzip && strtolower($key) == 'content-encoding' && strtolower($value) == 'gzip') {
            $isgzip = true;
        }
        if (!$ischunk && strtolower($key) == 'transfer-encoding' && strtolower($value) == 'chunked') {
            $ischunk = true;
        }
    }
    if ($chunked && $ischunk) {
        $rlt['content'] = ihttp_response_parse_unchunk($split1[1]);
    } else {
        $rlt['content'] = $split1[1];
    }
    if ($isgzip && function_exists('gzdecode')) {
        $rlt['content'] = gzdecode($rlt['content']);
    }

    $rlt['meta'] = $data;
    if ($rlt['code'] == '100') {
        return ihttp_response_parse($rlt['content']);
    }
    return $rlt;
}


/**
 * 微信auth2.0 换取code
 *
 * @param $redirecturl
 * @param $info
 */
function wxAuth($redirecturl, $info)
{
    $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $info['appid'] . "&redirect_uri=" . urlencode($redirecturl) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
    header("location:$oauth2_code");
    exit();
}

/**
 * 获取accesstoken
 *
 * @param $code
 * @param $info
 *
 * @return mixed
 */
function wxAccessToken($code, $info)
{
    $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$info['appid']}&secret={$info['appsecret']}&code={$code}&grant_type=authorization_code";
    $content = ihttp_get($oauth2_code);
    $token = @json_decode($content['content'], true);
    return $token;
}

/**
 * token换取用户信息
 *
 * @param $token
 *
 * @return mixed
 */
function tokenToinfo($token)
{
    $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$token ['access_token']}&openid={$token ['openid']}&lang=zh_CN";
    $json = ihttp_get($url);
    $userInfo = @json_decode($json['content'], true);
    return $userInfo;
}

/**
 * 获取用户信息
 *
 * @param $code
 * @param $info
 *
 * @return mixed
 */
function wxGetUserInfo($code, $info)
{
    $token = wxAccessToken($code, $info);
    $userInfo = tokenToinfo($token);
    return $userInfo;
}

/**
 * @param string $openid
 * @param string $prefix
 * @param float  $money
 *
 * @return string
 */
function createNO($openid = '', $prefix = '', $money = 0.00)
{
    $billno = createOrder();
    $data['openid'] = $openid;
    $data['ordersn'] = $billno;
    $data['createtime'] = time();
    $data['money'] = $money;
    M('orders')->add($data);
    return $prefix . $billno;;
}

/**
 * 生成订单编号
 */
function createOrder()
{
    $billno = date('YmdHis') . random(6, true);
    while (1) {
        $count = M('orders')->where(array(
            'ordersn' => $billno
        ))->count();
        if ($count <= 0) {
            break;
        }
        $billno = date('YmdHis') . random(6, true);
    }
    return $billno;
}

/**
 * 生成随机字符串
 *
 * @param      $length
 * @param bool $numeric
 *
 * @return string
 */
function str_random($length, $numeric = FALSE)
{
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    if ($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}

/**
 * 数组转xml
 *
 * @param     $arr
 * @param int $level
 *
 * @return null|string|string[]
 */
function array2xml($arr, $level = 1)
{
    $s = $level == 1 ? "<xml>" : '';
    foreach ($arr as $tagname => $value) {
        if (is_numeric($tagname)) {
            $tagname = $value['TagName'];
            unset($value['TagName']);
        }
        if (!is_array($value)) {
            $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
        } else {
            $s .= "<{$tagname}>" . array2xml($value, $level + 1) . "</{$tagname}>";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
    return $level == 1 ? $s . "</xml>" : $s;
}

/**
 *
 * @param $data
 *
 * @return bool
 */
function is_error($data)
{
    if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
        return false;
    } else {
        return true;
    }
}

/**
 * @param        $errno
 * @param string $message
 *
 * @return array
 */
function error($errno, $message = '')
{
    return array(
        'errno'   => $errno,
        'message' => $message
    );
}

/**
 * 是否微信环境
 */
function is_weixin()
{
    if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false) {
        return false;
    }
    return true;
}

/**
 * 充值
 *
 * @param $openid
 * @param $ordersn 订单号
 * @param $money
 * @param $title   标题
 * @param $account  系统账号
 * @param $notify
 *
 * @return mixed
 */
function recharege($openid, $ordersn, $money, $title, $account, $notify)
{
    if (!is_weixin()) {
        $tips['status'] = 0;
        $tips['info'] = '非微信环境';
        return $tips;
    }
    $wechat = array(
        'success' => false
    );
    $params = array();
    $params['tid'] = $ordersn;
    $params['user'] = $openid;
    $params['fee'] = $money * 100;
    $params['title'] = $title;
    $params['notify'] = $notify;
    $options = $account['wxpay'];

    $options['appid'] = $account['appid'];
    $options['secret'] = $account['appsecret'];
    $options['signkey'] = $options['apisecret'];
    $wechat = wechat_build($params, $options);
    $wechat['success'] = false;
    if (!is_error($wechat)) {
        $wechat ['success'] = true;
    } else {
        $tips['status'] = 0;
        $tips['info'] = $wechat ['message'];
        return $tips;
    }
    if (!$wechat ['success']) {
        $tips['status'] = 0;
        $tips['info'] = '微信支付参数错误!';
        return $tips;
    }
    $tips['status'] = 1;
    $tips['wechat'] = $wechat;
    return $tips;
}

/**
 * @param $params
 * @param $account
 *
 * @return string[]|unknown[]
 */
function wechat_build($params, $account)
{
    $package = array();
    $package['appid'] = $account['appid'];
    $package['mch_id'] = $account['machid'];
    $package['nonce_str'] = random(8) . "";
    $package['body'] = $params['title'];
    $package['device_info'] = "wl_yoscene";
    $package['attach'] = '12' . ':' . 0;
    $package['out_trade_no'] = $params['tid'];
    $package['total_fee'] = $params['fee'];
    $package['spbill_create_ip'] = get_client_ip();
    $package['notify_url'] = $params['notify'];
    $package['trade_type'] = 'JSAPI';
    $package['openid'] = $params['user'];
    ksort($package);
    $string1 = '';
    foreach ($package as $key => $v) {
        if (empty($v)) {
            continue;
        }
        $string1 .= "{$key}={$v}&";
    }
    $string1 .= "key={$account['signkey']}";
    $package['sign'] = strtoupper(md5($string1));
    $dat = array2xml($package);
    $response = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);
    if (is_error($response)) {
        return $response;
    }
    $xml = @simplexml_load_string($response['content'], 'SimpleXMLElement');
    if (strval($xml->return_code) == 'FAIL') {
        return error(-1, strval($xml->return_msg));
    }
    if (strval($xml->result_code) == 'FAIL') {
        return error(-1, strval($xml->err_code) . ': ' . strval($xml->err_code_des));
    }
    $prepayid = $xml->prepay_id;
    $wOpt['appId'] = $account['appid'];
    $wOpt['timeStamp'] = time() . "";
    $wOpt['nonceStr'] = random(8) . "";
    $wOpt['package'] = 'prepay_id=' . $prepayid;
    $wOpt['signType'] = 'MD5';
    ksort($wOpt);
    $string = '';
    foreach ($wOpt as $key => $v) {
        $string .= "{$key}={$v}&";
    }
    $string .= "key={$account['signkey']}";
    $wOpt['paySign'] = strtoupper(md5($string));
    return $wOpt;
}

/**
 * @param array $params
 * @param       $openid
 * @param       $amount
 *
 * @return mixed
 */
function wechat_cash($params = array(), $openid, $amount)
{
    $compay = new WxComPay();
    $compay->setApiKey($params['wxpay']['apisecret']); // 支付key
    $compay->setMchid($params['wxpay']['machid']); // 商户号
    $compay->setKey($params['wxpay']['weixin_key']);
    $compay->setCert($params['wxpay']['weixin_cert']);
    $compay->setMchAppid($params['appid']); // appid
    $compay->setOpenid($openid);
    $compay->setAmount($amount); // 付款金额 单位分 wtf！！！！
    $compay->setRemark('红包提现');
    $result = $compay->ComPay();
    $rsxml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
    if ($rsxml->return_code == 'SUCCESS') {
        if ($rsxml->result_code == 'SUCCESS') {
            $tips['status'] = 1;
            $tips['info'] = '提现成功';
        } else {
            $tips['status'] = 0;
            $tips['info'] = $rsxml->return_msg;
        }
    } else {
        $tips['status'] = 0;
        $tips['info'] = '支付失败' . $this->error = $rsxml->return_msg;
    }
    return $tips;
}

