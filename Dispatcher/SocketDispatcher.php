<?php

namespace Dispatcher;

use Core\Util;

class SocketDispatcher
{

    private static $svr;

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

        if (isset($GLOBALS['ROUTE'][$op])) {

            $route = explode('.', $GLOBALS['ROUTE'][$op]);

            $ctrl = Util::loadCtrl($route[0]);

            if (!empty($param)) {
                $ctrl->setParams($param);
            }

            $ctrl->fd = $fd;

            self::$svr = $svr;

            $ctrl->svr = $svr;

            $method = $route[1];

            $ret = $ctrl->$method();

            if (is_array($ret)) {
                $ctrl->send($op, $ret);
            }

        } else {
            echo "error {$op}\n";
        }

    }

    public static function send($op, $data, $fd)
    {

        $code = 0;

        if ($op < 0) {
            $code = $op;
        }

        $array = array(
            'code' => $code,
            'op' => intval($op),
            'version' => VERSION,
            'unixtime' => Util::millisecond(),
            'data' => $data
        );

        $data = json_encode($array, JSON_UNESCAPED_UNICODE);

        return self::$svr->push($fd, $data);
    }
}