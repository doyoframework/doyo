<?php
namespace Sdk;

use OSS\Core\OssException;
use OSS\OssClient;

class Alioss {

    const OSS_ACCESS_ID = 'WkHReDkgl1O84kdN';

    const OSS_ACCESS_KEY = 'QZpTr3cBPVIDJ2SgKiGBUFYaFmGqOX';

    const OSS_ENDPOINT = 'oss-ap-southeast-1.aliyuncs.com';

    const OSS_TEST_BUCKET = 'cocora';

    private $ossClient;

    public function __construct() {

        try {
            $this->ossClient = new OssClient(self::OSS_ACCESS_ID, self::OSS_ACCESS_KEY, self::OSS_ENDPOINT);
        } catch ( OssException $e ) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
        }
    
    }

    public function upload($path, $file) {

        $base = $path['base'] . $path['spath'];
        $name = $base . $path['name'];
        
        $this->ossClient->createObjectDir(self::OSS_TEST_BUCKET, dirname($base));
        
        $options = array ();
        
        $options['Content-Type'] = $file['type'];
        
        $this->ossClient->uploadFile(self::OSS_TEST_BUCKET, $name, $file['tmp_name'], $options);
        
        file_put_contents('/tmp/swoole.process.log', 'upload: ' . $file['tmp_name'] . "\n\n\n", FILE_APPEND);
        
        @unlink($file['tmp_name']);
    
    }

    public function copy($path, $type, $file) {

        $base = $path['base'] . $path['spath'];
        $name = $base . $path['name'];
        
        $this->ossClient->createObjectDir(self::OSS_TEST_BUCKET, dirname($base));
        
        $options = array ();
        
        $options['Content-Type'] = $type;
        
        $this->ossClient->uploadFile(self::OSS_TEST_BUCKET, $name, $file, $options);
        
        file_put_contents('/tmp/swoole.process.log', 'copy: ' . $file . "\n\n\n", FILE_APPEND);
    
    }

    public function delete($path) {

        $base = $path['base'] . $path['spath'];
        $name = $base . $path['name'];
        
        $this->ossClient->deleteObject(self::OSS_TEST_BUCKET, $name);
        
        file_put_contents('/tmp/swoole.process.log', 'delete: ' . $name . "\n\n\n", FILE_APPEND);
    
    }

}

?>