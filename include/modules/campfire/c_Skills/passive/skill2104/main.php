<?php

namespace skill2104
{
	function init() 
	{
		define('MOD_SKILL2104_INFO','unique;npcinfo;debuff;');
		eval(import_module('clubbase'));
		$clubskillname[2104] = '浊心';
	}
	
	function acquire2104(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost2104(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill2103'));
		$ex_def_kind = \skill2103\get_ex_def_kind();
		foreach($ex_def_kind as $ex)
		{
			\skillbase\skill_delvalue(2104,$ex,$pd);
			//$ex_start = $ex.'start';
			//$ex_end = $ex.'end';
			//\skillbase\skill_delvalue(2104,$ex_start,$pd);
			//\skillbase\skill_delvalue(2104,$ex_end,$pd);
		}
		\skillbase\skill_delvalue(2104,'start',$pd);
		\skillbase\skill_delvalue(2104,'end',$pd);
	}

	function check_unlocked2104(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	function check_skill2104_state(&$pa){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','skill2104'));
		if (!\skillbase\skill_query(2104,$pa)) return 0;
		//$ex_end = $ex.'end';
		$e=\skillbase\skill_getvalue(2104,'end',$pa);
		if ($now<$e) return 1;
		return 0;
	}

	function skill2104_debuff_levelup(&$pa,$ex)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		//浊心状态加深
		$levelup_debuff_per = \skillbase\skill_getvalue(2104,$ex,$pa)+get_ex_debuff_def_per();
		$levelup_debuff_per = min(get_max_ex_debuff_def_per(),$levelup_debuff_per);
		\skillbase\skill_setvalue(2104,$ex,$levelup_debuff_per,$pa);
		skill2104_debuff_lasttime_reset($pa,$ex);
	}

	function skill2104_debuff_lasttime_reset(&$pa,$ex)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','skill2104'));
		//$ex_start = $ex.'start';
		//$ex_end = $ex.'end';
		$tmp_lasttime = get_skill2104_lasttime();
		$tmp_now = $now;
		//\skillbase\skill_setvalue(2104,$ex_start,$tmp_now,$pa);
		//\skillbase\skill_setvalue(2104,$ex_end,$tmp_now+$tmp_lasttime,$pa);
		\skillbase\skill_setvalue(2104,'start',$tmp_now,$pa);
		\skillbase\skill_setvalue(2104,'end',$tmp_now+$tmp_lasttime,$pa);
	}

	function get_skill2104_lasttime()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//浊心触发时持续时间 单位：秒
		return 61;
	}

	function get_max_ex_debuff_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//减抗效果最高能叠到多少 默认是50
		return 51;
	}

	function get_ex_debuff_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//每次施加异常状态会降低多少对应属性抗性
		return 16;
	}

	//战前清空计数器
	function strike_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('ex_dmg_att','logger','skill2104'));
		foreach ( $ex_attack_list as $key ) $pd['dec_ex_'.$key.'_def_per'] = 0;
		if(\skillbase\skill_query(2104,$pd) && !check_skill2104_state($pd))
		{
			$log .= $pd['name']."从浊心状态中恢复了！<br>";
			\skillbase\skill_lost(2104,$pd);
			return $chprocess($pa, $pd, $active, $key);
		}
		$chprocess($pa, $pd, $active);
	}	

	//对手身上存在浊心状态时，影响属防的判定过程
	function check_ex_single_dmg_def_attr(&$pa, &$pd, $active, $key)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('ex_dmg_att','ex_dmg_def','skill2103','logger'));
		//属穿的部分有点麻烦 要先确认会造成属性伤害才会继续下一步 所以是否成功浊心就在这里处理
		if ($pa['attr_pierce_success'])
		{ //属穿成功，浊心状态加深 属穿默认提供的来源为全属性抗性穿透
			if(!\skillbase\skill_query(2104,$pd))	\skillbase\skill_acquire(2104,$pd);
			//浊心状态加深 只会降低成功打出来的属性的对应抗性
			\skill2104\skill2104_debuff_levelup($pd,$exdef_kind[$key]); 
			//eval(import_module('logger'));
			//$log .= $pd['name']."对".$exdef_kind[$key]."的抗性下降了！<br>";
		}
		if (\skillbase\skill_query(2104,$pd)) 
		{
			$tmp_dec_ex_def_per = \skillbase\skill_getvalue(2104,$exdef_kind[$key],$pd)+\skillbase\skill_getvalue(2104,'a',$pd);
			$pa['dec_ex_'.$key.'_def_per'] = $tmp_dec_ex_def_per;
			/*if ($active)
				$log .= "<span class=\"grey b\">{$pd['name']}的精神遭到污染，对{$exdmgname[$key]}的抗性降低了{$tmp_dec_ex_def_per}%！</span><br>";
			else  $log .= "<span class=\"grey b\">你的精神遭到污染，对{$exdmgname[$key]}的抗性降低了{$tmp_dec_ex_def_per}%！</span><br>";*/
		}
		return $chprocess($pa, $pd, $active, $key);
	}

	function bufficons_list()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		\player\update_sdata();
		if (\skillbase\skill_query(2104,$sdata))
		{
			eval(import_module('skill2104','skillbase'));
			$skill2104_start = (int)\skillbase\skill_getvalue(2104,'start'); 
			$skill2104_end = (int)\skillbase\skill_getvalue(2104,'end'); 
			$z=Array(
				'disappear' => 1,
				'clickable' => 0,
				'hint' => '状态「浊心」<br>对特定属性的抗性下降',
			);
			if ($now<$skill2104_end)
			{
				$z['style']=1;
				$z['totsec']=$skill2104_end-$skill2104_start;
				$z['nowsec']=$now-$skill2104_start;
			}
			else 
			{
				$z['style']=4;
			}
			\bufficons\bufficon_show('img/skill2104.gif',$z);
		}
		$chprocess();
	}
}

?>
