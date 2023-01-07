<?php

namespace skill2103
{
	//各类攻击属性对应的防御列表
	//属性防御的作用是防御所有在下列列表中的属性
	$exdef_kind = Array(
		'p' => 'q',
		'u' => 'U',
		'f' => 'U', //灼焰
		'i' => 'I',
		'k' => 'I', //冰华
		'e' => 'E',
		't' => 'W', //音爆
		'w' => 'W',
		'd' => 'D',
	);

	function init() 
	{
		define('MOD_SKILL2103_INFO','unique;equip;');
		eval(import_module('clubbase'));
		$clubskillname[2103] = '属抗';
	}
	
	function acquire2103(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost2103(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}

	function check_unlocked2103(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	function get_ex_def_kind()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return Array('q','U','I','E','W','D','a');
	}

	function get_single_ex_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//每级单系属防能够提供的属性减伤效果 伤害会乘以（100-该值）%
		return 26;
	}

	function get_all_ex_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//每级全系属防能够提供的属性减伤效果
		return 16;
	}

	function get_max_ex_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//最高能叠到多少减伤
		return 92;
	}

	function skill_onload_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skillbase','attrbase','skill2103'));
		//skill_onload_event里只处理由装备提供的临时性抗性 这些临时抗性会在skill_onsave_event时被注销 
		//不过暂时也没别的提供抗性的渠道
		$equiped_def_array = \attrbase\get_ex_def_array($pa, $pa, $active); //好……傻
		$count_equiped_def_arr = array_count_values($equiped_def_array);
		$ex_def_kind = get_ex_def_kind();
		foreach($ex_def_kind as $ek)
		{
			if (in_array($ek, $equiped_def_array))
			{ //存在能提供抗性的装备属性 打入抗性技能里
				if (!\skillbase\skill_query(2103,$pa))	\skillbase\skill_acquire(2103,$pa);
				$single_ex_def_per = get_single_ex_def_per();
				$all_ex_def_per = get_all_ex_def_per();
				$tmp_ek = 'tmp'.$ek;
				$tmp_ek_level = $ek=='a' ? ($count_equiped_def_arr[$ek])*$all_ex_def_per : ($count_equiped_def_arr[$ek])*$single_ex_def_per;
				\skillbase\skill_setvalue(2103,$tmp_ek,$tmp_ek_level,$pa);
				$ek_level = \skillbase\skill_getvalue(2103,$ek,$pa);
				$ek_level += $tmp_ek_level;
				\skillbase\skill_setvalue(2103,$ek,$ek_level,$pa);
			}
		}
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skillbase','skill2103'));
		if (\skillbase\skill_query(2103,$pa))
		{
			$lost_flag = true;
			$ex_def_kind = get_ex_def_kind();
			foreach($ex_def_kind as $ek)
			{
				$tmp_ek = 'tmp'.$ek;
				$tmp_ek_level = \skillbase\skill_getvalue(2103,$tmp_ek,$pa);
				$ek_level = \skillbase\skill_getvalue(2103,$ek,$pa);
				\skillbase\skill_delvalue(2103,$tmp_ek,$pa);
				if($tmp_ek_level < $ek_level)
				{ //该类抗性存在其他来源 只剔除装备提供的部分
					$ek_level -= $tmp_ek_level;
					\skillbase\skill_setvalue(2103,$ek,$ek_level,$pa);
					$lost_flag = false; //保留技能
				}
				else
				{ //该类抗性只依靠装备提供 直接注销
					\skillbase\skill_delvalue(2103,$ek,$pa);
				}
			}
			if($lost_flag) \skillbase\skill_lost(2103,$pa);
		}
		$chprocess($pa);
	}

	//技能“属抗”生效时 跳过原有的属性防御判定流程
	function check_ex_single_dmg_def_attr(&$pa, &$pd, $active, $key)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('ex_dmg_att','ex_dmg_def','skill2103','logger'));
		$tmp_ex_def_per = 0;
		//获取抗性
		if (\skillbase\skill_query(2103,$pd)) 
		{
			$tmp_ek = $exdef_kind[$key];
			if(\skillbase\skill_getvalue(2103,$tmp_ek,$pd) || \skillbase\skill_getvalue(2103,'a',$pd))
			{
				$tmp_ex_def_per = (\skillbase\skill_getvalue(2103,$tmp_ek,$pd))+(\skillbase\skill_getvalue(2103,'a',$pd));
				$max_ex_def_per = get_max_ex_def_per();
				$tmp_ex_def_per = min($max_ex_def_per,$tmp_ex_def_per);
			}
		}
		//获取减抗
		if($pa['dec_ex_'.$key.'_def_per'])
		{
			$tmp_ex_def_per -= $pa['dec_ex_'.$key.'_def_per'];
		}
		//属性抗性结果输出
		if($tmp_ex_def_per) 
		{
			$r = (100-$tmp_ex_def_per)/100;
			/*if($tmp_ex_def_per < 0)
			{
				$tmp_ex_def_per = abs($tmp_ex_def_per);
				$log .= "{$exdmgname[$key]}的伤害增加了<span class=\"b\">{$tmp_ex_def_per}%</span>！";
			}	
			else
			{
				$log .= "{$exdmgname[$key]}的伤害降低了<span class=\"b\">{$tmp_ex_def_per}%</span>！";
			}*/
			return $r;
		}
		return $chprocess($pa, $pd, $active, $key);
	}
}

?>
