<?php

namespace c_mobilephone
{
	//呃呃呃呃呃呃e为什么命名空间大写会报错
	
	function init() {}
	
	function print_mapzonedata()
	{
		//疑问 这个函数放这里还是放c_mapzone里 感觉放mapzone里更合适
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','c_mapzone'));
		$zlist = $mapzone_coorlist[$pls];
		for($i=0;$i<sizeof($zlist);$i++){
			list($x, $y) = explode('-',$zlist[$i]);
			$max_x = max($max_x,$x);
			$max_y = max($max_y,$y);
			$mpp[$x][$y]=$i;
		}
		$mpp['max_x'] = $max_x;
		$mpp['max_y'] = $max_y;
		return $mpp;
	}	

	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','input','logger','explore'));
		if ($mode == 'special' && $command == 'viewphone') 
		{
			$mode = MOD_C_MOBILEPHONE_PHONEPAGE;
			return;
		}
		if ($mode == 'special' && $command == 'mapzone_special') 
		{
			\explore\move($subcmd);
			return;
		}

		$chprocess();
	}
}

?>