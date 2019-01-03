<?php

namespace Ctrl;

use Core\BaseCtrl;
use Core\Util;

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
        $alisms = Util::loadSms();

        $param = [];
        $param['code'] = 123;

        $alisms->send('15901232947', '云中轮留修宝', '117769600', $param);
    }
}