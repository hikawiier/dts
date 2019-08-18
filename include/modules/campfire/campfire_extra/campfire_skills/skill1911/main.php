<?php

namespace skill1911
{	
	function init() 
	{
		define('MOD_SKILL1911_INFO','card;unique;locked;');
		eval(import_module('clubbase'));
		$clubskillname[1911] = '遗梦';
	}
	
	function acquire1911(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$pa['mhp'] = round($pa['mhp']*0.5);
		$pa['hp'] = $pa['mhp'];
		$pa['msp'] = round($pa['msp']*0.5);
		$pa['sp'] = $pa['msp'];
		\skillbase\skill_setvalue(1911,'unlocked','0',$pa);
	}
	
	function lost1911(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1911(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	//通过战斗获取经验的速度变为原本的3倍
	function calculate_attack_exp_gain_multiplier(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa,$pd,$active);
		if (\skillbase\skill_query(1911,$pa)) $ret *= 3;
		return $ret;
	}
	
	//通过战斗获取的熟练度额外增加3点
	function calculate_attack_weapon_skill_gain_base(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa,$pd,$active);
		if ( \skillbase\skill_query(1911,$pa) && check_unlocked1911($pa) ) $ret += 3;
		return $ret;
	}
}

?>