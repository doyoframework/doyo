<?php
namespace Engine;

use Core\Util;

class FileEngine {

    private $debug = false;

    /**
     * 验证文件大小
     *
     * @return array
     *
     */
    private function check_file_size($file, $size = FILE_SIZE) {

        if ($file['size'] > $size) {
            return array (
                'code' => -1, 
                'msg' => 'file size error' 
            );
        } else {
            return array (
                'code' => 1 
            );
        }
    
    }

    /**
     * 验证文件类型
     *
     * @return array
     *
     */
    public function check_file_type($name) {

        $stype = '';
        
        $type = explode('.', $name);
        $type = strtolower($type[1]);
        
        switch ($type) {
            
            case 'gif' :
                $stype = 'gif';
                break;
            case 'jpg' :
                $stype = 'jpg';
                break;
            case 'png' :
                $stype = 'png';
                break;
            case 'swf' :
                $stype = 'swf';
                break;
            
            case 'txt' :
                $stype = 'txt';
                break;
            
            case 'doc' :
                $stype = 'doc';
                break;
            case 'docx' :
                $stype = 'docx';
                break;
            
            case 'xls' :
                $stype = 'xls';
                break;
            case 'xlsx' :
                $stype = 'xlsx';
                break;
            
            case 'zip' :
                $stype = 'zip';
                break;
            case 'rar' :
                $stype = 'rar';
                break;
            
            case 'mp3' :
                $stype = 'mp3';
                break;
            case 'mp4' :
                $stype = 'mp4';
                break;
            
            case 'application/x-shockwave-flash' :
                $stype = 'swf';
                break;
            
            default :
                return array (
                    'code' => -2, 
                    'msg' => 'file type error.' 
                );
                break;
        }
        return array (
            'code' => 1, 
            'type' => $stype 
        );
    
    }

    /**
     * @param $file
     * @param $index
     * @param string $sort
     * @param int $part
     * @param int $maxW
     * @param int $maxH
     * @param int $minW
     * @param int $minH
     * @param bool $lock
     * @param bool $copy
     * @return bool|string
     * @throws \Exception\HTTPException
     */
    public function upload($file, $index, $sort = '/', $part = 0, $maxW = 0, $maxH = 0, $minW = 0, $minH = 0, $lock = true, $copy = false) {

        if (is_string($file)) {
            $file = $_FILES[$file];
        }
        if (empty($file)) {
            return false;
        }
        
        // 判断大小
        $size = $this->check_file_size($file);
        if ($size['code'] !== 1) {
            throw Util::HTTPException($size['msg']);
        }
        
        // 判断类型
        $type = $this->check_file_type($file['name']);
        if ($type['code'] !== 1) {
            throw Util::HTTPException($type['msg']);
        }
        
        // 设置目录和名称
        $path = $this->set_file_path($index, $sort, $part, $type['type']);
        
        // 缩略图路径
        $npath = $path['path'] . $path['name'];
        $spath = $path['path'] . $path['sname'];
        $tpath = $path['path'] . $path['tname'];
        
        if ($this->debug) {
            echo '<pre>';
            print_r($path);
            echo '</pre>';
            echo 'maxW :' . $maxW;
            echo '<br/>';
            echo 'maxH: ' . $maxH;
            echo '<br/>';
            echo 'lock: ' . $lock;
            echo '<br/>';
        }
        if (strtolower(FILE_SERVER_HOST) != 'localhost') {
            
            if (strtolower(FILE_SERVER_HOST) == 'alioss') {
                
                move_uploaded_file($file['tmp_name'], $file['tmp_name']);
                
                Util::async('Sdk\Alioss', 'upload', array (
                    $path, 
                    $file 
                ));
            } else {
                
                $this->do_upload($file, $path, $maxW, $maxH, $minW, $minH, $lock, $copy);
            }
        } else {
            
            // 创建目录
            Util::mkdirs($path['path']);
            
            // 上传文件
            move_uploaded_file($file['tmp_name'], $npath);
            
            // 判断是否是图片，创建缩略图
            if ($this->is_image($path['type'])) {
                // 复制一个备份_tmp.xxx
                if ($copy) {
                    copy($npath, $tpath);
                }
                
                // 按照最大宽高保存图片
                if ($maxW || $maxH) {
                    $this->_resave($npath, $maxW, $maxH, $npath, $lock);
                }
                
                // 按照最小宽高保存缩略图片
                if ($minW || $minH) {
                    $this->_resave($npath, $minW, $minH, $spath, $lock);
                }
            }
        }
        
        return $path;
    
    }

