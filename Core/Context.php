<?php

namespace Core;

use Exception\HTTPException;

class Context
{

    /**
     * 初始化配置
     *
     * 加载Config目录下的文件
     * 加载Common目录下的文件
     */
    public static function initialize()
    {

        $configs = glob(APP_PATH . '/Config/*.php');
        foreach ($configs as $config) {
            require_once $config;
        }

        $commons = glob(APP_PATH . '/Common/*.php');
        foreach ($commons as $common) {
            require_once $common;
        }

        $sdks = glob(CORE_PATH . '/Sdk/*.php');
        foreach ($sdks as $sdk) {
            require_once $sdk;
        }

        date_default_timezone_set(TIMEZONE);

        header('Content-Type: text/html; charset=' . CHARSET);

        if (defined('SESSION_MODE') && SESSION_MODE == 'redis') {

            $sess = Util::loadCls('Engine\\SessionEngine');

            if (isset($_COOKIE['PHPSESSID'])) {
                $sessionId = $_COOKIE['PHPSESSID'];
            } else {
                $sessionId = 'SESS_' . Util::millisecond() . '_' . uniqid();
            }

            if ($sess->start) {

                session_id($sessionId);
            } else {

                session_set_save_handler(array(
                    $sess,
                    "_session_open"
                ), array(
                    $sess,
                    "_session_close"
                ), array(
                    $sess,
                    "_session_read"
                ), array(
                    $sess,
                    "_session_write"
                ), array(
                    $sess,
                    "_session_destroy"
                ), array(
                    $sess,
                    "_session_gc"
                ));

                session_id($sessionId);

                session_cache_limiter('nocache');

                session_start();
            }
        } else {
            session_cache_expire(SESSION_EXPIRE);

            session_start();

            ini_set('session.gc_maxlifetime', SESSION_EXPIRE * 60);
        }

    }

    /**
     * HTTPDispatcher
     *
     * @return \Dispatcher\HTTPDispatcher|object
     *
     *
     */
    public static function HttpDispatcher()
    {

        return Util::loadCls('Dispatcher\HTTPDispatcher');

    }

    /**
     * ShellDispatcher
     *
     * @return \Dispatcher\ShellDispatcher|object
     */
    public static function ShellDispatcher()
    {
        return Util::loadCls('Dispatcher\ShellDispatcher');
    }

    /**
     * SocketDispatcher
     *
     * @return \Dispatcher\SocketDispatcher|object
     *
     *
     */
    public static function SocketDispatcher()
    {

        return Util::loadCls('Dispatcher\SocketDispatcher');

    }

    /**
     * 格式化异常
     *
     * @param \Exception $exception
     * @return array
     */
    public static function formatException($exception)
    {

        if (defined('EXCEPTION_LEVEL') && EXCEPTION_LEVEL === 0) {
            $exceptionHash = array(
                'message' => $exception->getMessage()
            );

            if ($exception instanceof HTTPException) {
                $data = $exception->errData();
                if ($data) {
                    $exceptionHash['data'] = $data;
                }
            }
        } else {

            $exceptionHash = array(
                'message' => $exception->getMessage()
            );

            if ($exception instanceof HTTPException) {
                $data = $exception->errData();
                if ($data) {
                    $exceptionHash['data'] = $data;
                }
            }

            $traceItems = $exception->getTrace();

            foreach ($traceItems as $traceItem) {
                $traceHash = array(
                    'file' => $traceItem['file'],
                    'line' => $traceItem['line'],
                    'function' => $traceItem['function'],
                    'args' => array()
                );

                if (!empty($traceItem['class'])) {
                    $traceHash['class'] = $traceItem['class'];
                }

                if (!empty($traceItem['type'])) {
                    $traceHash['type'] = $traceItem['type'];
                }

                if (!empty($traceItem['args'])) {
                    foreach ($traceItem['args'] as $argsItem) {
                        $traceHash['args'][] = var_export($argsItem, true);
                    }
                }

                $exceptionHash['trace'][] = $traceHash;

                if (defined('EXCEPTION_LEVEL') && EXCEPTION_LEVEL === 1) {
                    break;
                }
            }
        }

        if (defined('THROW_LOG_PATH')) {
            file_put_contents(THROW_LOG_PATH, json_encode($exceptionHash, JSON_UNESCAPED_UNICODE), FILE_APPEND);
        }

        return $exceptionHash;

    }

}