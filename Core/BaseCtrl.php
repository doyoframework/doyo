<?php

namespace Core;

use Common\Model;
use Dispatcher\SocketDispatcher;

class BaseCtrl
{

    /**
     * 取得一个Smarty对象
     *
     * @var \Smarty
     */
    private $view;

    /**
     * 保存param数组
     *
     * @var array
     */
    private $param = array();

    /**
     * 是否是表单提交
     *
     * @var boolean
     */
    public $isPost = false;

    /**
     * 构造函数
     */
    public final function __construct()
    {

    }

    /**
     * 继承的子类要用到的构造函数
     */
    public function __initialize()
    {

    }

    /**
     * 初始化Smarty配置
     *
     * @param null $template
     * @param null $compile
     */
    public final function initSmarty($template = null, $compile = null)
    {

        require_once(CORE_PATH . '/Engine/Smarty/libs/Smarty.class.php');

        $this->view = new \Smarty();

        $this->view->debugging = SMARTY_DEBUG;

        if ($template != null) {
            $this->view->setTemplateDir(APP_PATH . '/' . $template);
        } else {
            $this->view->setTemplateDir(APP_PATH . '/' . SMARTY_TEMPLATE_DIR);
        }

        if ($compile != null) {
            $this->view->setCompileDir(APP_PATH . '/' . $compile);
        } else {
            $this->view->setCompileDir(APP_PATH . '/' . SMARTY_COMPILE_DIR);
        }

        $this->view->compile_check = SMARTY_COMPILE_CHECK;

        $this->view->left_delimiter = SMARTY_LEFT_DELIMITER;

        $this->view->right_delimiter = SMARTY_RIGHT_DELIMITER;

        $this->view->joined_config_dir = APP_PATH . '/' . WEBROOT . '/';

        if (defined('SMARTY_CONFIG_LOAD')) {
            $this->view->configLoad(SMARTY_CONFIG_LOAD);
        }

    }

    /**
     * 设置Smarty的模板目录和编译目录
     *
     * @param $template
     * @param $compile
     */
    public final function setSmarty($template, $compile)
    {

        $this->view->setTemplateDir(APP_PATH . '/' . $template);
        $this->view->setCompileDir(APP_PATH . '/' . $compile);

    }

    /**
     * 设置参数
     *
     * @param $param
     * @throws \Exception\HTTPException
     */
    public final function setParams($param)
    {

        if (is_array($param)) {
            $this->param = $param;
        } else if (is_string($param)) {

            $this->param = array();

            $ary = explode('&', $param);

            foreach ($ary as $val) {
                $kv = explode('=', $val);
                $this->param[trim($kv[0])] = trim($kv[1]);
            }
        } else {
            throw Util::HTTPException('set params error');
        }

    }

    /**
     * 获得参数
     *
     * @return array
     */
    public final function getParams()
    {

        return $this->param;

    }

    /**
     * 查询传递的Integer参数
     *
     * @param $key
     * @param bool $notEmpty
     * @param bool $abs
     * @param string $msg
     * @return bool|float|int
     * @throws \Exception\HTTPException
     */
    protected final function getInteger($key, $notEmpty = false, $abs = false, $msg = '')
    {

        if (is_numeric($key)) {
            $key--;
        }

        $val = isset($this->param[$key]) ? $this->param[$key] : false;

        if ($notEmpty && $val === false) {
            $msg = $msg != '' ? $msg : $key . ' empty';
            throw Util::HTTPException($msg);
        }

        if ($val === false) {
            $val = 0;
        }

        if ($val == 0 && $msg != '') {
            throw Util::HTTPException($msg);
        }

        if ($abs) {
            $val = abs($val);
        }

        return $val;

    }

    /**
     * @param $key
     * @param bool $notEmpty
     * @return bool
     * @throws \Exception\HTTPException
     */
    protected final function getBoolean($key, $notEmpty = false)
    {
        if (is_numeric($key)) {
            $key--;
        }

        if (!isset($this->param[$key]) && $notEmpty) {
            throw Util::HTTPException($key . ' empty');
        }

        if (isset($this->param[$key])) {
            if (is_bool($this->param[$key])) {

                return $this->param[$key];

            } else if (!is_numeric($this->param[$key])) {

                if (strtolower($this->param[$key]) == 'true') {
                    return true;
                }

            } else {

                if ($this->param[$key] > 0) {
                    return true;
                }

            }
        }

        return false;
    }

    /**
     * 查询传递的参数
     *
     * @param $key
     * @return bool
     */
    protected final function hasParam($key)
    {

        if (is_numeric($key)) {
            $key--;
        }

        if (isset($this->param[$key])) {
            return true;
        }

        return false;

    }


    protected final function hasArray($key)
    {
        if (is_numeric($key)) {
            $key--;
        }

        if (isset($this->param[$key]) && is_array($this->param[$key])) {
            return true;
        }

        return false;
    }

