<?php

namespace skill1907
{
	//伏计生效的区域
	$trap_areagroup1907 = '数据之海';
	$trap_area1907 = Array(92,93,94);
	$trap_areanum1907 = sizeof($trap_area1907);
	//每次行动会触发伏计的概率
	$trap_set_obbs1907 = 12;
	function init() 
	{
		define('MOD_SKILL1907_INFO','card;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1907] = '伏技';
	}
	
	function acquire1907(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1907'));
		\skillbase\skill_setvalue(1907,'var','1',$pa);
		\skillbase\skill_setvalue(1907,'tn','奇迹螺旋',$pa);
		\skillbase\skill_setvalue(1907,'te','33333',$pa);
		\skillbase\skill_setvalue(1907,'tk','TO',$pa);
	}
	
	function lost1907(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1907(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function settrap1907()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger','map','skill1907'));
		foreach($trap_area1907 as $t)
		{//外循环，搜索地图区内是否拥有伏计技能的NPC
			$result = $db->query("SELECT * FROM {$tablepre}players WHERE pls='$t' AND hp>0");
			if($result)
			{
				while($ndata = $db->fetch_array($result)) 
				{//内循环，输出拥有伏计技能NPC的
					$get1907 = \skillbase\skill_getvalue_direct(1907,'var',$ndata['nskillpara']);
					if($get1907)
					{
						if(rand(0,99)<$trap_set_obbs1907)
						{
							$trapsetter = $ndata['pid'];
							$trap = \skillbase\skill_getvalue_direct(1907,'tn',$ndata['nskillpara']);
							$trape = \skillbase\skill_getvalue_direct(1907,'te',$ndata['nskillpara']);
							$trapk =\skillbase\skill_getvalue_direct(1907,'tk',$ndata['nskillpara']);
							shuffle($trap_area1907);
							$trappls = $trap_area1907[0];
							$db->query("INSERT INTO {$tablepre}maptrap (itm, itmk, itme, itms, itmsk, pls) VALUES ('$trap', '$trapk', '$trape', '1', '$trapsetter', '$trappls')");
							addnews($now, 'trap1907',$ndata['name'],$trappls);
						}					
					}
				}	
			}				
		}
	}
	
	function act(){//在伏计生效的区域，每次动作都会判断是否触发伏计
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','skill1907'));		
		if(in_array($pls,$trap_area1907)){
			settrap1907();
		}
		$chprocess();
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));
		
		if($news == 'trap1907') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"brickred b\">{$a}在{$plsinfo[$b]}设下了陷阱！</span></li>";
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>