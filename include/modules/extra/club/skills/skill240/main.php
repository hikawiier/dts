<?php

namespace skill240
{
	function init() 
	{
		define('MOD_SKILL240_INFO','club;');
		eval(import_module('clubbase'));
		$clubskillname[240] = '洞察';
	}
	
	function acquire240(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost240(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked240(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	function calculate_real_trap_obbs_change($var,$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(240,$pa))
			return $chprocess($var*0.85,$pa);
		else  return $chprocess($var,$pa);
	}
	
	function calculate_active_obbs_multiplier(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$r = 1;
		if (\skillbase\skill_query(240,$ldata) && check_unlocked240($ldata)) $r*=1.08;
		if (\skillbase\skill_query(240,$edata) && check_unlocked240($edata)) $r/=1.08;
		if($r != 1) $ldata['active_words'] = \attack\multiply_format($r, $ldata['active_words'],0);
		return $chprocess($ldata,$edata)*$r;
	}
}

?>
