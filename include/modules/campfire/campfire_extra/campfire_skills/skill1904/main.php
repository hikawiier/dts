<?php

namespace skill1904
{
	
	function init() 
	{
		define('MOD_SKILL1904_INFO','feature;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1904] = '仁慈';
	}
	
	function acquire1904(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost1904(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1904(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function get_trap_final_damage_modifier_down(&$pa, &$pd, $tritm, $damage)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (!\skillbase\skill_query(1904,$pa)) return $chprocess($pa,$pd,$tritm,$damage);
		eval(import_module('logger'));
		$z=$chprocess($pa,$pd,$tritm,$damage);
		if ($z>=$pd['hp'] && $pd['hp']>1)
		{
			$z=$pd['hp']-1;
			$log .= "你感觉自己被炸了个七荤八素，六神无主。<br>然而出于设置陷阱者的<span class=\"yellow b\">仁慈</span>，你竟然没被炸死，真是可喜可贺！<br>";
		}
		return $z;
	}
	
	function check_damage_limit1904(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','logger','skill1904'));
		if(($pa['dmg_dealt']>$pd['hp']) && (\skillbase\skill_query(1904,$pa)) && $pd['hp']>1)
		{
			$pa['dmg_dealt']=$pd['hp']-1;
			if ($active) $log .= "<span class=\"yellow b\">你如狂风骤雨般的攻击打得敌人难以招架，但出于仁慈，你给你的敌人留下了一线生机！</span><br>";
			else $log .= "<span class=\"yellow b\">敌人如狂风骤雨般的攻击打得你的难以招架，但出于仁慈，你的敌人给你留下了一线生机！</span><br>";
		}
	}	
	
	function apply_total_damage_modifier_insurance(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		check_damage_limit1904($pa, $pd, $active);
	}
}

?>