<?php

namespace Core;

class Session
{

    public static function set($key, $val)
    {

        $_SESSION[$key] = $val;

    }

    public static function get($key)
    {

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return null;

    }

    public static function exists($key)
    {

        return isset($_SESSION[$key]);

    }

    public static function del($key)
    {

        unset($_SESSION[$key]);

    }

    public static function clear()
    {

        session_unset();

    }

    public static function destroy()
    {

        session_destroy();

    }

}
