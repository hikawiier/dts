<?php

namespace skill22
{
	function init() 
	{
		define('MOD_SKILL22_INFO','club;hidden;');
	}
	
	function acquire22(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost22(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function get_trap_escape_rate($pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(22,$pa)) return $chprocess($pa)-4; else return $chprocess($pa);
	}
	
	function calculate_trap_reuse_rate()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(22)) return $chprocess()-15; else return $chprocess();
	}
	
	function calculate_real_trap_obbs_change($var,$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(22,$pa)) return $chprocess($var+3,$pa); else return $chprocess($var,$pa);
	}
	
}

?>
