<?php
namespace Core;

class Session {

    public static function get($key) {

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        
        return null;
    
    }

}

?>