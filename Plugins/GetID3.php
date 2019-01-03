<?php
class GetID3
{

    private static $getid3;

    public static function instance()
    {
        if (self::$getid3 == null) {
            include_once 'getid3/getid3.php';

            self::$getid3 = new \getID3();
        }
        return self::$getid3;
    }

    /**
     * @param $filename
     * @return mixed
     */
    public static function analyze($filename)
    {
        return self::instance()->analyze($filename);
    }

}