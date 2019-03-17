<?php

namespace skill1905
{
	//能够触发均衡效果的NPC类型……暂时先这么写死，回头有需要玩家调用再重做
	$type1905 = Array(1007);
	function init() 
	{
		define('MOD_SKILL1905_INFO','feature;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1905] = '均衡';
	}
	
	function acquire1905(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1905'));
		\skillbase\skill_setvalue(1905,'var','1',$pa);
	}
	
	function lost1905(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1905(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function balance_status1905()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger','skill1905'));
		$n_arr=Array();$max_hp_scale=0;
		foreach($type1905 as $t)
		{//外循环，所有支持均衡技能的NPC类别
			$result = $db->query("SELECT * FROM {$tablepre}players WHERE type = $t AND hp>0");
			if($result)
			{
				while($ndata = $db->fetch_array($result)) 
				{//内循环，对该NPC类型产生均衡效果
					$get1905 = \skillbase\skill_getvalue_direct(1905,'var',$ndata['nskillpara']);
					if($get1905)
					{
						$hp_scale = $ndata['hp']/$ndata['mhp'];
						//获取目前生命百分比最高的NPC
						if($hp_scale > $max_hp_scale) $max_hp_scale=$hp_scale;
						//记录该NPC的PID与生命最大值
						$n_arr[]=$ndata['pid'];
					}
				}
				if($max_hp_scale)
				{
					foreach($n_arr as $nid)
					{//内循环，调整该类NPC血量
						$data = \player\fetch_playerdata_by_pid($nid);
						if($data['hp']<round($data['mhp']*$max_hp_scale))
						{
							$data['hp'] = round($data['mhp']*$max_hp_scale);
							\player\player_save($data);
							addnews($now, 'balance1905',$data['name']);
						}							
					}
				}		
			}				
		}
	}
	
	function post_addarea_process($atime, $areaaddlist)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1905'));
		balance_status1905();
		$chprocess($atime, $areaaddlist);
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		
		if($news == 'balance1905') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"brickred b\">在均衡的作用下，{$a}的生命力得到了恢复！</span></li>";
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>