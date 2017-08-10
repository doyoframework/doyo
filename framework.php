<?php
use Core\Context;
session_start();

// 必须需要xmlrpc 模块
if (!function_exists('xmlrpc_encode_request'))
    exit('need Modules xmlrpc');

/**
 * 自动加载Class
 */
function sys_autoload($class) {

    $basePath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $classFile = APP_PATH . DIRECTORY_SEPARATOR . $basePath;
    
    if (!file_exists($classFile)) {
        $classFile = __DIR__ . DIRECTORY_SEPARATOR . $basePath;
    }
    
    if (!file_exists($classFile)) {
        $classFile = __DIR__ . DIRECTORY_SEPARATOR . 'Engine/Smarty/libs/plugins/' . $basePath;
    }
    
    if (!file_exists($classFile)) {
        $classFile = __DIR__ . DIRECTORY_SEPARATOR . 'Engine/Smarty/libs/sysplugins/' . $basePath;
    }
    
    if (!file_exists($classFile)) {
        $classFile = __DIR__ . DIRECTORY_SEPARATOR . 'Sdk/' . $basePath;
    }
    
    if (file_exists($classFile)) {
        require_once ($classFile);
    }

}
spl_autoload_register('sys_autoload');

/**
 * 框架目录
 */
define('CORE_PATH', __DIR__);

/**
 * 设置异常输出的处理方法
 */
set_exception_handler(function ($exception) {
    $exceptionHash = Context::formatException($exception);
    
    Context::dispatcher()->display($exceptionHash, 1);
});

/**
 * 初始化框架
 */
Context::initialize();
?>