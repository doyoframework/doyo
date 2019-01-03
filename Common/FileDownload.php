<?php

namespace Common;


class FileDownload
{


    /**
     * @param string $file 要下载的文件路径
     * @param string $name 文件名称,为空则与下载的文件名称一样
     * @param bool $reload 是否开启断点续传
     * @param int $speed 下载速度
     * @return string
     */
    public static function download($file, $name = '', $reload = true, $speed = 512)
    {

        if (!file_exists($file)) {
            return false;
        }

        set_time_limit(0);

        if ($name == '') {
            $name = basename($file);
        }

        $fp = fopen($file, 'rb');
        $file_size = filesize($file);

        $ranges = self::getRange($file_size);

        header('cache-control:public');
        header('content-type:application/octet-stream');
        header('content-disposition:attachment; filename=' . $name);

        if ($reload && $ranges) {

            header('HTTP/1.1 206 Partial Content');
            header('Accept-Ranges:bytes');

            header(sprintf('content-length:%u', $ranges['end'] - $ranges['start']));
            header(sprintf('content-range:bytes %s-%s/%s', $ranges['start'], $ranges['end'], $file_size));

            // fp指针跳到断点位置
            fseek($fp, sprintf('%u', $ranges['start']));
        } else {
            header('HTTP/1.1 200 OK');
            header('content-length:' . $file_size);
        }

        while (!feof($fp)) {

            echo fread($fp, round($speed * 1024, 0));
            flush();
            ob_flush();
        }

        fclose($fp);

    }


    /**
     * 获取header range信息
     *
     * @param $file_size
     * @return array|mixed
     */
    private static function getRange($file_size)
    {

        if (isset($_SERVER['HTTP_RANGE']) && !empty($_SERVER['HTTP_RANGE'])) {

            $range = preg_replace('/[\s|,].*/', '', $_SERVER['HTTP_RANGE']);
            $range = explode('-', substr($range, 6));

            if (count($range) < 2) {
                $range[1] = $file_size;
            }
            $range = array_combine(array('start', 'end'), $range);

            if (empty($range['start'])) {
                $range['start'] = 0;
            }
            if (empty($range['end'])) {
                $range['end'] = $file_size;
            }
            return $range;
        }
        return [];
    }

}

