<?php
namespace Core;

use Core\Util;

class BaseCtrl {

    /**
     * 取得一个Smarty对象
     *
     * @var \Smarty
     */
    private $view;

    /**
     * 保存param数组
     *
     * @var Array
     */
    private $param = array ();

    /**
     * 是否是表单提交
     *
     * @var boole
     */
    public $isPost = false;

    /**
     * 构造函数
     */
    public final function __construct() {

    }

    /**
     * 继承的子类要用到的构造函数
     */
    public function __initialize() {

    }

    /**
     * 初始化Smarty配置
     */
    public final function initSmarty($template = null, $compile = null) {

        require_once (CORE_PATH . '/Engine/Smarty/libs/Smarty.class.php');
        
        $this->view = new \Smarty();
        
        $this->view->debugging = SMARTY_DEBUG;
        
        if ($template != null) {
            $this->view->template_dir = APP_PATH . '/' . $template;
        } else {
            $this->view->template_dir = APP_PATH . '/' . SMARTY_TEMPLATE_DIR;
        }
        
        if ($compile != null) {
            $this->view->compile_dir = APP_PATH . '/' . $compile;
        } else {
            $this->view->compile_dir = APP_PATH . '/' . SMARTY_COMPILE_DIR;
        }
        
        $this->view->compile_check = SMARTY_COMPILE_CHECK;
        
        $this->view->left_delimiter = SMARTY_LEFT_DELIMITER;
        
        $this->view->right_delimiter = SMARTY_RIGHT_DELIMITER;
        
        $this->view->config_dir = APP_PATH . '/' . WEBROOT . '/';
        
        if (defined('SMARTY_CONFIG_LOAD') && SMARTY_CONFIG_LOAD) {
            $this->view->configLoad(SMARTY_CONFIG_LOAD);
        }
    
    }

    /**
     * 设置Smarty的模板目录和编译目录
     */
    public final function setSmarty($template, $compile) {

        $this->view->template_dir = APP_PATH . '/' . $template;
        $this->view->compile_dir = APP_PATH . '/' . $compile;
    
    }

    /**
     * 初始化upload配置
     *
     * @return \Engine\FileEngine
     *
     *
     */
    public final function initFiles() {

        return Util::loadCls('Engine\FileEngine');
    
    }

