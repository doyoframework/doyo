<?php
namespace Sdk;

use OSS\Core\OssException;
use OSS\OssClient;

class Alioss {
	
    private $ossClient;

    public function __construct($accessId, $accessKey, $endpoint) {

        try {
            $this->ossClient = new OssClient($accessId, $accessKey, $endpoint);
        } catch ( OssException $e ) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
        }
    
    }

    public function upload($bucket, $path, $file) {

        $base = $path['base'] . $path['spath'];
        $name = $base . $path['name'];
        
        $this->ossClient->createObjectDir($bucket, dirname($base));
        
        $options = array ();
        
        $options['Content-Type'] = $file['type'];
        
        $this->ossClient->uploadFile($bucket, $name, $file['tmp_name'], $options);
        
        file_put_contents('/tmp/swoole.process.log', 'upload: ' . $file['tmp_name'] . "\n\n\n", FILE_APPEND);
        
        @unlink($file['tmp_name']);
    
    }

    public function copy($bucket, $path, $type, $file) {

        $base = $path['base'] . $path['spath'];
        $name = $base . $path['name'];
        
        $this->ossClient->createObjectDir($bucket, dirname($base));
        
        $options = array ();
        
        $options['Content-Type'] = $type;
        
        $this->ossClient->uploadFile($bucket, $name, $file, $options);
        
        file_put_contents('/tmp/swoole.process.log', 'copy: ' . $file . "\n\n\n", FILE_APPEND);
    
    }

    public function delete($bucket, $path) {

        $base = $path['base'] . $path['spath'];
        $name = $base . $path['name'];
        
        $this->ossClient->deleteObject($bucket, $name);
        
        file_put_contents('/tmp/swoole.process.log', 'delete: ' . $name . "\n\n\n", FILE_APPEND);
    
    }

}

?>