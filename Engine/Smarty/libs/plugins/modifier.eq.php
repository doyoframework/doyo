<?php
/**
 * Smarty eq modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     eq<br>
 * Purpose:  if args1 else args2
 *
 * {@internal {$string|eq:$val:args1:args2}}}
 *
 * @param string  $string
 * @param string  $val  
 * @param string  $args1
 * @param string  $args2
 * 
 * @return string eq string
 * 
 * @author D.Y
 */
function smarty_modifier_eq($string, $val, $args1, $args2 = false)
{
	if($string == $val) {
		echo $args1;
	}else{
		echo $args2;
	}
} 
?>