    /**
     * 查询传递的String参数
     *
     * @param $key
     * @param bool $notEmpty
     * @param string $msg
     * @return string
     * @throws \Exception\HTTPException
     */
    protected final function getString($key, $notEmpty = false, $msg = '')
    {
        if (is_numeric($key)) {
            $key--;
        } else {
            if (isset($this->param[$key]) && !is_string($this->param[$key])) {
                throw Util::HTTPException($key, -1, '参数类型不正确');
            }
        }

        $val = isset($this->param[$key]) ? trim($this->param[$key]) : false;

        if ($notEmpty && $val === false) {
            $msg = $msg != '' ? $msg : $key . ' empty';
            throw Util::HTTPException($msg);
        }

        if ($val == '' && $msg != '') {
            throw Util::HTTPException($msg);
        }

        if (Util::is_json($val)) {
            return strval($val);
        } else {
            return addslashes(strval($val));
        }
    }

    /**
     * 查询传递的Integer数组参数
     *
     * @param $key
     * @param bool $notEmpty
     * @param bool $abs
     * @return array|mixed|string
     * @throws \Exception\HTTPException
     */
    protected final function getIntegers($key, $notEmpty = false, $abs = false)
    {

        if (is_numeric($key)) {
            $key--;
        }

        $val = isset($this->param[$key]) ? $this->param[$key] : false;

        if ($notEmpty && $val === false) {
            throw Util::HTTPException($key . ' empty');
        }

        if ($val) {

            $_val = $val;

            if (!is_array($_val)) {

                $_val = json_decode($val);

                if (!is_array($_val)) {
                    $_val = explode(',', $val);
                }

                if (!is_array($_val) || count($_val) == 1) {
                    $_val = explode('_', $val);
                }

                if (!is_array($_val) || count($_val) == 1) {
                    $_val = explode('-', $val);
                }
            }

            if (!is_array($_val)) {
                throw Util::HTTPException($key . ' not array');
            }

            $val = array_map('floatval', $_val);

            if ($abs) {
                $val = array_map('abs', $val);
            }

        } else {
            $val = [];
        }

        return $val;

    }

    /**
     * 查询传递的String数组参数
     *
     * @param $key
     * @param bool $notEmpty
     * @return array|mixed|string
     * @throws \Exception\HTTPException
     */
    protected final function getStrings($key, $notEmpty = false, $msg = '')
    {

        if (is_numeric($key)) {
            $key--;
        }

        $val = isset($this->param[$key]) ? $this->param[$key] : false;

        if ($notEmpty && $val === false) {
            if ($msg) {
                throw Util::HTTPException($msg);
            } else {
                throw Util::HTTPException($key . ' empty');
            }
        }

        if ($val) {

            $_val = $val;

            if (!is_array($_val)) {

                $_val = json_decode($val);

                if (!is_array($_val)) {
                    $_val = explode(',', $val);
                }

                if (!is_array($_val) || count($_val) == 1) {
                    $_val = explode('_', $val);
                }

                if (!is_array($_val) || count($_val) == 1) {
                    $_val = explode('-', $val);
                }

            }

            if (!is_array($_val)) {
                throw Util::HTTPException($key . ' not array');
            }

            $val = $_val;

            $val = array_map('strval', $val);

            foreach ($val as $k => $v) {
                if (!Util::is_json($v)) {
                    $val[$k] = addslashes($v);
                } else {
                    $val[$k] = $v;
                }
            }

        } else {
            $val = [];
        }

        return $val;

    }

    /**
     * 设置_SESSION内的值
     *
     * @param $key
     * @param $val
     */
    protected final function setSession($key, $val)
    {

        Session::set($key, $val);

    }

    /**
     * 查询_SESSION内的值
     *
     * @param $key
     * @param bool $notEmpty
     * @return bool
     * @throws \Exception\HTTPException
     */
    protected final function getSession($key, $notEmpty = false)
    {

        if (Session::exists($key)) {
            return Session::get($key);
        }

        if ($notEmpty) {
            throw Util::HTTPException($key . ' error.', -100);
        }

        return false;

    }

    /**
     * 删除_SESSION内的值
     *
     * @param $key
     */
    protected final function delSession($key)
    {

        Session::del($key);

    }

    /**
     * 清空_SESSION内的值
     */
    protected final function destroySession()
    {

        Session::destroy();

    }

    /**
     * 设置$_COOKIE内的值
     *
     * @param $key
     * @param $val
     * @param int $expire
     * @param string $path
     * @param string $domain
     */
    protected final function setCookie($key, $val, $expire = 86400, $path = '/', $domain = COOKIE_DOMAIN)
    {

        $expire += time();

        setcookie($key, $val, $expire, $path, $domain);

    }

