<?php

namespace skill514
{
	function init() 
	{
		define('MOD_SKILL514_INFO','card;unique;');
		eval(import_module('clubbase'));
		$clubskillname[514] = '妄击';
	}
	
	function acquire514(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(514,'var','10',$pa);//单位是千分之一
	}
	
	function lost514(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_delvalue(514,'var',$pa);
	}
	
	function check_unlocked514(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	//被命中率不会大于那个值
	function get_hitrate_change(&$pa,&$pd,$active,$hitrate)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $pd, $active, $hitrate);
		if (!\skillbase\skill_query(514, $pd)) return $ret;
		$skill514_var = \skillbase\skill_getvalue(514,'var',$pd)/10;
		return min($ret, $skill514_var);
	}
	
	//攻击结束时如果命中则做那个值翻倍
	function attack_finish(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa,$pd,$active);
		if(\skillbase\skill_query(514,$pa) && !empty($pa['is_hit']))
		{
			$skill514_var = (int)\skillbase\skill_getvalue(514,'var',$pa);
			$skill514_var = min(1000, max(1, $skill514_var * 2));
			\skillbase\skill_setvalue(514,'var',$skill514_var,$pa);
		}
	}
}

?>