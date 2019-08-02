<?php

namespace skill1912
{	
	function init() 
	{
		define('MOD_SKILL1912_INFO','card;unique;feature;');
		eval(import_module('clubbase'));
		$clubskillname[1912] = '朋友';
	}
	
	function acquire1912(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$maxsans = rand(1,20);
		$maxsans *= 5;
		\skillbase\skill_setvalue(1912,'status','1',$pa);
		\skillbase\skill_setvalue(1912,'sans',$maxsans,$pa);
	}
	
	function lost1912(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1912(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function get_status1912(&$pa = NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return (int)\skillbase\skill_getvalue(1912,'status',$pa);
	}
	
	function get_sans1912(&$pa = NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return \skillbase\skill_getvalue(1912,'sans',$pa);
	}
}

?>
