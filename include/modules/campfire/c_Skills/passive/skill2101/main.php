<?php

namespace skill2101
{
	$phydef_kind = Array(
		'N' => 'P',
		'P' => 'P',
		'K' => 'K',
		'G' => 'G',
		'C' => 'C',
		'B' => 'C',
		'D' => 'D',
		'F' => 'F',
		'J' => 'G'
	);

	function init() 
	{
		define('MOD_SKILL2101_INFO','unique;equip;');
		eval(import_module('clubbase'));
		$clubskillname[2101] = '物甲';
	}
	
	function acquire2101(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost2101(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}

	function check_unlocked2101(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	function get_phy_def_kind()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return Array('P','K','G','C','D','F','A');
	}

	function get_single_phy_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//每级单系防御能够提供的物理减伤效果 伤害会乘以（100-该值）%
		return 19;
	}

	function get_all_phy_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//每级全系防御能够提供的物理减伤效果
		return 11;
	}

	function get_max_phy_def_per()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//最高能叠到多少减伤
		return 92;
	}

	function skill_onload_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skillbase','attrbase','skill2101'));
		//skill_onload_event里只处理由装备提供的临时性抗性 这些临时抗性会在skill_onsave_event时被注销 
		//不过暂时也没别的提供抗性的渠道
		$equiped_def_array = \attrbase\get_ex_def_array($pa, $pa, $active); //好……傻
		$count_equiped_def_arr = array_count_values($equiped_def_array);
		$phy_def_kind = get_phy_def_kind();
		foreach($phy_def_kind as $dk)
		{
			if (in_array($dk, $equiped_def_array))
			{ //存在能提供抗性的装备属性 打入抗性技能里
				if (!\skillbase\skill_query(2101,$pa))	\skillbase\skill_acquire(2101,$pa);
				$single_phy_def_per = get_single_phy_def_per();
				$all_phy_def_per = get_all_phy_def_per();
				$tmp_dk = 'tmp'.$dk;
				$tmp_dk_level = $dk=='A' ? ($count_equiped_def_arr[$dk])*$all_phy_def_per : ($count_equiped_def_arr[$dk])*$single_phy_def_per;
				\skillbase\skill_setvalue(2101,$tmp_dk,$tmp_dk_level,$pa);
				$dk_level = \skillbase\skill_getvalue(2101,$dk,$pa);
				$dk_level += $tmp_dk_level;
				\skillbase\skill_setvalue(2101,$dk,$dk_level,$pa);
			}
		}
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skillbase','skill2101'));
		if (\skillbase\skill_query(2101,$pa))
		{
			$lost_flag = true;
			$phy_def_kind = get_phy_def_kind();
			foreach($phy_def_kind as $dk)
			{
				$tmp_dk = 'tmp'.$dk;
				$tmp_dk_level = \skillbase\skill_getvalue(2101,$tmp_dk,$pa);
				$dk_level = \skillbase\skill_getvalue(2101,$dk,$pa);
				\skillbase\skill_delvalue(2101,$tmp_dk,$pa);
				if($tmp_dk_level < $dk_level)
				{ //该类抗性存在其他来源 只剔除装备提供的部分
					$dk_level -= $tmp_dk_level;
					\skillbase\skill_setvalue(2101,$dk,$dk_level,$pa);
					$lost_flag = false; //保留技能
				}
				else
				{ //该类抗性只依靠装备提供 直接注销
					\skillbase\skill_delvalue(2101,$dk,$pa);
				}
			}
			if($lost_flag) \skillbase\skill_lost(2101,$pa);
		}
		$chprocess($pa);
	}

	//技能“物理护甲”生效时 跳过属性类“物理防御”的判断
	function check_physical_def_attr(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('ex_phy_def','logger','skill2101'));
		$tmp_phy_def_per = 0;
		//获取物甲
		if (\skillbase\skill_query(2101,$pd)) 
		{
			$tmp_wk = $phydef_kind[$pa['wep_kind']];
			if(\skillbase\skill_getvalue(2101,$tmp_wk,$pd) || \skillbase\skill_getvalue(2101,'A',$pd))
			{
				$tmp_phy_def_per = (\skillbase\skill_getvalue(2101,$tmp_wk,$pd))+(\skillbase\skill_getvalue(2101,'A',$pd));
				$max_phy_def_per = get_max_phy_def_per();
				$tmp_phy_def_per = min($max_phy_def_per,$tmp_phy_def_per);//其实应该在载入技能的时候做这个判断……但是有点厘不清了就放这吧。
			}
		}
		//获取破甲
		if($pa['dec_phy_def_per'])
		{
			$tmp_phy_def_per -= $pa['dec_phy_def_per'];
		}
		//物理护甲结果输出
		if($tmp_phy_def_per)
		{
			$d = (100-$tmp_phy_def_per)/100;
			if($tmp_phy_def_per < 0)
			{
				$tmp_phy_def_per = abs($tmp_phy_def_per);
				if ($active)
				$log .= "<span class=\"grey b\">{$pd['name']}受到的物理伤害增加了{$tmp_phy_def_per}%！</span><br>";
			else  $log .= "<span class=\"grey b\">你受到的物理伤害增加了{$tmp_phy_def_per}%！</span><br>";
			}	
			else
			{
				if ($active)
				$log .= "<span class=\"grey b\">{$pd['name']}的物理护甲使你的伤害降低了{$tmp_phy_def_per}%！</span><br>";
			else  $log .= "<span class=\"grey b\">你的物理护甲使{$pa['name']}的伤害降低了{$tmp_phy_def_per}%！</span><br>";
			}
			return Array($d);
		}
		return $chprocess($pa, $pd, $active);
	}
}

?>
