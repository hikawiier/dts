<?php

namespace campfire_hiddenarea
{
	function init() 
	{
		eval(import_module('itemmain'));
		//不会有物品掉落的地区列表
		$map_noitemdrop_arealist[] = 99;
		$map_noitemdrop_arealist[] = 98;
		$map_noitemdrop_arealist[] = 97;	
	}
	
	function campfire_teleport($tpto=99)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger'));		
		$defaultpls = $pls;
		if($tpto==99)
		{
			$pls_available = \map\get_safe_plslist(0);//如果只能移动到危险区域，就移动到危险区域
			shuffle($pls_available);
			$pls = $pls_available[0];
		}
		else
		{
			$pls = $tpto;
		}
	}
}

?>