    /**
     * 删除图片
     */
    public function delete($index, $sort, $part, $type) {

        $path = $this->set_file_path($index, $sort, $part, $type);
        
        if ($this->debug) {
            echo '<pre>';
            print_r($path);
            echo '</pre>';
        }
        
        if (strtolower(FILE_SERVER_HOST) != 'localhost') {
            
            if (strtolower(FILE_SERVER_HOST) == 'alioss') {
                
                Util::async('Sdk\Alioss', 'delete', array (
                    $path 
                ));
            } else {
                $this->do_delete($path);
            }
        } else {
            
            $dirs = $path['path'];
            
            $npath = $dirs . $path['name'];
            $tpath = $dirs . $path['tname'];
            $spath = $dirs . $path['sname'];
            
            if (file_exists($npath)) {
                unlink($npath);
            }
            
            if ($this->is_image($path['type'])) {
                if (file_exists($tpath)) {
                    unlink($tpath);
                }
                if (file_exists($spath)) {
                    unlink($spath);
                }
            }
        }
    
    }

    public function read($file) {

        return file($file['tmp_name']);
    
    }

    public function resave($data, $index, $sort, $part, $type, $copy = false) {

        $path = $this->set_file_path($index, $sort, $part, $type);
        
        if (strtolower(FILE_SERVER_HOST) != 'localhost') {
            
            if (strtolower(FILE_SERVER_HOST) == 'alioss') {
            } else {
                // 将文件base64编码
                $contents = base64_encode($data);
                
                $args = array (
                    $path, 
                    $contents, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    $copy 
                );
                
                $this->xmlrpc_request('insert', $args);
            }
        } else {
            
            // 创建目录
            Util::mkdirs($path['path']);
            
            $npath = $path['path'] . $path['name'];
            $tpath = $path['path'] . $path['tname'];
            
            // 保存写入文件
            file_put_contents($npath, $data);
            
            // 是否创建副本
            if ($copy) {
                file_put_contents($tpath, $data);
            }
        }
        
        return $path;
    
    }

    /**
     * 判断是否是图片
     */
    private function is_image($type) {

        if (in_array(strtolower($type), array (
            'jpg', 
            'gif', 
            'png' 
        ))) {
            return true;
        }
        return false;
    
    }

    /**
     *
     * @param file $file            
     * @param string $path            
     * @param number $maxW            
     * @param number $maxH            
     * @param number $minW            
     * @param number $minH            
     * @param boolean $lock            
     *
     */
    private function do_upload($file, $path, $maxW, $maxH, $minW, $minH, $lock, $copy) {
        
        // 将文件以二进制读取到一个对象内
        $handle = fopen($file['tmp_name'], 'a+');
        
        if (flock($handle, LOCK_EX)) { // 进行排它型锁定
            $contents = '';
            while ( !feof($handle) ) {
                $contents .= fread($handle, 8192);
            }
        }
        
        flock($handle, LOCK_UN); // 释放锁定
        fclose($handle);
        
        // 将文件base64编码
        $contents = base64_encode($contents);
        
        $args = array (
            $path, 
            $contents, 
            $maxW, 
            $maxH, 
            $minW, 
            $minH, 
            $lock, 
            $copy 
        );
        
        $ret = $this->xmlrpc_request('insert', $args);
        
        return $ret;
    
    }

