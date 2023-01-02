<?php

namespace c_battle
{
	///战斗距离、轮次相关

	//根据武器射程对比初始化战斗距离
	function get_battle_range(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','weapon'));
		$r1 = \weapon\get_weapon_range($pa, $active);
		$r2 = \weapon\get_weapon_range($pd, 1-$active);
		$r = max(0,$r1-$r2);
		if($r1 === 0)
		{
			//先制者武器为爆炸物，距离值恒定为+2
			$r = 2;			
		}
		return $r;
	}

	//距离、轮次初始化
	function rs_battle_range_and_turns(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//从追击来源发起的战斗 不进行初始化
		if(strpos($pa['action'],'chase')!==false || strpos($pd['action'],'chase')!==false)	return;

		$pa['battle_range'] = get_battle_range($pa, $pd, $active);
		$pd['battle_range'] = 0-$pa['battle_range'];
		$pd['battle_turns'] = $pa['battle_turns'] = 0;

		eval(import_module('logger'));
		$log .="【DEBUG】距离与轮次初始化完成。<br>";
	}

	//战斗距离步进
	function change_battle_range(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		//距离为0情况下不会改变战斗距离
		if(!$pa['battle_range'] || !$pd['battle_range']) return;
		//反击不会改变战斗距离
		//if($pa['is_counter']) return; 反击不会不会改变战斗距离 因为存在射程还能反击的情况只有技能或者换了武器 这时候要加速双方战斗进度

		$pa['battle_range'] = $pa['battle_range']>0 ? max(0,$pa['battle_range']-1) : min(0,$pa['battle_range']+1) ;
		$pd['battle_range'] = 0-$pa['battle_range'];

		//改变战斗距离时应用距离事件
		change_battle_range_events($pa, $pd, $active);

		eval(import_module('logger'));
		$log .= "【DEBUG】距离步进，分别为{$pa['battle_range']}与{$pd['battle_range']}。<br>";
	}

	//改变战斗距离后的事件
	function change_battle_range_events(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		//战斗踩雷 遵照原版探索遇雷概率算法
		eval(import_module('trap'));
		$trap_dice=rand(0,99);
		//$trap_dice=0;
		if($trap_dice < $trap_max_obbs) calculate_in_battle_trap_obbs($pa,$pd,$active);
	}

	//战斗轮次步进
	function change_battle_turns(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		$pa['battle_turns']++;
		$pd['battle_turns']=$pa['battle_turns'];
		eval(import_module('logger'));
		$log .="【DEBUG】战斗轮增加了一回合。<br>";
	}
}

?>