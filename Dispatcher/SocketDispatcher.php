<?php
namespace Dispatcher;

use Core\Util;

class SocketDispatcher {

    /**
     * 分发器
     */
    public function dispatch($svr, $frame) {

        $data = json_decode($frame->data, true);
        
        if (is_array($data) && isset($data['op']) && isset($GLOBALS['ROUTE'][$data['op']])) {
            
            $route = explode('.', $GLOBALS['ROUTE'][$data['op']]);
            
            $ctrl = Util::loadCls("Ctrl\\{$route[0]}");
            
            if (isset($data['param'])) {
                $ctrl->setParams($data['param']);
            }
            
            $ctrl->fd = $frame->fd;
            
            $ctrl->svr = $svr;
            
            $method = $route[1];
            
            $ret = $ctrl->$method();
            
            if (is_array($ret)) {
                $ctrl->send($ctrl->fd, $data['op'], $ret);
            }
        }
    
    }

}
?>