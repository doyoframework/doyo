<?php
namespace Core;

interface ITools {

    /**
     * 获得md5密码
     *
     * @param $passport
     * @param $password
     * @return mixed
     */
    public static function md5pswd($passport, $password);

    /**
     * 分页
     *
     * @param $data
     * @param $url
     * @param null $args
     * @return mixed
     */
    public static function paging($data, $url, $args = null);

}
