<?php

namespace skill523
{

	function init()
	{
		define('MOD_SKILL523_INFO','card;unique;locked;feature;');
		eval(import_module('clubbase'));
		$clubskillname[523] = '日精';
	}

	function acquire523(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}

	function lost523(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}

	function check_unlocked523(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}


	//非灵系物理伤害为零
	function check_skill523_proc(&$pa, &$pd, $active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill523','player','logger'));
		if ($active && (strstr($pa['wepk'], 'F') == '')){
			$log .=  \battle\battlelog_parser($pa, $pd, $active, '<span class="yellow b"><:pa_name:>的物理伤害无效</span><br>');
			$r = 1;
			return $r;
		}
		return 0;
	}

	function get_physical_dmg_change(&$pa, &$pd, $active, $dmg)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;

		if(check_skill523_proc($pa,$pd,$active)){
			$dmg = 0;
		}
		return $dmg;
	}

	//火系伤害4倍
	function calculate_ex_attack_dmg_multiplier(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;

		$r=Array();
		if (\skillbase\skill_query(523,$pd) && check_unlocked523($pd) &&(( strstr($pa['wepsk'], 'u') != '') || strstr($pa['wepsk'], 'f'))){
			$r = Array(4);
		}

		return array_merge($r,$chprocess($pa,$pd,$active));
	}

}

?>
