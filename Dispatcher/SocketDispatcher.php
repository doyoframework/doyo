<?php
namespace Dispatcher;

use Core\Util;

class SocketDispatcher {

    /**
     * 分发器
     */
    public function dispatch(&$svr, $fd, $data, $request = null) {

        echo "dispatch fd {$fd} ";
        
        $data = json_decode($data, true);
        
        if (is_array($data) && isset($data['op']) && isset($GLOBALS['ROUTE'][$data['op']])) {
            
            echo "op {$data['op']}\n";
            
            $route = explode('.', $GLOBALS['ROUTE'][$data['op']]);
            
            $ctrl = Util::loadCtrl($route[0]);
            
            if (isset($data['param'])) {
                $ctrl->setParams($data['param']);
            }
            
            if ($request != null && !isset(Util::$connections[$fd])) {
                Util::$connections[$fd] = $request;
            }
            
            $ctrl->fd = $fd;
            
            $ctrl->svr = $svr;
            
            $method = $route[1];
            
            $ret = $ctrl->$method();
            
            if (is_array($ret)) {
                $ctrl->send($data['op'], $ret);
            }
        } else {
            echo "\n";
        }
    
    }

}
?>