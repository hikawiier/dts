<?php

namespace skill1900
{	
	function init() 
	{
		define('MOD_SKILL1900_INFO','card;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1900] = '唯心';
	}
	
	function acquire1900(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost1900(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1900(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	//贯穿触发率
	function get_ex_pierce_proc_rate(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $pd, $active);
		//被攻击者拥有唯心技能，提高攻击者的贯穿率
		if(\skillbase\skill_query(1900,$pd) && \skill1900\check_unlocked1900($pd)) {
			$ret *= 2;
		//攻击者拥有唯心技能，提高攻击者的贯穿率	
		}elseif(\skillbase\skill_query(1900,$pa) && \skill1900\check_unlocked1900($pa)) {
			$ret *= 4;
		}
		return $ret;
	}
	
	//属穿触发率
	function get_attr_pierce_proc_rate(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $pd, $active);
		//被攻击者拥有唯心技能，提高攻击者的属穿率
		if(\skillbase\skill_query(1900,$pd) && \skill1900\check_unlocked1900($pd)) {
			$ret *= 2;
		//攻击者拥有唯心技能，提高攻击者的属穿率	
		}elseif(\skillbase\skill_query(1900,$pa) && \skill1900\check_unlocked1900($pa)) {
			$ret *= 4;
		}
		return $ret;
	}
	
	function get_ex_dmg_def_proc_rate(&$pa, &$pd, $active, $key)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $pd, $active, $key);
		//被攻击者拥有唯心技能，其属性防御失效率变高
		if(\skillbase\skill_query(1900,$pd) && \skill1900\check_unlocked1900($pd)) {
			$ret = 200-$ret*2;
		//攻击者拥有唯心技能，其属性防御失效率变高
		}elseif(\skillbase\skill_query(1900,$pa) && \skill1900\check_unlocked1900($pa)) {
			$ret = 200-$ret*4;
		}
		return $ret;
	}
	
	function get_ex_phy_def_proc_rate(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $pd, $active);
		//被攻击者拥有唯心技能，其物理防御失效率变高
		if(\skillbase\skill_query(1900,$pd) && \skill1900\check_unlocked1900($pd)) {
			$ret = 200-$ret*2;
		//攻击者拥有唯心技能，其物理防御失效率变高
		}elseif(\skillbase\skill_query(1900,$pa) && \skill1900\check_unlocked1900($pa)) {
			$ret = 200-$ret*4;
		}
		return $ret;
	}
	
	function get_ex_phy_nullify_proc_rate(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $pd, $active);
		//被攻击者拥有唯心技能，其物抹失效率变高
		if(\skillbase\skill_query(1900,$pd) && \skill1900\check_unlocked1900($pd)) {
			$ret = 200-$ret*2;
		//攻击者拥有唯心技能，其物抹失效率变高
		}elseif(\skillbase\skill_query(1900,$pa) && \skill1900\check_unlocked1900($pa)) {
			$ret = 200-$ret*4;
		}
		return $ret;
	}
	
	function get_ex_dmg_nullify_proc_rate(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $pd, $active);
		//被攻击者拥有唯心技能，其属抹失效率变高
		if(\skillbase\skill_query(1900,$pd) && \skill1900\check_unlocked1900($pd)) {
			$ret = 200-$ret*2;
		//攻击者拥有唯心技能，其属抹失效率变高
		}elseif(\skillbase\skill_query(1900,$pa) && \skill1900\check_unlocked1900($pa)) {
			$ret = 200-$ret*4;
		}
		return $ret;
	}	
}

?>