    /**
     * 查询$_COOKIE内的值
     *
     * @param $key
     * @param bool $notEmpty
     * @return null
     * @throws \Exception\HTTPException
     */
    protected final function getCookie($key, $notEmpty = false)
    {

        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }

        if ($notEmpty) {
            throw Util::HTTPException($key . 'error.');
        }

        return null;

    }

    /**
     * Curl POST提交
     *
     * @param $url
     * @param $param
     * @return bool|mixed
     */
    public final function post($url, $param)
    {

        return Util::curl_request($url, 'POST', $param);

    }

    /**
     * Curl GET提交
     *
     * @param $url
     * @return bool|mixed
     */
    public final function get($url)
    {

        return Util::curl_request($url, 'GET');

    }

    /**
     * 向模板传递变量
     *
     * @param $tpl_var
     * @param $value
     * @param bool $nocache
     */
    public final function assign($tpl_var, $value, $nocache = false)
    {

        $this->view->assignGlobal($tpl_var, $value, $nocache);

    }

    /**
     * 渲染模板
     *
     * @param $template
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     */
    public final function display($template, $cache_id = null, $compile_id = null, $parent = null)
    {

        if ($this->view->templateExists($template)) {
            $this->view->display($template, $cache_id, $compile_id, $parent);
        } else {
            exit('404');
        }

    }

    /**
     * 返回渲染的结果
     *
     * @param $template
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     * @param bool $display
     * @param bool $merge_tpl_vars
     * @param bool $no_output_filter
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    public final function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {

        if ($this->view->templateExists($template)) {
            return $this->view->fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
        }

        return '';

    }

    /**
     * 重定向
     *
     * @param $url
     */
    public final function redirect($url)
    {

        header('location:' . $url);
        exit();

    }

    /**
     * 以JSON格式返回
     */
    public final function display_json()
    {

        header('Content-Type: text/json; charset=' . CHARSET);
        Context::HttpDispatcher()->model = 'JSON';

    }

    /**
     * 以HTML格式返回
     */
    public final function display_html()
    {

        header('Content-Type: text/html; charset=' . CHARSET);
        Context::HttpDispatcher()->model = 'HTML';

    }

    /**
     * 获取客户端IP
     *
     * @param bool $long
     * @return array|false|int|string
     */


    public final function ipaddr($long = true)
    {
        if ($this->fd > 0 && $this->svr != null) {

            $info = $this->svr->connection_info($this->fd);

            if (isset($info['remote_ip'])) {
                if ($long) {
                    return ip2long($info['remote_ip']);
                }
                return $info['remote_ip'];
            } else {
                if ($long) {
                    return 0;
                } else {
                    return '0.0.0.0';
                }
            }

        }

        return Util::ipaddr($long);
    }

    /**
     * 长连接的文件描述
     * @var int
     */
    public $fd = -1;

    /**
     * 长连接的文件引用
     *
     * @var \swoole_websocket_server
     *
     */
    public $svr = null;

    /**
     * 长连接发送数据
     *
     * @param $op
     * @param array $data
     * @param int $fd
     * @return bool
     */
    public final function send($op, $data = array(), $fd = -1)
    {

        if ($fd === -1) {
            $fd = $this->fd;
        }

        if (!is_numeric($fd)) {
            echo " fd not numeric. \n";
            return false;
        }

        if (!$this->svr->exist($fd)) {
            echo " fd not connect: {$fd} \n";
            return false;
        }

        return SocketDispatcher::send($op, $data, $fd);

    }

    /**
     * 长连接返回数据
     *
     * @param $data
     * @param int $fd
     * @param int $op
     */
    public final function error($data, $fd = -1, $op = -1)
    {
        $this->send($op, array(
            'message' => $data
        ), $fd);
    }


    /**
     * 绑定UID
     *
     * @param $pid
     * @throws \Exception\HTTPException
     */
    public final function connection_bind($pid)
    {
        if ($this->svr->exist($this->fd)) {
            $this->svr->bind($this->fd, $pid);
        } else {
            throw Util::HTTPException('fd not connect');
        }
    }

    /**
     * 是否在线
     *
     * @param $fd
     * @return mixed
     */
    public final function is_online($fd)
    {
        return $this->svr->exist($fd);
    }

    /**
     * file description info
     *
     * @param int $fd
     * @return array
     */
    public final function connection_info($fd = -1)
    {
        $fd = intval($fd);

        if ($fd === -1) {
            $fd = $this->fd;
        }

        if ($this->svr->exist($fd)) {
            return $this->svr->connection_info($fd);
        }

        return array();
    }

    /**
     * 关闭一个文件描述
     *
     * @param int $fd
     */
    public final function close($fd = -1)
    {
        $fd = intval($fd);

        if ($fd === -1) {
            $fd = $this->fd;
        }

        if ($this->svr->exist($fd)) {
            $this->svr->close($fd);
        }

    }

}