<?php
namespace Core;

use Exception\HTTPException;

class Context {

    /**
     * 初始化配置
     *
     * 加载Config目录下的文件
     * 加载Common目录下的文件
     */
    public static function initialize() {

        $configs = glob(APP_PATH . '/Config/*.php');
        foreach ( $configs as $config ) {
            require_once $config;
        }
        
        $commons = glob(APP_PATH . '/Common/*.php');
        foreach ( $commons as $common ) {
            require_once $common;
        }
        
        $sdks = glob(CORE_PATH . '/Sdk/*.php');
        foreach ( $sdks as $sdk ) {
            require_once $sdk;
        }
        
        date_default_timezone_set(TIMEZONE);
        
        header("Content-Type: text/html; charset=" . CHARSET);
    
    }

    /**
     * HTTPDispatcher
     *
     * @return \Dispatcher\HTTPDispatcher
     *
     *
     */
    public static function dispatcher() {

        return Util::loadCls('Dispatcher\HTTPDispatcher');
    
    }

    /**
     * 格式化异常
     *
     * @param \Exception $exception            
     * @return array
     */
    public static function formatException($exception) {

        if (EXCEPTION_LEVEL === 0) {
            $exceptionHash = array (
                'message' => $exception->getMessage() 
            );
            
            if ($exception instanceof HTTPException) {
                $data = $exception->getData();
                if ($data) {
                    $exceptionHash['data'] = $data;
                }
            }
        } else {
            
            $exceptionHash = array (
                'message' => $exception->getMessage() 
            );
            
            if ($exception instanceof HTTPException) {
                $data = $exception->getData();
                if ($data) {
                    $exceptionHash['data'] = $data;
                }
            }
            
            $traceItems = $exception->getTrace();
            
            foreach ( $traceItems as $traceItem ) {
                $traceHash = array (
                    'file' => $traceItem['file'], 
                    'line' => $traceItem['line'], 
                    'function' => $traceItem['function'], 
                    'args' => array () 
                );
                
                if (!empty($traceItem['class'])) {
                    $traceHash['class'] = $traceItem['class'];
                }
                
                if (!empty($traceItem['type'])) {
                    $traceHash['type'] = $traceItem['type'];
                }
                
                if (!empty($traceItem['args'])) {
                    foreach ( $traceItem['args'] as $argsItem ) {
                        $traceHash['args'][] = var_export($argsItem, true);
                    }
                }
                
                $exceptionHash['trace'][] = $traceHash;
                
                if (EXCEPTION_LEVEL === 1) {
                    break;
                }
            }
        }
        
        return $exceptionHash;
    
    }

}