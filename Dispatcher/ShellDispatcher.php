<?php
namespace Dispatcher;

use Core\Util;

class ShellDispatcher {

    /**
     * 分发器
     */
    public function dispatch() {

        if ((array_key_exists("argv", $_SERVER) && count($_SERVER['argv']) > 1)) {
            $action = $_SERVER['argv'][1];
            
            if (preg_match('/^([a-z_]+)\.([a-z_]+)$/i', $action, $items)) {
                $ctrlName = $items[1];
                $methodName = $items[2];
            } else {
                echo 'Error Commend.';
                echo "\n";
                exit();
            }
            
            $className = "Ctrl\\" . $ctrlName;
            
            $ctrl = Util::loadCls($className);
            
            $params = array ();
            if (count($_SERVER['argv']) >= 3) {
                $params = array_slice($_SERVER['argv'], 2);
            }
            
            call_user_method_array($methodName, $ctrl, $params);
        }
    
    }

}
?>