<?php

namespace Dispatcher;

use Core\Util;

class SocketDispatcher
{

    /**
     * 分发器
     *
     * @param $svr
     * @param $fd
     * @param $op
     * @param array $param
     * @throws \Exception\HTTPException
     */
    public function dispatch(&$svr, $fd, $op, $param = array())
    {

        echo "dispatch fd {$fd} ";

        if (isset($GLOBALS['ROUTE'][$op])) {

            echo "op {$op}\n";

            $route = explode('.', $GLOBALS['ROUTE'][$op]);

            $ctrl = Util::loadCtrl($route[0]);

            if (!empty($param)) {
                $ctrl->setParams($param);
            }

            $ctrl->fd = $fd;

            $ctrl->svr = $svr;

            $method = $route[1];

            $ret = $ctrl->$method();

            if (is_array($ret)) {
                $ctrl->send($op, $ret);
            }
        } else {
            echo "\n";
        }

    }

}