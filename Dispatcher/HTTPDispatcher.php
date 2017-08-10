<?php
namespace Dispatcher;

use Core\Util;
use Core\BaseCtrl;

class HTTPDispatcher {

    /**
     * 控制器目录
     */
    private $ctrlPath = '';

    /**
     * Smarty模板目录
     */
    private $template = null;

    /**
     * Smarty模板编译目录
     */
    private $compile = null;

    /**
     * 显示报错/方法返回的方式
     */
    public $model = 'HTML';

    /**
     * 分发器
     */
    public function dispatch($_ctrlPath = 'Ctrl') {

        $this->ctrlPath = $_ctrlPath;
        
        if ($this->ctrlPath != 'Ctrl') {
            $this->template = $this->ctrlPath . '/templates';
            $this->compile = $this->ctrlPath . '/templates_c';
        }
        
        $ctrlName = "Index";
        $methodName = "main";
        
        $params = array ();
        
        // 普通访问
        if (isset($_GET['p'])) {
            $p = $_GET['p'];
            
            if ($p[0] == '/') {
                $p = substr($p, 1);
            }
            
            if ($p) {
                
                $pargs = explode("/", $p);
                
                // 取出最后一个参数
                $last = array_pop($pargs);
                
                // 取出最后一个参数的最后5个字符
                $tags = strtolower(substr($last, -5));
                
                // 判断是否是.html结尾
                if ($tags == '.html') {
                    $pargs[] = substr($last, 0, strlen($last) - 5); // 如果是，去除.html然后合并
                } else {
                    $pargs[] = $last; // 如果不是，则直接合并
                }
                
                // ctrlName 永远是第1个
                $ctrlName = ucfirst(strtolower($pargs[0]));
                
                if (count($pargs) == 2) { // 如果有两个参数，则第一个是ctrl，第二个是参数，默认method是main
                    $params = array_splice($pargs, 1);
                } else if (count($pargs) >= 3) { // 如果有三个参数，则第一个是ctrl，第二个是method，第三个以后都是参数
                    $params = array_splice($pargs, 2);
                    
                    $methodName = strtolower($pargs[1]);
                }
            }
        }
        
        $className = $this->ctrlPath . "\\" . $ctrlName;
        
        if ($this->ctrlPath != 'Ctrl') {
            if (!file_exists(APP_PATH . '/' . $this->ctrlPath . '/' . $ctrlName . '.php')) {
                if (file_exists(APP_PATH . '/' . $this->ctrlPath . '/plugins/' . strtolower($ctrlName) . '/' . $ctrlName . '.php')) {
                    
                    $className = $this->ctrlPath . "\\plugins\\" . strtolower($ctrlName) . '\\' . $ctrlName;
                    
                    $this->template = $this->ctrlPath . '/plugins/' . strtolower($ctrlName) . '/templates';
                    
                    $this->compile = $this->ctrlPath . '/plugins/' . strtolower($ctrlName) . '/templates_c';
                }
            }
        }
        
        if (!class_exists($className)) {
            header('HTTP/1.1 404 Not Found');
            echo '404';
            exit();
        }
        
        $GLOBALS['CTRL_NAME'] = strtolower($ctrlName);
        $GLOBALS['METHOD_NAME'] = strtolower($methodName); 

        $ctrl = Util::loadCls($className);
        
        if ($ctrlName != 'Plugins' && !method_exists($ctrl, $methodName)) {
            $params = array_splice($pargs, 1);
            $methodName = 'main';
        }
        
        if ($ctrl instanceof BaseCtrl) {
            $ctrl->isPost = false;
            
            if (in_array($methodName, array (
                'get', 
                'post', 
                'assign', 
                'display', 
                'redirect', 
                'display_json', 
                'initfiles', 
                'setsmarty', 
                'initsmarty', 
                '__construct', 
                '__initialize' 
            ))) {
                header('HTTP/1.1 404 Not Found');
                echo '404';
                exit();
            }
            
            if (!empty($_POST)) {
                
                $ctrl->isPost = true;
                
                $params = array_merge($params, $_POST, $_FILES);
            }
            
            $ctrl->initSmarty($this->template, $this->compile);
        }
                
        $ctrl->setParams($params);
        
        $msg = $ctrl->$methodName();
        
        if ($msg) {
            $this->display($msg, 0);
        }
    
    }

    public function display($msg, $status) {

        if ($this->model == 'JSON') {
            
            $array = array (
                "ret" => $status, 
                "version" => VERSION, 
                "unixtime" => REQUEST_TIME, 
                'msg' => $msg 
            );
            
            echo json_encode($array);
        } else {
            if ($status == 1) {
                echo "<pre>";
                print_r($msg);
                echo "</pre>";
            } else {
                if (is_array($msg)) {
                    echo json_encode($msg);
                } else {
                    echo $msg;
                }
            }
        }
    
    }

    /**
     * 执行一个Ctrl的方法
     */
    public function fetch($params) {

        $ctrlName = ucfirst(strtolower($params['action']));
        
        $className = $this->ctrlPath . "\\" . $ctrlName;
        
        $ctrl = Util::loadCls($className);
        
        if ($ctrl instanceof BaseCtrl)
            $ctrl->initSmarty($this->template, $this->compile);
        
        $ctrl->setParams($params);
        
        $method = $params['method'];
        
        $ctrl->$method();
    
    }

    /**
     * 查询数据
     */
    public function data($params) {

        $ctrlName = ucfirst(strtolower($params['action']));
        
        $className = $this->ctrlPath . "\\" . $ctrlName;
        
        $ctrl = Util::loadCls($className);
        
        $ctrl->setParams($params);
        
        $method = $params['method'];
        
        return $ctrl->$method();
    
    }

}
?>