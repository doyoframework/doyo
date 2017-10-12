<?php
namespace Engine;

use Core\Util;

class SessionEngine {

    /**
     * 状态
     */
    public $start = false;

    /**
     * 操作句柄
     */
    private $redis;

    public function __construct() {

        $this->redis = Util::loadRedis('session');
    
    }
    
    // 打开Session
    public function _session_open($save_path, $session_id) {

        $this->start = true;
        return true;
    
    }
    
    // 关闭Session
    public function _session_close() {

        $this->start = false;
        return true;
    
    }
    
    // 读取Session
    public function _session_read($key) {
        
        // 设置过期时间
        $this->redis->expire($key, SESSION_EXPIRE);
        
        return $this->redis->get($key);
    
    }
    
    // 写入Session
    public function _session_write($key, $val) {

        $this->redis->set($key, $val, SESSION_EXPIRE);
    
    }
    
    // 删除指定Session
    public function _session_destroy($key) {

        $this->redis->delete($key);
    
    }
    
    // 删除过期Session
    public function _session_gc($expire) {

        return false;
    
    }

}
?>