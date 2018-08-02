<?php

namespace Sdk;

use Core\Util;
use OSS\Core\OssException;
use OSS\OssClient;

class Alioss
{

    /**
     * @var OssClient
     */
    private $ossClient;

    public function __construct()
    {

        try {
            $this->ossClient = new OssClient(OSS_KEY, OSS_SECRET, OSS_END_POINT);
        } catch (OssException $e) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
        }

    }

    public function getObjectMeta($bucket, $path)
    {
        if ($path[0] == '/') {
            $path = substr($path, 1);
        }

        return $this->ossClient->getObjectMeta($bucket, $path);
    }

    public function listObjects($bucket)
    {
        return $this->ossClient->listObjects($bucket);
    }

    /**
     * @param $bucket
     * @param $path
     * @param $file
     * @throws OssException
     */
    public function upload($bucket, $path, $file)
    {

        if (is_array($path)) {
            $name = $path['base'] . $path['spath'] . $path['name'];
        } else {
            $name = $path;
        }

        if ($name[0] == '/') {
            $name = substr($name, 1);
        }

        $this->ossClient->createObjectDir($bucket, dirname($name));

        $options = array();

        $options['Content-Type'] = $file['type'];

        $this->ossClient->uploadFile($bucket, $name, $file['tmp_name'], $options);

        @unlink($file['tmp_name']);

    }

    /**
     * @param $bucket
     * @param $path
     */
    public function delete($bucket, $path)
    {

        if (is_array($path)) {
            $name = $path['base'] . $path['spath'] . $path['name'];
        } else {
            $name = $path;
        }

        if ($name[0] == '/') {
            $name = substr($name, 1);
        }

        $this->ossClient->deleteObject($bucket, $name);

    }

}
