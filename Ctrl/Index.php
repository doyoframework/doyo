<?php

namespace Ctrl;

use Core\BaseCtrl;
use Core\Util;
use Plugins\GetID3;
use Plugins\PHPExcel;
use Plugins\QRCode;
use Plugins\WxService;

class Index extends BaseCtrl
{

    /**
     * @throws \Exception\HTTPException
     */
    public function main()
    {
        $passport_id = $this->getSession('passport_id');

        if ($passport_id > 0) {
            $this->assign('page', 'main/main.html');
        } else {
            $this->assign('page', 'main/login.html');
        }

        $this->display('index.html');
    }

    public function page()
    {

    }

    public function sms()
    {
        $sms = Util::loadSms();

        $param = [];
        $param['code'] = 123;

        $ret = $sms->send('15901232947', ALI_SMS_SIGN_NAME, ALI_SMS_TEMPLATE_CODE, $param);

        print_r($ret);
    }

    /**
     * @throws \OSS\Core\OssException
     */
    public function oss()
    {
        $oss = Util::loadOss();

        $file = [];
        $file['type'] = 'log';
        $file['tmp_name'] = '/tmp/svn.log';

        $ret = $oss->upload(OSS_BUCKET, '/test/svn.log', $file);

        print_r($ret);

    }

    /**
     * @throws \Exception\HTTPException
     * @throws \OSS\Core\OssException
     */
    public function upload()
    {

        if ($this->isPost) {

            $file = Util::initFiles();

            $ret = $file->upload('file', 1, 'test');

            print_r($ret);

        } else {

            $this->display('upload.html');
        }

    }

    public function getid3()
    {
        $new_file = '/tmp/a.mp3';
        $analyze = GetID3::analyze($new_file);
        print_r($analyze);
    }

    /**
     * @throws \Exception
     */
    public function mail()
    {
        $mail = Util::loadMail();

        $ret = $mail->send('115561897@qq.com', 'test', 'test body', ['/tmp/svn.log' => 'svn.log']);

        print_r($ret);
    }

    /**
     * @throws \Exception\HTTPException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function import()
    {
        if ($this->isPost) {

            $excel = new PHPExcel();

            $ret = $excel->import($_FILES['file']['tmp_name'], 'xls');

            print_r($ret);

        } else {

            $this->display('import.html');
        }
    }

    public function qrcode()
    {
        $qrcode = new QRCode();
        $qrcode->png('aaa');
    }

    /**
     * @throws \Exception\HTTPException
     */
    public function service()
    {
        $service = new WxService('wx7d116e5036ea484e', '3ccc62383d6ac48fa82d8f1fe12a59a8');

        $menu = $service->get_menu();

        print_r($menu);
    }
}