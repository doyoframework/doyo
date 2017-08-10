<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 * 
 * @author D.Y
 */

function smarty_function_inner($params, $template)
{
    global $dispatcher;
    
    $dispatcher->fetch($params);

}
?>