    /**
     * 设置参数
     *
     * @return void
     */
    public final function setParams($param) {

        if (is_array($param)) {
            $this->param = $param;
        } else if (is_string($param)) {
            
            $this->param = array ();
            
            $ary = explode('&', $param);
            
            foreach ( $ary as $val ) {
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
    public final function getParams() {

        return $this->param;
    
    }

    /**
     * 查询传递的Integer参数
     *
     * @return int
     */
    protected final function getInteger($key, $notEmpty = false, $abs = false) {

        if (is_numeric($key)) {
            $key--;
        }
        
        $val = array_key_exists($key, $this->param) ? floatval($this->param[$key]) : '';
        
        if ($notEmpty && $val === '') {
            throw Util::HTTPException($key . ' empty');
        }
        
        if ($abs) {
            $val = abs($val);
        }
        
        return $val;
    
    }

    /**
     * 查询传递的String参数
     *
     * @param string $key            
     * @param boolean $notEmpty            
     *
     * @return boolean
     */
    protected final function hasString($key, $notEmpty = false) {

        if (is_numeric($key)) {
            $key--;
        }
        
        $val = '';
        
        if (isset($this->param[$key]) && is_string($this->param[$key])) {
            $val = array_key_exists($key, $this->param) ? trim($this->param[$key]) : '';
        }
        
        if ($notEmpty && $val == '') {
            return false;
        }
        
        return !empty($val);
    
    }

    /**
     * 查询传递的String参数
     *
     * @param string $key            
     * @param boolean $notEmpty            
     *
     * @return string
     */
    protected final function getString($key, $notEmpty = false) {

        if (is_numeric($key)) {
            $key--;
        }
        
        $val = '';
        
        if (isset($this->param[$key]) && is_string($this->param[$key])) {
            $val = array_key_exists($key, $this->param) ? trim($this->param[$key]) : '';
        }
        
        if ($notEmpty && $val == '') {
            throw Util::HTTPException($key . ' empty');
        }
        
        return strval($val);
    
    }

    /**
     * 查询传递的Integer数组参数
     *
     * @return int[]
     */
    protected final function getIntegers($key, $notEmpty = false, $abs = false) {

        if (is_numeric($key)) {
            $key--;
        }
        
        $val = array_key_exists($key, $this->param) ? $this->param[$key] : '';
        
        if ($notEmpty && $val == '') {
            throw Util::HTTPException($key . ' empty');
        }
        
        if ($val != '') {
            
            $_val = $val;
            
            if (!is_array($_val)) {
                
                $_val = json_decode($val);
                
                if (!is_array($_val)) {
                    $_val = explode(',', $val);
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
        }
        
        return $val;
    
    }

    /**
     * 查询传递的String数组参数
     *
     * @param string $key            
     * @param boolean $notEmpty            
     *
     * @return string[]
     */
    protected final function getStrings($key, $notEmpty = false) {

        if (is_numeric($key)) {
            $key--;
        }
        
        $val = array_key_exists($key, $this->param) ? $this->param[$key] : '';
        
        if ($notEmpty && $val == '') {
            throw Util::HTTPException($key . ' empty');
        }
        
        if ($val != '') {
            
            $_val = $val;
            
            if (!is_array($_val)) {
                
                $_val = json_decode($val);
                
                if (!is_array($_val)) {
                    $_val = explode(',', $val);
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
        }
        
        return $val;
    
    }

    /**
     * 设置$_SESSION内的值
     *
     * @param string $key            
     * @param object $val            
     *
     * @return void
     */
    protected final function setSession($key, $val) {

        $_SESSION[$key] = $val;
    
    }

    /**
     * 查询$_SESSION内的值
     *
     * @param string $key            
     *
     * @return object
     */
    protected final function getSession($key) {

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return false;
    
    }

    /**
     * 删除$_SESSION内的值
     *
     * @param string $key            
     *
     * @return void
     */
    protected final function delSession($key) {

        unset($_SESSION[$key]);
    
    }

    /**
     * 清空$_SESSION内的值
     *
     * @param string $key            
     *
     * @return void
     */
    protected final function clearSession() {

        session_destroy();
    
    }

    /**
     * 设置$_COOKIE内的值
     *
     * @param string $key            
     * @param object $val            
     *
     * @return void
     */
    protected final function setCookie($key, $val, $expire = 86400, $path = '/', $domain = COOKIE_DOMAIN) {

        $expire += time();
        
        setcookie($key, $val, $expire, $path, $domain);
    
    }

    /**
     * 查询$_COOKIE内的值
     *
     * @param string $key            
     *
     * @return object
     */
    protected final function getCookie($key) {

        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        return false;
    
    }

    /**
     * Curl POST提交
     *
     * @param string $url            
     * @param array $param            
     * @param function $callback            
     *
     * @return void
     */
    public final function post($url, $param) {

        return Util::curl_request($url, 'POST', $param);
    
    }

    /**
     * Curl GET提交
     */
    public final function get($url) {

        return Util::curl_request($url, 'GET');
    
    }

    /**
     * 向模板传递变量
     */
    public final function assign($tpl_var, $value, $nocache = false) {

        $this->view->assignGlobal($tpl_var, $value, $nocache);
    
    }

    /**
     * 渲染模板
     */
    public final function display($template, $cache_id = null, $compile_id = null, $parent = null) {

        if ($this->view->templateExists($template)) {
            $this->view->display($template, $cache_id, $compile_id, $parent);
        } else {
            exit('404');
        }
    
    }

    /**
     * 返回渲染的结果
     */
    public final function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false) {

        if ($this->view->templateExists($template)) {
            return $this->view->fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
        }
        
        return '';
    
    }

    /**
     * 重定向
     *
     * @param string $key            
     *
     * @return void
     */
    public final function redirect($url) {

        header('location:' . $url);
        exit();
    
    }

    public final function display_json() {

        Context::dispatcher()->model = 'JSON';
    
    }
    
    public final function display_html() {
    
        Context::dispatcher()->model = 'HTML';
    
    }

    public $fd;

    public $svr;

    public final function send($op, $data, $fd = -1, $code = 1) {

        if ($fd === -1) {
            $fd = $this->fd;
        }
        
        $array = array (
            'code' => $code, 
            'op' => $op, 
            'version' => VERSION, 
            'unixtime' => Util::millisecond(), 
            'data' => $data 
        );
        
        $data = json_encode($array, true);
        
        $this->svr->push($fd, $data);
    
    }

    public final function error($data, $fd = -1) {

        if ($fd === -1) {
            $fd = $this->fd;
        }
        
        $this->send(-1, array (
            'message' => $data 
        ), $fd, 0);
    
    }

    public final function close($fd = -1) {

        if ($fd === -1) {
            $fd = $this->fd;
        }
        
        $this->svr->close($fd);
    
    }

}
?>