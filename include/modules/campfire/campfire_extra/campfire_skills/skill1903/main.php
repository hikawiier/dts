<?php

namespace skill1903
{
	$skill1903_base_obbs = 40;
	$skill1903_add_obbs = 10;
	$skill1903_max_obbs = 99;
	
	function init() 
	{
		define('MOD_SKILL1903_INFO','feature;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1903] = '清洗';
	}
	
	function acquire1903(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1903'));
		\skillbase\skill_setvalue(1903,'var',$skill1903_base_obbs,$pa);
	}
	
	function lost1903(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_delvalue(1903,'var',$pa);
	}
	
	function check_unlocked1903(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function get_skill1903_procrate(&$pa,&$pd,&$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1903','player','logger'));
		if (!\skillbase\skill_query(1903, $pa) || !check_unlocked1903($pa)) return 0;
		$r = \skillbase\skill_getvalue(1903,'var',$pa);
		return $r;
	}

	function apply_total_damage_modifier_seckill(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(1903,$pa) && check_unlocked1903($pa)){
			eval(import_module('skill1903','logger'));
			$var_1903=get_skill1903_procrate($pa,$pd,$active);
			if ( rand(0,99) < $var_1903 && $pa['is_hit'] && ( $pd['mhp'] < 5000000 || $var_1903 >= 10 )){
				$pa['dmg_dealt']=$pd['hp'];
				if ($active) $log .= "<span class=\"red b\">一股难以言喻的力量直接杀死了你的敌人！</span><br>";
				else $log .= "<span class=\"red b\">一股难以言喻的力量直接杀死了你！</span><br>";
				$pa['seckill'] = 1;
				if($var_1903 > $skill1903_base_obbs) \skillbase\skill_setvalue(1903,'var',$skill1903_base_obbs,$pa);
			}else{
				$var_1903 =min($var_1903+$skill1903_add_obbs,$skill1903_max_obbs);
				\skillbase\skill_setvalue(1903,'var',$var_1903,$pa);
			}
		}
		$chprocess($pa,$pd,$active);
	}
}

?>