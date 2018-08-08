<?php

namespace Core;

use Engine\RedisEngine;
use Engine\RedLock;
use Exception\HTTPException;
use Sdk\QRCode;

class Util
{

    private static $instances = array();

    /**
     * 根据类名获取该类的单例
     *
     * @param $class_name
     * @param string $tags
     * @return object
     */
    public static function loadCls($class_name, $tags = '')
    {
        if (!isset(self::$instances[$class_name . '_' . $tags])) {
            self::$instances[$class_name . '_' . $tags] = new $class_name();
        }
        return self::$instances[$class_name . '_' . $tags];
    }

    private static $models = array();

    /**
     * 根据类名获取该类的单例
     *
     * @param $class_name
     * @param $entry_name
     * @param $id
     * @return BaseModel
     */
    public static function loadModel($class_name, $entry_name, $id)
    {
        if (!isset(self::$models[$class_name . '_' . $id])) {
            self::$models[$class_name . '_' . $id] = new $class_name($entry_name, $id);
        }

        return self::$models[$class_name . '_' . $id];
    }

    private static $redis = array();

    /**
     * 根据类名获取该类的单例
     *
     * @param $tags
     * @param bool $config
     * @return RedisEngine
     * @throws HTTPException
     */
    public static function loadRedis($tags, $config = false)
    {

        if ($config === false) {
            if (!isset($GLOBALS['REDIS'][$tags])) {
                throw Util::HTTPException('load redis tags ' . $tags . ' not exists.');
            }
            $config = $GLOBALS['REDIS'][$tags];
        }

        if (!isset(self::$redis[$tags])) {
            $className = 'Engine\\RedisEngine';
            self::$redis[$tags] = new $className();
            self::$redis[$tags]->connect($config['host'], $config['port'], $config['timeout'], $config['database'], $config['pconnect'], $config['password']);
        }

        if (self::$redis[$tags]->ping()) {
            self::$redis[$tags]->reconnect();
        }

        return self::$redis[$tags];

    }

    /**
     * 加载Ctrl类
     *
     * @param $clsName
     * @return BaseCtrl|object
     */
    public static function loadCtrl($clsName)
    {

        return Util::loadCls("Ctrl\\{$clsName}");

    }


    /**
     * * 初始化upload配置
     *
     * @return \Engine\FileEngine|object
     */
    public static function initFiles()
    {

        return Util::loadCls('Engine\FileEngine');

    }

    /**
     * 加载短信发送类
     *
     * @return \Alisms|object
     */
    public static function loadSms()
    {

        return Util::loadCls("Alisms");

    }

    /**
     * 加载短信发送类
     *
     * @return \Sdk\Alioss|object
     */
    public static function loadOss()
    {

        return Util::loadCls("Sdk\Alioss");

    }

    /**
     * @var RedLock
     */
    private static $lock = null;


    /**
     * @param $server
     *
     * @return RedLock
     */
    public static function redLock($server = array())
    {
        if (self::$lock == null) {
            if (empty($server)) {
                self::$lock = new RedLock($GLOBALS['REDIS']['lock']);
            } else {
                self::$lock = new RedLock($server);
            }
        }
        return self::$lock;
    }

    /**
     * 抛出异常
     *
     * @param $errMsg
     * @param int $errCode
     * @param null $errData
     * @return HTTPException
     */
    public static function HTTPException($errMsg, $errCode = -1, $errData = null)
    {

        return new HTTPException($errMsg, $errCode, $errData);

    }

    /**
     * 拆分ID
     *
     * @param $id
     * @return string
     */
    public static function id_path($id)
    {
        // str_pad
        $pathNum = 1000000000 + $id;
        $pathNumA = substr($pathNum, 1, 3);
        $pathNumB = substr($pathNum, 4, 3);
        $pathNumC = substr($pathNum, 7, 3);
        $user_path = $pathNumA . '/' . $pathNumB . '/' . $pathNumC;
        return $user_path;

    }

