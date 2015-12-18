<?php

namespace buffeffect
{
	function init() 
	{
	}
	
	function get_buff_full_food()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		$log.="你看起来好像吃饱了！<br>";
		$msp+=200;
		$sp+=200;
		$att+=200;
		$def+=200;		
	}
	function lost_buff_full_food()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		$log.="效果结束了！<br>";
		$msp-=200;
		$sp-=200;
		$att-=200;
		$def-=200;		
	}
}

?>
