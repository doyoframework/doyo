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
     * @param array $param
     * @param bool $single
     * @return mixed
     */
    public static function loadCls($class_name, $tags = '', $param = array(), $single = true)
    {
        if (empty($param)) {

            if (!$single) {
                return new $class_name();
            }

            if (!isset(self::$instances[$class_name . '_' . $tags])) {
                self::$instances[$class_name . '_' . $tags] = new $class_name();
            }

            return self::$instances[$class_name . '_' . $tags];

        } else {

        }
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
     * @return BaseCtrl
     */
    public static function loadCtrl($clsName)
    {

        return Util::loadCls($clsName);

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
     * 定时任务单例
     * @var array
     */
    private static $crontab = array();

    /**
     * 加载定时任务
     *
     * @param $tags
     * @param string $config
     * @return \Engine\CrontabEngine
     */
    public static function loadCrontab($tags = '__CRONTAB__', $config = 'session')
    {

        if (!isset(self::$crontab[$tags])) {
            $className = 'Engine\\CrontabEngine';
            self::$crontab[$tags] = new $className($tags, $config);
        }

        return self::$crontab[$tags];
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
        $pathNum = 10000000000 + $id;
        $pathNumA = substr($pathNum, 1, 4);
        $pathNumB = substr($pathNum, 4, 3);
        $pathNumC = substr($pathNum, 8, 3);
        $user_path = $pathNumA . '/' . $pathNumB . '/' . $pathNumC;
        return $user_path;

    }

    /**
     * 是否移动设备
     *
     * @return bool
     */
    public static function is_mobile()
    {

        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }

        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }

        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );

            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }

        return false;

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
     * 按权重获取多个
     *
     * @param $data
     * @param $odds
     * @param int $num
     * @return array
     */
    public static function randItem($data, $odds, $num = 1)
    {

        $items = array();

        while ($num) {
            $item = self::randItems($data, $odds);

            if ($item) {
                $items[] = $item;
            } else {
                break;
            }

            $num--;
        }

        return $items;

    }

    /**
     * 按权重获取物品
     *
     * @param $data
     * @param $odds
     * @return array
     */
    private static function randItems(&$data, $odds)
    {

        $num = 0;
        foreach ($data as $item) {
            $num += $item[$odds];
        }

        $rd = rand(1, $num);
        $rv = 0;

        foreach ($data as $k => $item) {
            $rv += $item[$odds];

            if ($rd <= $rv) {

                unset($data[$k]);

                return $item;
            }

        }

        return [];
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

        $im = null;

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
     * 写入文件
     *
     * @param $path
     * @param $data
     */
    public static function write($path, $data)
    {
        $fp = fopen($path, 'w');
        fwrite($fp, $data);
        fclose($fp);
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

    }

    /**
     * @param $op
     * @param $param
     * @throws HTTPException
     */
    public static function task($op, $param)
    {
        //向Redis队列增加一条数据
        $data = array();
        $data['op'] = $op;
        $data['param'] = $param;
        Util::async($data);

    }

    /**
     * @param $data
     * @throws HTTPException
     */
    public static function async($data)
    {
        $crontab = Util::loadRedis('crontab');

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $crontab->lpush(strtoupper(SERVER_KEY) . '_ASYNC', $data);
    }

    /**
     * @param $op
     * @param $param
     * @throws HTTPException
     */
    public static function queue($op, $param)
    {
        $crontab = Util::loadRedis('crontab');

        //向Redis队列增加一条数据
        $data = array();
        $data['op'] = $op;
        $data['param'] = $param;

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $crontab->lpush(strtoupper(SERVER_KEY) . '_QUEUE', $data);
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

        return $xml;
    }

    /**
     * @param $ary
     * @return string
     */
    public static function toXml($ary)
    {

        return $ary;
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

    public static function time($day, $format = 'Y-m-d')
    {
        return strtotime(date($format, time() + $day * 86400));
    }

    public static function logs($content, $file = false)
    {
        if (!$file) {
            $file = '/tmp/doyo.' . date('Y-m-d') . '.log';
        }
        file_put_contents($file, '[' . date('Y-m-d H:i:s') . ']' . $content . "\n", FILE_APPEND);
    }

    public static function week($week)
    {
        switch ($week) {
            case 1 :
                return '一';
            case 2 :
                return '二';
            case 3 :
                return '三';
            case 4 :
                return '四';
            case 5 :
                return '五';
            case 6 :
                return '六';
            case 7 :
                return '日';
        }
    }
}

