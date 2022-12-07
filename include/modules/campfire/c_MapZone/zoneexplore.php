<?php

namespace c_mapzone
{	//区域移动相关

	function get_next_mapinfo($p)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		$next = array_search($p,$arealist)+1;
		if(isset($arealist[$next]))
		{
			return $arealist[$next];
		}
		return 'end';
	}

	function get_prev_mapinfo($p)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		$prev = array_search($p,$arealist)-1;
		if(isset($arealist[$prev]))
		{
			return $arealist[$prev];
		}
		return 'end';
	}

	function get_mapzoneinfo($p,$z){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','c_mapzone'));
		$mpt=$mapzone_coorlist[$p][$z]['t'];
		if($mpt)
		{
			if($mpt == 'start')
			{
				$info = isset($mapzoneinfo['start'][$pls]) ? "<span class='yellow b'>{$mapzoneinfo['start'][$pls]}</span>" : '<span class="yellow b">入口</span>';
			}
			elseif($mpt == 'end')
			{
				$info = isset($mapzoneinfo['end'][$pls]) ? "<span class='yellow b'>{$mapzoneinfo['end'][$pls]}</span>" : '<span class="yellow b">出口</span>';
			}
			else
			{
				$mpt = explode('-',$mpt);
				$info = '<span class="b">'.$mapzoneinfo[$mpt[0]].'</span>';
			}
		}
		else
		{
			$info = '区域'.$z;
		}
		return $info;
	}

	function check_moveto_zone_dir($carr,$x,$y)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$dir = Array();
		$ny = $y-1; $sy = $y+1;
		$wx = $x-1; $ex = $x+1;
		//我真是日了狗了 方向对应关系 0=x+；1=x-；2=y+；3=y-；
		//向北	
		if(isset($carr[$x][$ny]))
		{
			$dir[3] = $carr[$x][$ny];
		} 
		//向西
		if(isset($carr[$wx][$y]))
		{
			$dir[1] = $carr[$wx][$y];
		}
		//向东
		if(isset($carr[$ex][$y]))
		{
			$dir[0] = $carr[$ex][$y];
		}
		//向南
		if(isset($carr[$x][$sy]))
		{
			$dir[2] = $carr[$x][$sy];
		}
		return $dir;
	}

	function check_moveto_pls_available()
	{	//我印象里见过一个和这个差不多名字的函数……不过不管了……
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','c_mapzone'));
		switch($pzone)
		{
			case 0:
				$moveto = get_prev_mapinfo($pls);
				break;
			case $mapzone_end[$pls]:
				$moveto = get_next_mapinfo($pls);
				break;
			default:
				$moveto = NULL;
		}
		if(array_search($moveto,$arealist) <= $areanum && !$hack)
		{
			return;
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
			$moveto  = substr($command,5);
			//$log.='你输入的指令是'.$moveto;
			//$moveto = \c_mapzone\check_moveto_zone_dir($carr,$x,$y);
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