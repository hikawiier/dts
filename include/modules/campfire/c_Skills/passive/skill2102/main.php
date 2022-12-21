<?php

namespace skill2102
{
	function init() 
	{
		define('MOD_SKILL2102_INFO','unique;npcinfo;debuff;');
		eval(import_module('clubbase'));
		$clubskillname[2102] = '碎甲';
	}
	
	function acquire2102(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost2102(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_delvalue(2102,'start',$pd);
		\skillbase\skill_delvalue(2102,'end',$pd);
		\skillbase\skill_delvalue(2102,'debuff',$pd);
	}

	function check_unlocked2102(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	function check_skill2102_state(&$pa){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','skill2102'));
		if (!\skillbase\skill_query(2102,$pa)) return 0;
		$e=\skillbase\skill_getvalue(2102,'end',$pa);
		if ($now<$e) return 1;
		return 0;
	}

	function skill2102_debuff_levelup(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		//碎甲状态加深
		$levelup_debuff_per = \skillbase\skill_getvalue(2102,'debuff',$pa)+get_debuff_def_per();
		$levelup_debuff_per = min(get_max_debuff_def_per(),$levelup_debuff_per);
		\skillbase\skill_setvalue(2102,'debuff',$levelup_debuff_per,$pa);
		skill2102_debuff_lasttime_reset($pa);
	}

	function skill2102_debuff_lasttime_reset(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		$tmp_lasttime = get_skill2102_lasttime();
		$tmp_now = $now;
		\skillbase\skill_setvalue(2102,'start',$tmp_now,$pa);
		\skillbase\skill_setvalue(2102,'end',$tmp_now+$tmp_lasttime,$pa);
	}

	function get_skill2102_lastturn()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//碎甲触发时持续的战斗轮
		//废弃 如果需要以战斗轮形式显示碎甲 最好是另开一个技能 将时效性的DEBUFF和轮次性的DEBUFF区分开
	}

	function get_skill2102_lasttime()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//碎甲触发时持续时间 单位：秒
		return 61;
	}

	function get_max_debuff_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//破甲最多能叠到多少 默认是50
		return 51;
	}

	function get_debuff_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//每次施加异常状态会降低多少全系物理护甲
		return 15;
	}

	//战前清空计数器 检查debuff状态
	function strike_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$pa['dec_phy_def_per'] =  0;
		eval(import_module('skill2102'));
		if(\skillbase\skill_query(2102,$pd) && !check_skill2102_state($pd))
		{
			//eval(import_module('logger'));
			//$log .= $pd['name']."从碎甲状态中恢复了！<br>";
			\skillbase\skill_lost(2102,$pd);
		}
		$chprocess($pa, $pd, $active);
	}

	//物穿的判定是在计算物理伤害之前
	function calculate_physical_dmg(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if($pa['physical_pierce_success'])
		{ //贯穿生效，附加碎甲状态
			if(!\skillbase\skill_query(2102,$pd))	\skillbase\skill_acquire(2102,$pd);
			//碎甲状态加深
			\skill2102\skill2102_debuff_levelup($pd);
			//eval(import_module('logger'));
			//$log .= $pd['name']."被碎甲了！<br>";
		}
		return $chprocess($pa, $pd, $active);
	}

	//对手身上存在物弱状态时，影响物防的判定过程
	function check_physical_def_attr(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('ex_phy_def','logger','skill2101','skill2102'));
		if (\skillbase\skill_query(2102,$pd)) 
		{
			$tmp_dec_phy_def_per = \skillbase\skill_getvalue(2102,'debuff',$pd);
			$pa['dec_phy_def_per'] = $tmp_dec_phy_def_per;
			if ($active)
				$log .= "<span class=\"grey b\">{$pd['name']}处于碎甲状态，物理护甲降低了{$tmp_dec_phy_def_per}%！</span><br>";
			else  $log .= "<span class=\"grey b\">你处于碎甲状态，物理护甲降低了{$tmp_dec_phy_def_per}%！</span><br>";
		}
		return $chprocess($pa, $pd, $active);
	}

	function bufficons_list()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		\player\update_sdata();
		if (\skillbase\skill_query(2102,$sdata))
		{
			eval(import_module('skill2102','skillbase'));
			$skill2102_start = (int)\skillbase\skill_getvalue(2102,'start'); 
			$skill2102_end = (int)\skillbase\skill_getvalue(2102,'end'); 
			$skill2102_effect = \skillbase\skill_getvalue(2102,'debuff'); 
			$z=Array(
				'disappear' => 1,
				'clickable' => 0,
				'hint' => '状态「碎甲」<br>物理护甲下降'.$skill2102_effect.'%',
			);
			if ($now<$skill2102_end)
			{
				$z['style']=1;
				$z['totsec']=$skill2102_end-$skill2102_start;
				$z['nowsec']=$now-$skill2102_start;
			}
			else 
			{
				$z['style']=4;
			}
			\bufficons\bufficon_show('img/skill2102.gif',$z);
		}
		$chprocess();
	}
}

?>
