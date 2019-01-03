<?php
/**
 * 设置时区
 *
 * @var string
 */
define('TIMEZONE', 'Asia/Shanghai');

/**
 * 字符集设置
 */
define('CHARSET', 'utf-8');

/**
 * 版本号
 */
define('VERSION', time());


/**
 * 签名密钥
 */
define('SERVER_KEY', 'dongyong');

/**
 * 网站目录
 *
 * @var string
 *
 *
 */
define('WEBROOT', 'webroot');

/**
 * 用于网站混淆加密的地方
 */
define('WEB_MIX', 'dongyong');

/**
 * COOKIE作用域
 */
define('COOKIE_DOMAIN', '.oegame.com');

/**
 * MySQL SQL Log Path
 */
define('SQL_LOG_PATH', '/tmp/sql.log');

define('THROW_LOG_PATH', '/tmp/throw.log');

define('SESSION_MODE', 'redis');

define('SESSION_EXPIRE', 60 * 60 * 24);


/**
 * MySQL SQL Log Path
 */
define('EXCEPTION_LEVEL', 0);

/**
 * MySQL SQL Log Path
 */
define('REWRITE', '/');