    /**
     * 获得文件路径
     *
     * @return string
     *
     */
    public function set_file_path($index, $sort, $part, $_type) {

        if ($sort[0] != '/') {
            $sort = '/' . $sort;
        }
        
        if ($sort[strlen($sort) - 1] != '/') {
            $sort .= '/';
        }
        
        $type = strtolower($_type);
        
        $_path_num = array ();
        
        /**
         * 判断是否是数字
         */
        if (is_numeric($index)) {
            /**
             * 获得数字基数
             */
            $_number = $index + 1000000000;
            
            /**
             * 获取数字分级
             */
            $_path_num[0] = substr($_number, 1, 3);
            $_path_num[1] = substr($_number, 4, 3);
            
            /**
             * 获取临时文件名
             */
            $_tmp_name = substr($_number, 7);
            
            /**
             * 小路径
             */
            $spath = $sort . $_path_num[0] . '/' . $_path_num[1] . '/';
            
            /**
             * 全路径
             */
            $path = APP_PATH . '/' . WEBROOT . '/' . FILE_PATH . $spath;
            
            /**
             * 设置文件名称
             */
            $_tmp_ = $_tmp_name . 'n' . strtoupper(substr(md5($index . FILE_TAGS . $part), 2, 6));
        } else {
            throw Util::HTTPException('index error');
        }
        
        $src = '/' . FILE_PATH . $spath . $_tmp_ . '.' . $type;
        
        return array (
            'sort' => $sort, 
            'type' => $type, 
            'name' => ($_tmp_ . '.' . $type), 
            'tname' => ($_tmp_ . '_tmp.' . $type), 
            'sname' => ($_tmp_ . '_small.' . $type), 
            'base' => FILE_PATH, 
            'spath' => $spath, 
            'path' => $path, 
            'src' => $src 
        );
    
    }

    /**
     * 删除远程文件
     */
    private function do_delete($path) {

        $args = array (
            $path 
        );
        
        return $this->xmlrpc_request('delete', $args);
    
    }

    /**
     * xmlrpc client
     */
    private function xmlrpc_request($method, $args) {

        $fp = fsockopen(FILE_SERVER_IP, FILE_SERVER_PORT, $errno, $errstr, $timeout = 30);
        
        if (!$fp) {
            echo 'Socket upload error.';
            return false;
        }
        
        // 把需要发送的XML请求进行编码成XML文件
        $request = xmlrpc_encode_request($method, $args);
        
        // 构造需要进行通信的XML-RPC服务器端的查询POST请求信息
        $query = 'POST ' . FILE_SERVER_EXEC . ' HTTP/1.0' . "\n";
        $query .= 'User_Agent: XML-RPC Client' . "\n";
        $query .= 'Host: ' . FILE_SITE . "\n";
        $query .= 'Content-Type: text/xml' . "\n";
        $query .= 'Content-Length: ' . strlen($request) . "\n\n" . $request . "\n";
        
        // 把构造好的HTTP协议发送给服务器，失败返回false
        if (!fputs($fp, $query, strlen($query))) {
            echo 'Write error';
            return false;
        }
        
        // 获取从服务器端返回的所有信息，包括HTTP头和XML信息
        $contents = '';
        while ( !feof($fp) ) {
            $contents .= fgets($fp);
        }
        
        // 关闭连接资源
        fclose($fp);
        
        $split = '<?xml version="1.0" encoding="iso-8859-1"?>';
        $xml = explode($split, $contents);
        $xml = $split . array_pop($xml);
        $res = xmlrpc_decode($xml);
        
        return $res;
    
    }

    /**
     * 设置缩略图
     */
    private function _resave($path, $ruleW, $ruleH, $npath, $lock = true) {

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
        
        if ($iminfo[0] > $ruleW && $ruleW) {
            $resizeByW = true;
        }
        if ($iminfo[1] > $ruleH && $ruleH) {
            $resizeByH = true;
        }
        
        if ($resizeByH && $resizeByW) {
            $resizeByH = ($iminfo[0] / $ruleW < $iminfo[1] / $ruleH);
            $resizeByW = !$resizeByH;
        }
        
        if ($resizeByW) {
            if ($lock) {
                $newW = $ruleW;
                $newH = round(($iminfo[1] * $ruleW) / $iminfo[0]);
            } else {
                $newW = $ruleW;
                $newH = $iminfo[1];
            }
        } else if ($resizeByH) {
            if ($lock) {
                $newW = round(($iminfo[0] * $ruleH) / $iminfo[1]);
                $newH = $ruleH;
            } else {
                $newW = $iminfo[0];
                $newH = $ruleH;
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

}
?>