    /**
     * 递归创建目录
     *
     * @param $path
     * @param int $mode
     * @return bool|string
     */
    public static function mkdirs($path, $mode = 0755)
    {

        $dirs = explode('/', $path);
        $dirslen = count($dirs);
        $state = '';
        for ($c = 0; $c < $dirslen; $c++) {
            $thispath = '';
            for ($cc = 0; $cc <= $c; $cc++) {
                $thispath .= $dirs[$cc] . '/';
            }
            if (!@file_exists($thispath)) {
                $thispaths = substr($thispath, 0, strrpos($thispath, '/'));
                $state = @mkdir($thispaths, $mode);
            }
        }
        return $state;

    }

    /**
     * 按权重获取类型
     *
     * @param $data
     * @return int|string
     */
    private static function rand($data)
    {

        $rd = rand(1, array_sum($data));
        $rv = 0;

        foreach ($data as $type => $odds) {
            $rv += $odds;

            if ($rd <= $rv) {
                return $type;
            }
        }

        return 0;

    }

    /**
     * 按权重获取多个
     *
     * @param $data
     * @param int $num
     * @return array
     */
    public static function rands($data, $num = 1)
    {

        $items = array();

        while ($num) {
            $item = self::rand($data);

            if ($item)
                $items[] = $item;
            else
                break;

            unset($data[$item]);
            $num--;
        }

        return $items;

    }

    /**
     * 按权重获取物品
     *
     * @param $data
     * @return mixed
     */
    public static function randItem($data)
    {

        $num = 0;
        foreach ($data as $item) {
            $num += $item[2];
        }

        $rd = rand(1, $num);
        $rv = 0;
        foreach ($data as $item) {
            $rv += $item[2];
            if ($rd <= $rv) {
                return $item;
            }
        }

    }

    /**
     * 改变图片大小
     *
     * @param $path
     * @param $maxW
     * @param $maxH
     * @param $npath
     * @param $lock
     * @param int $quality
     */
    public static function resize($path, $maxW, $maxH, $npath, $lock, $quality = 100)
    {

        $iminfo = getimagesize($path);

        switch ($iminfo[2]) {
            case 1 :
                $im = imagecreatefromgif($path);
                break; /* gif */
            case 2 :
                $im = imagecreatefromjpeg($path);
                break; /* jpg */
            case 3 :
                $im = imagecreatefrompng($path);
                break; /* png */
        }

        $resizeByW = $resizeByH = false;

        if ($iminfo[0] > $maxW && $maxW) {
            $resizeByW = true;
        }
        if ($iminfo[1] > $maxH && $maxH) {
            $resizeByH = true;
        }

        if ($resizeByH && $resizeByW) {
            $resizeByH = ($iminfo[0] / $maxW < $iminfo[1] / $maxH);
            $resizeByW = !$resizeByH;
        }

        if ($resizeByW) {
            if ($lock) {
                $newW = $maxW;
                $newH = round(($iminfo[1] * $maxW) / $iminfo[0]);
            } else {
                $newW = $maxW;
                $newH = $iminfo[1];
            }
        } else if ($resizeByH) {
            if ($lock) {
                $newW = round(($iminfo[0] * $maxH) / $iminfo[1]);
                $newH = $maxH;
            } else {
                $newW = $iminfo[0];
                $newH = $maxH;
            }
        } else {
            $newW = $iminfo[0];
            $newH = $iminfo[1];
        }
        $imN = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($imN, $im, 0, 0, 0, 0, $newW, $newH, $iminfo[0], $iminfo[1]);
        switch ($iminfo[2]) {
            case '1' :
                imagegif($imN, $npath);
                break;
            case '2' :
                imagejpeg($imN, $npath, $quality);
                break;
            case '3' :
                imagepng($imN, $npath);
                break;
        }

    }

    /**
     * 转换URL
     *
     * @return string
     */
    public static function url()
    {

        $args = func_get_args();
        $url = array_shift($args);
        foreach ($args as $param) {
            $url .= '/' . $param;
        }

        return REWRITE . $url;

    }

