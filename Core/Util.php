<?php
namespace Core;

use Exception\HTTPException;

class Util {

    private static $instances = array ();

    /**
     * 根据类名获取该类的单例
     *
     * @param string $className            
     * @return Object
     */
    public static function loadCls($className, $tags = '') {

        if (!array_key_exists($className . '_' . $tags, self::$instances)) {
            self::$instances[$className . '_' . $tags] = new $className();
        }
        return self::$instances[$className . '_' . $tags];
    
    }

    private static $models = array ();

    /**
     * 根据类名获取该类的单例
     *
     * @param string $className            
     * @return BaseModel
     */
    public static function loadModel($className, $entryName, $id) {

        if (!array_key_exists($className . '_' . $id, self::$models)) {
            self::$models[$className . '_' . $id] = new $className($entryName, $id);
        }
        
        return self::$models[$className . '_' . $id];
    
    }

    private static $redis = array ();

    /**
     * 根据类名获取该类的单例
     *
     * @param string $className            
     * @return \Engine\RedisEngine
     */
    public static function loadRedis($tags, $config = false) {

        if ($config === false) {
            if (!isset($GLOBALS['REDIS'][$tags])) {
                throw Util::HTTPException('load redis tags ' . $tags . ' not exists.');
            }
            $config = $GLOBALS['REDIS'][$tags];
        }
        
        if (!array_key_exists($tags, self::$redis)) {
            $className = 'Engine\\RedisEngine';
            self::$redis[$tags] = new $className();
            self::$redis[$tags]->connect($config['host'], $config['port'], $config['timeout'], $config['database'], $config['pconnect'], $config['password']);
        }
        
        return self::$redis[$tags];
    
    }

    /**
     * 抛出异常
     */
    public static function HTTPException($errMsg, $errData = null) {

        return new HTTPException($errMsg, $errData);
    
    }

    /**
     * 拆分ID
     */
    public static function id_path($id) {
        // str_pad
        $pathNum = 1000000000 + $id;
        $pathNumA = substr($pathNum, 1, 3);
        $pathNumB = substr($pathNum, 4, 3);
        $pathNumC = substr($pathNum, 7, 3);
        $user_path = $pathNumA . '/' . $pathNumB . '/' . $pathNumC;
        return $user_path;
    
    }

    /**
     * 创建目录
     */
    public static function mkdirs($path, $mode = 0755) {

        $dirs = explode('/', $path);
        $dirslen = count($dirs);
        $state = '';
        for($c = 0; $c < $dirslen; $c++) {
            $thispath = '';
            for($cc = 0; $cc <= $c; $cc++) {
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
     * @param array $weights
     *            [type => weight]
     * @return int
     */
    private static function rand($data) {

        $rd = rand(1, array_sum($data));
        $rv = 0;
        
        foreach ( $data as $type => $odds ) {
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
     * @param array $weights            
     * @param int $num            
     */
    public static function rands($data, $num = 1) {

        $items = array ();
        
        while ( $num ) {
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
     * @param array $data            
     *
     */
    public static function randItem($data) {

        $items = array ();
        $num = 0;
        foreach ( $data as $item ) {
            $num += $item[2];
        }
        
        $rd = rand(1, $num);
        $rv = 0;
        foreach ( $data as $item ) {
            $rv += $item[2];
            if ($rd <= $rv) {
                return $item;
            }
        }
    
    }

    /**
     * 获取客户端IP
     *
     * @return string
     */
    public static function getClientIP($int = true) {

        if (getenv('HTTP_CLIENT_IP')) {
            $ipAddr = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipAddr = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR')) {
            $ipAddr = getenv('REMOTE_ADDR');
        } else {
            $ipAddr = $_SERVER['REMOTE_ADDR'];
        }
        if (strchr($ipAddr, ',')) {
            $ipAddr = explode(',', $ipAddr);
            $ipAddr = $ipAddr[count($ipAddr) - 1];
        }
        $ipAddr = ltrim($ipAddr);
        
        if ($int) {
            return ip2long($ipAddr);
        }
        
        return $ipAddr;
    
    }

    /**
     * 改变图片大小
     */
    public static function resize($path, $maxW, $maxH, $npath, $lcok) {

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
            if ($lcok) {
                $newW = $maxW;
                $newH = round(($iminfo[1] * $maxW) / $iminfo[0]);
            } else {
                $newW = $maxW;
                $newH = $iminfo[1];
            }
        } else if ($resizeByH) {
            if ($lcok) {
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
                $result = imagegif($imN, $npath);
                break;
            case '2' :
                $result = imagejpeg($imN, $npath, 100);
                break;
            case '3' :
                $result = imagepng($imN, $npath);
                break;
        }
    
    }

    public static function url() {

        $args = func_get_args();
        $url = array_shift($args);
        foreach ( $args as $param ) {
            $url .= '/' . $param;
        }
        
        return REWRITE . $url;
    
    }

    /**
     * 通过curl获取远程文本内容
     */
    public static function curl_request($url, $type, $params = false) {

        $ch = curl_init();
        
        $timeout = 30;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)'); // 伪造浏览器头
        
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        if (strtoupper($type) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($params) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
        }
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response === false) {
            return false;
        }
        
        return $response;
    
    }

    public static function async($ctrl, $method, $params) {

        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        
        $client->connect('127.0.0.1', 9501);
        
        $client->send(json_encode(array (
            'method' => 'process', 
            'params' => array (
                'ctrl' => $ctrl, 
                'method' => $method, 
                'params' => $params 
            ) 
        )));
    
    }

    /**
     * 检查email格式是否合法
     */
    public static function check_email($email) {

        $pattern = '/^([0-9a-z\-_\.]+)@([0-9a-z]+\.[a-z]{2,30}(\.[a-z]{2,30})?)$/i';
        
        if (preg_match($pattern, $email)) {
            return true;
        } else {
            return false;
        }
    
    }

    public static function send_mail($to, $title, $body, $attach = array()) {

        Util::async('Engine\MailEngine', 'send', array (
            $to, 
            $title, 
            $body, 
            $attach 
        ));
    
    }

    public static function millisecond() {

        list ($usec, $sec) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($usec) + floatval($sec)) * 1000);
    
    }

}
?>