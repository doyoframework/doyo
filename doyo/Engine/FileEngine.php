<?php

namespace Engine;

use Core\Util;

class FileEngine
{

    private $debug = false;

    /**
     * 验证文件大小
     *
     * @param $file
     * @param $type
     * @return array
     */
    private function check_file_size($file, $type)
    {
        if ($this->is_image($type)) {
            $size = IMAGE_SIZE;
        } else {
            $size = FILE_SIZE;
        }

        if ($file['size'] > $size) {
            return array(
                'code' => -1,
                'msg' => "文件大小超过限制（{$size}）"
            );
        } else {
            return array(
                'code' => 1
            );
        }
    }

    /**
     * 验证文件类型
     *
     * @param $name
     * @return array
     */
    public function check_file_type($name)
    {

        $type = strtolower(substr($name, strrpos($name, '.') + 1));

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
            case 'xml' :
                $stype = 'xml';
                break;
            case 'log' :
                $stype = 'log';
                break;

            case 'pdf' :
                $stype = 'pdf';
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

            case 'ppt' :
                $stype = 'ppt';
                break;
            case 'pptx' :
                $stype = 'pptx';
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
            case 'avi' :
                $stype = 'avi';
                break;
            case 'mov' :
                $stype = 'mov';
                break;
            case 'wmv' :
                $stype = 'wmv';
                break;
            case '3gp' :
                $stype = '3gp';
                break;

            case 'amr' :
                $stype = 'amr';
                break;

            case 'wav' :
                $stype = 'wav';
                break;

            case 'csv' :
                $stype = 'csv';
                break;

            default :
                return array(
                    'code' => -2,
                    'msg' => '不支持的文件类型'
                );
                break;
        }
        return array(
            'code' => 1,
            'type' => $stype
        );

    }


    /**
     * 获得文件路径
     *
     * @param $index
     * @param $sort
     * @param $part
     * @param $_type
     * @param $_filename
     * @return array
     * @throws \Exception\HTTPException
     */
    public function set_file_path($index, $sort, $part, $_type, $_filename = false)
    {

        if ($sort[0] != '/') {
            $sort = '/' . $sort;
        }

        if ($sort[strlen($sort) - 1] != '/') {
            $sort .= '/';
        }

        $filename = $_filename;

        if (!$_filename) {
            $filename = uniqid();
        }

        $type = strtolower($_type);

        $_path_num = array();

        /**
         * 判断是否是数字
         */
        if (!is_numeric($index)) {
            throw Util::HTTPException('index error');
        }

        /**
         * 获得数字基数
         */
        $_number = $index + 10000000000;

        /**
         * 获取数字分级
         */
        $_path_num[0] = substr($_number, 1, 4);
        $_path_num[1] = substr($_number, 4, 3);

        /**
         * 获取临时文件名
         */
        $_tmp_name = substr($_number, 8);

        /**
         * 小路径
         */
        $spath = $sort . $_path_num[0] . '/' . $_path_num[1] . '/';

        /**
         * 全路径/data/nginx/htdocs/xxx.xxx.xxx/
         */
        $path = APP_PATH . '/' . WEBROOT . '/' . FILE_PATH . $spath;

        /**
         * 设置文件名称
         */
        $name = $_tmp_name . 'n' . strtoupper(substr(md5($index . FILE_TAGS . $part), 2, 12));

        $src = '/' . FILE_PATH . $spath . $name . '.' . $type . '?t=' . time();

        if (strtolower(FILE_SERVER_HOST) == 'localhost') {
            $url = $src;
        } else {
            $url = FILE_HOST_PROTOCOL . FILE_SITE . $src;
        }

        return array(
            'sort' => $sort,
            'type' => $type,
            'filename' => $filename,
            'name' => $name . '.' . $type,
            'tname' => $name . '_tmp.' . $type,
            'sname' => $name . '_small.' . $type,
            'base' => FILE_PATH,
            'spath' => $spath,
            'path' => $path,
            'src' => $src,
            'url' => $url
        );

    }


    /**
     * 开始上传
     *
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
     * @return array|bool
     * @throws \Exception\HTTPException
     * @throws \OSS\Core\OssException
     */
    public function upload($file, $index, $sort = '/', $part = 0, $maxW = 0, $maxH = 0, $minW = 0, $minH = 0, $lock = true, $copy = false)
    {

        if (is_string($file)) {
            $file = $_FILES[$file];
        }

        if (empty($file)) {
            return false;
        }

        // 判断类型
        $type = $this->check_file_type($file['name']);
        if ($type['code'] !== 1) {
            throw Util::HTTPException($type['msg']);
        }

        // 判断大小
        $size = $this->check_file_size($file, $type['type']);

        if ($size['code'] !== 1) {
            throw Util::HTTPException($size['msg']);
        }

        // 设置目录和名称
        $path = $this->set_file_path($index, $sort, $part, $type['type'], $file['name']);

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

                $alioss = Util::loadOss();
                $alioss->upload(OSS_BUCKET, $path, $file);

            } else {

                $this->xmlrpc_upload($file, $path, $maxW, $maxH, $minW, $minH, $lock, $copy);

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
                    $this->resize($npath, $maxW, $maxH, $npath, $lock);
                }

                // 按照最小宽高保存缩略图片
                if ($minW || $minH) {
                    $this->resize($npath, $minW, $minH, $spath, $lock);
                }
            }
        }

        return $path;

    }

    /**
     * 删除图片
     *
     * @param $index
     * @param $sort
     * @param $part
     * @param $type
     * @throws \Exception\HTTPException
     */
    public function delete($index, $sort, $part, $type)
    {

        $path = $this->set_file_path($index, $sort, $part, $type);

        if ($this->debug) {
            echo '<pre>';
            print_r($path);
            echo '</pre>';
        }

        if (strtolower(FILE_SERVER_HOST) != 'localhost') {

            if (strtolower(FILE_SERVER_HOST) == 'alioss') {

                $alioss = Util::loadOss();
                $alioss->delete(OSS_BUCKET, $path);

            } else {

                $this->xmlrpc_delete($path);

            }

        } else {

            $npath = $path['path'] . $path['name'];
            $tpath = $path['path'] . $path['tname'];
            $spath = $path['path'] . $path['sname'];

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


    /**
     * @param $filepath
     * @param $index
     * @param $sort
     * @param $part
     * @param $type
     * @param bool $is_data
     * @return array
     * @throws \Exception\HTTPException
     * @throws \OSS\Core\OssException
     */
    public function resave($filepath, $index, $sort, $part, $type, $is_data = false)
    {

        $path = $this->set_file_path($index, $sort, $part, $type);

        // 创建目录
        Util::mkdirs($path['path']);

        if (strtolower(FILE_SERVER_HOST) != 'localhost') {

            if (strtolower(FILE_SERVER_HOST) == 'alioss') {

                if ($is_data) {
                    $data = '/tmp/' . uniqid();
                    Util::write($data, $filepath);
                } else {
                    $data = $filepath;
                }

                $alioss = Util::loadOss();

                $file = array();
                $file['type'] = $type;
                $file['tmp_name'] = $data;
                $alioss->upload(OSS_BUCKET, $path, $file);

            } else {

                if ($is_data) {
                    // 将文件base64编码
                    $contents = base64_encode($filepath);
                } else {
                    $contents = base64_encode(file_get_contents($filepath));
                }

                $args = array(
                    $path,
                    $contents,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                );

                $this->xmlrpc_request('insert', $args);
            }

        } else {

            $npath = $path['path'] . $path['name'];

            if ($is_data) {
                // 保存写入文件
                file_put_contents($npath, $filepath);
            } else {
                // 保存写入文件
                file_put_contents($npath, file_get_contents($filepath));
            }

        }

        return $path;

    }

    public function read($file)
    {

        return file_get_contents($file['tmp_name']);

    }

    /**
     * 判断是否是图片
     *
     * @param $type
     * @return bool
     */
    private function is_image($type)
    {
        if (in_array(strtolower($type), array(
            'jpg',
            'gif',
            'png'
        ))) {
            return true;
        }
        return false;
    }

    /**
     * @param $file
     * @param $path
     * @param $maxW
     * @param $maxH
     * @param $minW
     * @param $minH
     * @param $lock
     * @param $copy
     * @return bool|mixed
     */
    private function xmlrpc_upload($file, $path, $maxW, $maxH, $minW, $minH, $lock, $copy)
    {

        // 将文件以二进制读取到一个对象内
        $handle = fopen($file['tmp_name'], 'a+');

        $contents = '';

        if (flock($handle, LOCK_EX)) { // 进行排它型锁定
            while (!feof($handle)) {
                $contents .= fread($handle, 8192);
            }
        }

        flock($handle, LOCK_UN); // 释放锁定
        fclose($handle);

        // 将文件base64编码
        $contents = base64_encode($contents);

        $args = array(
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
     * 删除远程文件
     *
     * @param $path
     * @return bool|mixed
     */
    private function xmlrpc_delete($path)
    {

        $args = array(
            $path
        );

        return $this->xmlrpc_request('delete', $args);

    }

    /**
     * xmlrpc client
     *
     * @param $method
     * @param $args
     * @return bool|mixed
     */
    private function xmlrpc_request($method, $args)
    {

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
        while (!feof($fp)) {
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
     *
     * @param $path
     * @param $ruleW
     * @param $ruleH
     * @param $npath
     * @param bool $lock
     * @param int $quality
     */
    public function resize($path, $ruleW, $ruleH, $npath, $lock = true, $quality = 80)
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
            default :
                break;
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
                imagegif($imN, $npath);
                break;
            case '2' :
                imagejpeg($imN, $npath, $quality);
                break;
            case '3' :
                imagepng($imN, $npath);
                break;
            default :
                break;
        }

    }

}