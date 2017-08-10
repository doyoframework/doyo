<?php
namespace Core;

interface ITools {

    /**
     * 获得md5密码
     *
     * @param string $passport            
     * @param string $password            
     */
    public static function md5pswd($passport, $password);

    /**
     * 分页
     *
     * @param object $data            
     * @param string $url            
     * @param array $args            
     *
     */
    public static function paging($data, $url, $args = null);

}
?>