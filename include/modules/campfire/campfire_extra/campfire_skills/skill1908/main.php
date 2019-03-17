<?php

namespace skill1908
{
	//能够触发调谐效果的NPC类型……暂时先这么写死，主要是为了降低数据库查询次数
	$type1908 = Array(1007);
	function init() 
	{
		define('MOD_SKILL1908_INFO','feature;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1908] = '调谐';
	}
	
	function acquire1908(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(1908,'imm_wep','',$pa);
		\skillbase\skill_setvalue(1908,'suf_wep','',$pa);
	}
	
	function lost1908(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1908(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function get_immwep1908(&$pa = NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return \skillbase\skill_getvalue(1908,'imm_wep',$pa);
	}	
	
	function get_sufwep1908(&$pa = NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return \skillbase\skill_getvalue(1908,'suf_wep',$pa);
	}
	
	function check_imm_wepinfo1908($p,$w)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$immwep = get_immwep1908($p);
		$w = restore_wepname1908($w);
		if(strpos($immwep,$w)!==false) return 1;
		else return 0;
	}
	
	function check_suf_wepinfo1908($p,$w)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$sufwep = get_sufwep1908($p);
		$w = restore_wepname1908($w);
		if(strpos($sufwep,$w)!==false) return 1;
		else return 0;
	}
	
	function restore_wepname1908($n)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('itemmix'));
		$itmname_ignore[] = '/\[\+[0-9]+?\]/si';
		$n = trim($n);
		foreach(Array($itmname_ignore) as $value)
		{
			$n = preg_replace($value,'',$n);
		}
		$n = str_replace('钉棍棒','棍棒',$n);
		return $n;
	}
	
	function check_get_immwepinfo1908(&$p,$w)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger','skill1908'));
		//先将它记录为唯一可造成伤害的武器
		$log.="<span class='grey b'>{$w}的频率被记录了……</span><br>";
		$w = restore_wepname1908($w);
		\skillbase\skill_setvalue(1908,'suf_wep',$w,$p);
		//然后再检查该武器是否存在于技能通用的免疫武器列表中
		if(get_immwep1908($p) || !check_imm_wepinfo1908($p,$w))
		{
			$immwep = get_immwep1908($p);
			if(strpos($immwep,$w)===false)
			{
				if($immwep) $immwep .= ','.$w;
				else $immwep=$w;
				\skillbase\skill_setvalue(1908,'imm_wep',$immwep,$p);
			}
			foreach($type1908 as $t)
			{//外循环，获取所有支持该技能的NPCid
				$result = $db->query("SELECT pid FROM {$tablepre}players WHERE type = $t AND hp>0");
				if($db->num_rows($result))
				{
					while($r = $db->fetch_array($result))
					{
						$pids[] = $r['pid'];
					}
				}
				foreach($pids as $piv)
				{
					//内循环
					$edata = \player\fetch_playerdata_by_pid($piv);
					if (\skillbase\skill_query(1908,$edata))
					{
						\skillbase\skill_setvalue(1908,'imm_wep',$immwep,$edata);
						\player\player_save($edata);
					}	
				}				
			}
		}
	}
	
	function apply_total_damage_modifier_invincible(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (!\skillbase\skill_query(1908,$pd)) return $chprocess($pa,$pd,$active);
		eval(import_module('sys','logger','skill1908'));
		if (\skillbase\skill_query(1908,$pd))
		{	
			if((!get_immwep1908($pd) && !get_sufwep1908($pd)) || (get_immwep1908($pd) && !get_sufwep1908($pd) && !check_imm_wepinfo1908($pd,$pa['wep'])) || (get_immwep1908($pd) && get_sufwep1908($pd) && check_suf_wepinfo1908($pd,$pa['wep'])))
			{
				//能造成伤害但不属于sufwep的情况下				
				if(!check_suf_wepinfo1908($pd,$pa['wep'])) check_get_immwepinfo1908($pd,$pa['wep']);
			}
			else
			{	
				$pa['dmg_dealt']=0;
				if ($active) $log .= "<span class=\"lime b\">你的攻击迅猛刚烈，然而却像打在了棉花上一样没有产生任何效果。</span><br>";
				else $log .= "<span class=\"lime b\">敌人的攻击迅猛刚烈，然而却像打在了棉花上一样没有产生任何效果。</span><br>";
			}	
		}
		$chprocess($pa,$pd,$active);
	}
}

?>