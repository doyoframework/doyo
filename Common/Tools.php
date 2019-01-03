<?php

namespace Common;

use Core\ITools;
use Core\Util;

class Tools implements ITools
{

    /**
     * 返回md5的密码
     *
     * @param string $passport
     * @param string $password
     * @return string
     */
    public static function md5pswd($passport, $password)
    {

        return md5(md5(strtolower($passport)) . WEB_MIX . $password);

    }

    /**
     * @param object $data
     * @param string $url
     * @param bool $args
     * @return string
     */
    public static function paging($data, $url, $args = false)
    {

        $page = $data['page'];
        $pcount = $data['pcount'];
        $next = $data['next'];
        $prev = $data['prev'];

        $pagenum = 7;

        $begin = 1;
        $end = $pagenum;

        if ($end >= $pcount) {
            $end = $pcount;
        } else {
            if ($page > ceil($pagenum / 2)) {
                $begin = $page - floor($pagenum / 2);
                $end = $page + floor($pagenum / 2);
            }
            if ($page + floor($pagenum / 2) >= $pcount) {
                $begin = $pcount - $pagenum + 1;
                $end = $pcount;
            }
        }

        if (empty($args))
            $args = array();

        $firsturl = vsprintf($url, array_merge($args, array(
            1
        )));

        $prevurl = vsprintf($url, array_merge($args, array(
            $prev
        )));
        $nexturl = vsprintf($url, array_merge($args, array(
            $next
        )));
        $lasturl = vsprintf($url, array_merge($args, array(
            $pcount
        )));

        $pagstr = array();
        $pagstr[] = '<ul>';

        if ($page == $prev) {
            $pagstr[] = '<li class="active"><a href="#">&lt;</a></li>';
        } else {
            $pagstr[] = '<li><a href="' . $firsturl . '" target="editContent">&lt;&lt;</a></li>';
            $pagstr[] = '<li><a href="' . $prevurl . '" target="editContent">&lt;</a></li>';
        }

        for ($i = $begin; $i <= $end; $i++) {
            if ($i == $page) {
                $pagstr[] = '<li class="active"><a href="#"><u>' . $i . '</u></a></li>';
            } else {
                $pageurl = vsprintf($url, array_merge($args, array(
                    $i
                )));
                $pagstr[] = '<li><a href="' . $pageurl . '" target="editContent">' . $i . '</a></li>';
            }
        }
        if ($page == $next) {
            $pagstr[] = '<li class="active"><a href="#">&gt;</a></li>';
        } else {
            $pagstr[] = '<li><a href="' . $nexturl . '" target="editContent">&gt;</a></li>';
            $pagstr[] = '<li><a href="' . $lasturl . '" target="editContent">&gt;&gt;</a></li>';
        }
        $pagstr[] = '</ul>';

        return implode($pagstr, "\n");

    }


}