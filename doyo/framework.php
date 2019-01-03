<?php

use Core\Context;

/**
 * 自动加载Class
 *
 * @param $class
 */
function sys_autoload($class)
{
    $basePath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $classFile = APP_PATH . DIRECTORY_SEPARATOR . $basePath;

    if (!file_exists($classFile)) {
        $classFile = __DIR__ . DIRECTORY_SEPARATOR . $basePath;
    }

    if (!file_exists($classFile)) {
        $classFile = APP_PATH . '/Plugins/' . $basePath;
    }

    if (file_exists($classFile)) {
        require_once $classFile;
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

    $errCode = -1;

    if ($exception instanceof \Exception\HTTPException) {
        $errCode = $exception->errCode();
    }

    Context::HttpDispatcher()->display($exceptionHash, $errCode);
});

/**
 * 初始化框架
 */
Context::initialize();