    /**
     * 通过curl获取远程文本内容
     *
     * @param $url
     * @param $type
     * @param bool $params
     * @param array $header
     * @return bool|mixed
     */
    public static function curl_request($url, $type = 'GET', $params = false, $header = array())
    {

        $ch = curl_init();

        $timeout = 30;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (count($header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($ch, CURLOPT_HEADER, 0);

        if (strtoupper($type) == 'FILE') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            if (strtoupper($type) == 'POST') {
                curl_setopt($ch, CURLOPT_POST, 1);
                if ($params) {
                    if (is_array($params)) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                    } else {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    }
                }
            }
        }

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return false;
        }

        return $response;

    }

    /**
     * 检查email格式是否合法
     *
     * @param $email
     * @return bool
     */
    public static function check_email($email)
    {

        $pattern = '/^([0-9a-z\-_\.]+)@([0-9a-z]+\.[a-z]{2,30}(\.[a-z]{2,30})?)$/i';

        if (preg_match($pattern, $email)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 检查passport格式是否合法
     *
     * @param $passport
     * @return bool
     */
    public static function check_passport($passport)
    {

        if (!preg_match("/^[a-zA-Z0-9_＼x7f-＼xff][a-zA-Z0-9_＼x7f-＼xff]+$/", $passport)) {
            return false;
        }

        return true;
    }

    /**
     * 异步发送邮件
     *
     * @param $to
     * @param $title
     * @param $body
     * @param array $attach
     */
    public static function send_mail($to, $title, $body, $attach = array())
    {

        Util::async('Engine\MailEngine', 'send', array(
            $to,
            $title,
            $body,
            $attach
        ));

        file_put_contents('/tmp/swoole.process.log', 'mail send: ' . $title . "\n\n\n", FILE_APPEND);

    }

    /**
     * 当前系统毫秒
     *
     * @return float
     */
    public static function millisecond()
    {

        list ($usec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($usec) + floatval($sec)) * 1000);

    }

    /**
     * @param $list
     * @param $limit
     * @param $page
     * @param $offset
     * @return array
     */
    public static function page(&$list, $limit, $page, $offset = 0)
    {

        $rcount = count($list);

        $pcount = ceil($rcount / $limit);

        if ($page < 1) {
            $page = 1;
        } else if ($page > $pcount) {
            $page = $pcount;
        }

        $next = $page + 1;
        $prev = $page - 1;

        if ($next > $pcount) {
            $next = $pcount;
        }

        if ($prev < 1) {
            $prev = 1;
        }

        $_offset = (($page - 1) * $limit) + $offset;

        $dataArray = array_slice($list, $_offset, $limit);

        $array = array();
        $array['data'] = $dataArray;
        $array['limit'] = $limit;
        $array['page'] = $page;
        $array['rcount'] = $rcount;
        $array['pcount'] = $pcount;
        $array['next'] = $next;
        $array['prev'] = $prev;

        return $array;
    }

    /**
     * @param bool $long
     * @return array|false|int|mixed|string
     */
    public static function ipaddr($long = true)
    {

        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddr = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddr = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddr = getenv('REMOTE_ADDR');
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddr = $_SERVER['REMOTE_ADDR'];
        } else {
            if ($long) {
                return 0;
            } else {
                return '0.0.0.0';
            }
        }

        if (strchr($ipaddr, ',')) {
            $ipaddr = explode(',', $ipaddr);
            $ipaddr = array_pop($ipaddr);
        }

        $ipaddr = ltrim($ipaddr);

        if ($long) {
            return ip2long($ipaddr);
        }

        return $ipaddr;

    }

    /**
     * @param $xml
     * @return array
     */
    public static function parseXML($xml)
    {

        return array();
    }

    /**
     * @param $ary
     * @return string
     */
    public static function toXml($ary)
    {

        return "";
    }

    /**
     * @return QRCode|object
     */
    public static function qrcode()
    {
        return Util::loadCls('Sdk\QRCode');
    }

    /**
     * @param $string
     * @return bool
     */
    public static function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function logs($content, $file = false)
    {
        if (!$file) {
            $file = '/tmp/doyo.' . date('Y-m-d') . '.log';
        }

        file_put_contents($file, '[' . date('Y-m-d H:i:s') . ']' . $content . "\n", FILE_APPEND);
    }
}

