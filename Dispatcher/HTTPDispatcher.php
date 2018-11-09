<?php

namespace Dispatcher;

use Core\Util;
use Core\BaseCtrl;

class HTTPDispatcher
{

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
     * 参数
     *
     * @var array
     */
    public $params = array();

    /**
     * 分发器
     *
     * @param string $_ctrlPath
     * @throws \Exception\HTTPException
     */
    public function dispatch($_ctrlPath = 'Ctrl')
    {

        $this->ctrlPath = $_ctrlPath;

        if ($this->ctrlPath != 'Ctrl') {
            $this->template = $this->ctrlPath . '/templates';
            $this->compile = $this->ctrlPath . '/templates_c';
        }

        $slice = 1;
        $pargs = array();

        $ctrlName = 'Index';
        $methodName = '';

        // 普通访问
        if (isset($_GET['p'])) {
            $p = $_GET['p'];

            if ($p[0] == '/') {
                $p = substr($p, 1);
            }

            if ($p) {

                $pargs = explode('/', $p);

                // 取出最后一个参数
                $last = array_pop($pargs);

                // 取出最后一个参数的最后5个字符
                $tags = strtolower(substr($last, -5));

                // 判断是否是.html结尾
                if ($tags == '.html') {
                    if (count($pargs) == 0) {
                        $pargs[] = 'index';
                    }
                    $pargs[] = substr($last, 0, strlen($last) - 5); // 如果是，去除.html然后合并
                } else {
                    if ($last != '') {
                        $pargs[] = $last; // 如果不是，则直接合并
                    }
                }

                // ctrlName 永远是第1个
                $ctrlName = ucfirst(strtolower($pargs[0]));

                if (isset($pargs[1])) {
                    $methodName = strtolower($pargs[1]);
                }

                $slice = 1;
            }
        }


        $ctrls = explode('.', $ctrlName);

        $packet = '';
        foreach ($ctrls as $k => $v) {
            $v = strtolower($v);
            if ($k == count($ctrls) - 1) {
                $v = ucfirst(strtolower($v));
            }
            $packet .= '\\' . $v;
        }

        $className = $this->ctrlPath . $packet;

        if ($this->ctrlPath != 'Ctrl') {
            if (!file_exists(APP_PATH . '/' . $this->ctrlPath . '/' . $ctrlName . '.php')) {
                if (isset($pargs[1]) && file_exists(APP_PATH . '/' . $this->ctrlPath . '/plugins/' . strtolower($pargs[1]) . '/' . ucfirst(strtolower($pargs[1])) . '.php')) {

                    $this->template = $this->ctrlPath . '/plugins/' . strtolower($pargs[1]) . '/templates';

                    $this->compile = $this->ctrlPath . '/plugins/' . strtolower($pargs[1]) . '/templates_c';

                    $ctrlName = ucfirst(strtolower($pargs[1]));

                    if (isset($pargs[2]) == 2) {
                        $methodName = strtolower($pargs[2]);
                    }

                    $slice = 2;

                    // 编辑器插件
                    $className = $this->ctrlPath . '\\plugins\\' . strtolower($pargs[1]) . '\\' . $ctrlName;
                }
            }
        }


        if (!class_exists($className)) {
            header('HTTP/1.1 404 Not Found');
            echo '404';
            exit();
        }

        if (isset($GLOBALS['IGNORE'])) {
            $fullname = $className . '.' . $methodName;
            if (in_array($fullname, $GLOBALS['IGNORE'])) {
                header('HTTP/1.1 404 Not Found');
                echo 'ignore';
                exit();
            }
        }

        $GLOBALS['CTRL_NAME'] = strtolower($ctrlName);
        $GLOBALS['METHOD_NAME'] = strtolower($methodName);

        $ctrl = Util::loadCls($className);

        if (method_exists($ctrl, $methodName)) {
            $this->params = array_slice($pargs, $slice + 1);
        } else {
            $this->params = array_slice($pargs, $slice);
            $methodName = 'main';
        }

        $ctrl->isPost = false;

        $this->params = array_merge($this->params, $_GET);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $ctrl->isPost = true;

            $this->params = array_merge($this->params, $_POST, $_FILES);
        }

        if ($ctrl instanceof BaseCtrl) {
            $ctrl->initSmarty($this->template, $this->compile);
        }

        $ctrl->setParams($this->params);

        $ctrl->__initialize();

        $data = $ctrl->$methodName();

        $this->display($data, 0);

    }

    /**
     * @param $data
     * @param $code
     * @throws \Exception\HTTPException
     */
    public function display($data, $code)
    {

        if ($this->model == 'JSON') {
            $array = array(
                'code' => $code,
                'version' => VERSION,
                'unixtime' => Util::millisecond(),
                'data' => $data
            );

            echo json_encode($array, JSON_UNESCAPED_UNICODE);

            if (json_last_error() > 0) {
                throw Util::HTTPException(json_last_error_msg());
            }

        } else {
            if ($code != 0) {
                echo '<pre>';
                print_r($data);
                echo '</pre>';
            } else {
                if (is_array($data)) {
                    echo json_encode($data, JSON_UNESCAPED_UNICODE);
                    if (json_last_error() > 0) {
                        throw Util::HTTPException(json_last_error_msg());
                    }
                } else {
                    echo $data;
                }
            }
        }

    }

    /**
     * 执行一个Ctrl的方法
     *
     * @param $params
     * @throws \Exception\HTTPException
     */
    public function fetch($params)
    {

        $ctrlName = ucfirst(strtolower($params['action']));

        $className = $this->ctrlPath . '\\' . $ctrlName;

        $ctrl = Util::loadCls($className);

        if ($ctrl instanceof BaseCtrl) {
            $ctrl->initSmarty($this->template, $this->compile);
        }

        $ctrl->setParams($params);

        $method = $params['method'];

        $ctrl->$method();

    }

    /**
     * 查询数据
     *
     * @param $params
     * @return mixed
     */
    public function data($params)
    {

        $ctrlName = ucfirst(strtolower($params['action']));

        $className = $this->ctrlPath . '\\' . $ctrlName;

        $ctrl = Util::loadCls($className);

        $ctrl->setParams($params);

        $method = $params['method'];

        return $ctrl->$method();

    }

}