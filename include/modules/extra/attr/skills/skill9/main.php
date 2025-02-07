<?php

namespace skill9
{
	function init() 
	{
		eval(import_module('wound'));
		//受伤状态简称（用于profile显示）
		$infinfo['w'] = '<span class="grey b">乱</span>';
		//受伤状态名称动词
		$infname['w'] = '<span class="grey b">混乱</span>';
		//受伤状态对应的特效技能编号
		$infskillinfo['w'] = 9;
	}
	
	function acquire9(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\c_battle\set_inf_skills_value($pa,'w');
	}
	
	function lost9(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\c_battle\del_inf_skills_value($pa,'w');
	}
	
	function skill_onload_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (strpos($pa['inf'],'w')!==false && !\skillbase\skill_query(9,$pa)) \skillbase\skill_acquire(9,$pa);
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (strpos($pa['inf'],'w')===false && \skillbase\skill_query(9,$pa)) \skillbase\skill_lost(9,$pa);
		$chprocess($pa);
	}

	function search_area()	//探索时减少异常状态持续时间
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		if (\skillbase\skill_query(9))
		{
			\c_battle\change_inf_turns($sdata,'w');
		}
		$chprocess();
	}
	
	function move_to_area($moveto)	//移动时减少异常状态持续时间
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		if (\skillbase\skill_query(9))
		{
			\c_battle\change_inf_turns($sdata,'w');
		}
		$chprocess($moveto);
	}

	function change_battle_turns_events(&$pa, &$pd, $active) //战斗中在战斗轮步进阶段减少异常状态持续时间
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		if (\skillbase\skill_query(9,$pa)) \c_battle\change_inf_turns($pa,'w');
	}
	
	function get_def_multiplier(&$pa,&$pd,$active)	//混乱防御力降低
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa,$pd,$active);
		if (\skillbase\skill_query(9,$pd)) {
			$var = 0.7;
			array_unshift($ret, $var);
		}
		return $ret;
	}
	
	function calculate_active_obbs_multiplier(&$ldata,&$edata)	//混乱先攻率降低（但出于对原版本的兼容，对手冻结不会增加你的先攻率，不然NPC要哭了）
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$var = 1;
		if (\skillbase\skill_query(9,$ldata)) {
			$var = 0.8;
			$ldata['active_words'] = \attack\multiply_format($var, $ldata['active_words'],0);
		}
		return $chprocess($ldata,$edata)*$var;
	}
	
	function calculate_counter_rate_multiplier(&$pa, &$pd, $active)	//混乱反击率降低
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa,$pd,$active);
		if (\skillbase\skill_query(9,$pa)) 
			return $ret*0.8;
		else  return $ret;
	}
}

?>
