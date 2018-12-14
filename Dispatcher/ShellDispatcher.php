<?php

namespace Dispatcher;

use Core\Util;

class ShellDispatcher
{

    /**
     * 分发器
     *
     * @param string $_ctrlPath
     * @throws \Exception\HTTPException
     */
    public function dispatch($_ctrlPath = 'Ctrl')
    {

        if ((isset($_SERVER["argv"]) && count($_SERVER['argv']) > 1)) {
            $action = $_SERVER['argv'][1];

            if (preg_match('/^([a-z_]+)\.([a-z_]+)$/i', $action, $items)) {
                $ctrlName = $_ctrlPath . '\\' . $items[1];
                $methodName = $items[2];
            } else {
                echo 'Error Commend.';
                echo "\n";
                exit();
            }

            $ctrl = Util::loadCtrl($ctrlName);

            $params = array();
            if (count($_SERVER['argv']) >= 3) {
                $params = array_slice($_SERVER['argv'], 2);
            }

            $ctrl->setParams($params);

            call_user_func_array([$ctrl, $methodName], $params);
        }

    }

}