<?php
namespace Dispatcher;

use Core\Util;
use Core\BaseCtrl;

class SocketDispatcher {

    /**
     * 分发器
     */
    public function dispatch($svr, $fd, $data) {

        $data = json_decode($data, true);
        
        if (is_array($data) && isset($data['op']) && isset($GLOBALS['ROUTE'][$data['op']])) {
            
            $route = explode('.', $GLOBALS['ROUTE'][$data['op']]);
            
            $ctrl = $this->loadCtrl($route[0]);
            
            if (isset($data['param'])) {
                $ctrl->setParams($data['param']);
            }
            
            $ctrl->fd = $fd;
            
            $ctrl->svr = $svr;
            
            $method = $route[1];
            
            $ret = $ctrl->$method();
            
            if (is_array($ret)) {
                $ctrl->send($data['op'], $ret);
            }
        }
    
    }

    /**
     *
     * @return BaseCtrl
     */
    public function loadCtrl($clsName) {

        return Util::loadCls("Ctrl\\{$clsName}");
    
    }

}
?>