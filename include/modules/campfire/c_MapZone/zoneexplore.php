<?php

namespace c_mapzone
{	//区域移动相关

	function check_moveto_zone_available($dir)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','c_mapzone'));

		$mpp = $mapzone_coorarr[$pls];
		$x = $mapzone_coorlist[$pls][$pzone]['x'];
		$y = $mapzone_coorlist[$pls][$pzone]['y'];
		$nx = $x; $ny = $y;
		switch($dir)
		{ // 0=n 1=w 2=e 3=s
			case 0:
				$ny = $y-1;
				//echo '向北：'.$x.'-'.$ny;
				if(isset($mpp[$x][$ny]))
				{
					return $mpp[$x][$ny];
				} 
				break;
			case 1:
				$nx = $x-1;
				//echo '向西：'.$nx.'-'.$y;
				if(isset($mpp[$nx][$y]))
				{
					return $mpp[$nx][$y];
				}
				break;
			case 2:
				$nx = $x+1;
				//echo '向东：'.$nx.'-'.$y;
				if(isset($mpp[$nx][$y]))
				{
					return $mpp[$nx][$y];
				}
				break;
			case 3:
				$ny = $y+1;
				//echo '向南：'.$x.'-'.$ny;
				if(isset($mpp[$x][$ny]))
				{
					return $mpp[$x][$ny];
				}
				break;
			default:
				return NULL;
				break;
		}
		return NULL;
	}

	function check_moveto_pls_available()
	{	//我印象里见过一个和这个差不多名字的函数……不过不管了……
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','c_mapzone'));

		$moveto = NULL;
		switch($pzone)
		{
			case 0:
				$moveto = $arealist[array_search($pls,$arealist)-1];
				break;
			case $mapzone_end[$pls]:
				$moveto = $arealist[array_search($pls,$arealist)+1];
				break;
			default:
				return NULL;
				break;
		}
		if(array_search($moveto,$arealist) <= $areanum && !$hack)
		{
			return NULL;
		}
		else
		{
			return $moveto;
		}
	}

	function move_to_area($moveto)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger'));
		
		if(strpos($command,'move_')===0)
		{
			return;
		}
		$chprocess($moveto);
	}

	function move_to_zone($moveto)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger','explore','c_mapzone'));

		$pzonenum = $mapzone_end[$pls];
		if(($moveto === 'main')||($moveto < 0 )||($moveto > $pzonenum)){
			$log .= '请选择正确的移动地点2。<br>'.$moveto.$mapzone_end[$pls];
			return;
		} elseif($pzone == $moveto){
			$log .= '相同地点，不需要移动。<br>';
			return;
		}
		$movesp=max(\explore\calculate_move_sp_cost(),1);		
		if($sp <= $movesp){
			$log .= "体力不足，不能移动！<br>还是先睡会儿吧！<br>";
			return;
		}
		$sp -= $movesp;
		$log .= "你消耗<span class=\"yellow b\">{$movesp}</span>点体力，移动到了$plsinfo[$pls]的区域[$moveto]。<br>";
		$pzone = $moveto;
		$moveto = 'move_'.$moveto;
		\explore\move_to_area($moveto);
	}

	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','player','input','explore','c_mapzone'));

		if($mode == 'command' && strpos($command,'move_')===0) 
		{
			$moveto = NULL;
			$dir = substr($command,5);
			$moveto = \c_mapzone\check_moveto_zone_available($dir);
			if(isset($moveto))	\c_mapzone\move_to_zone($moveto);				
		} 
		elseif ($mode == 'command' && strpos($command,'movepls')===0) 
		{
			$moveto = NULL;
			$moveto = \c_mapzone\check_moveto_pls_available();
			if(isset($moveto))	\explore\move($moveto);	
		}
		$chprocess();
	}
}

?>