<?php

namespace campfire_powerarmor
{
	function init()
	{
		eval(import_module('armor','itemmain'));
		//装甲分为T,S,A,B,C,O六个等级，在类别后面加上对应字母来区分，例如“DBPT”就是T等级的身体装甲
		//没有等级的装甲默认为O等级
		//不同级别的装甲差别主要体现在各个功能的数值上
		/*
		头部装甲：增加你的先攻率（仅在主动攻击时生效）、遇敌率（作战、偷袭、强袭姿态下）以及道具发现率（探索姿态下）
		身体装甲：获得技能【护盾】（为你提供额外的减伤效果与护盾值，主动发动，发动时无法卸下装备），
				  在同时装备身体装甲与头部装甲、且装甲未被破坏时，你不会受到致死伤害。
		手部装甲：增加你的命中率，并额外附加取决于防具效果的电气伤害；
		腿部装甲：降低你移动和探索时消耗的体力，降低敌人的命中率，增加你的回避率。
		*/
		
		//防具上的属性仅仅用作识别，不具有实际效果
		$itemspkinfo['^01099'] = '<span class="gold b">装甲</span>';		
		$itemspkinfo['^01098'] = '<span class="purple b">装甲</span>';	
		$itemspkinfo['^01097'] = '<span class="lightblue b">装甲</span>';
		$itemspkinfo['^01096'] = '<span class="evergreen b">装甲</span>';	
		$itemspkinfo['^01095'] = '<span class="b">装甲</span>';	
		$itemspkinfo['^01094'] = '<span class="grey b">装甲</span>';	
	}
	//获取装备中的动力装甲信息（任意对象）
	function get_pa_kind_array($pad)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		$pa_kind_array = Array();
		foreach(array('arb','arh','ara','arf') as $armor)
		{
			if($pad[$armor.'s']!=='∞' && strpos($pad[$armor.'k'],'P')!==false)
			{
				$pa_kind = $pad[$armor.'k'];
				//获取动力甲等级
				$pa_lvl = substr($pa_kind,3) ? substr($pa_kind,3) : 'O';
				$pa_kind_array[$armor] = $pa_lvl;
			}
		}
		return $pa_kind_array;
	}
	//获取单个部位动力装甲信息（仅自己）
	function get_once_pa_kind_null($pa_kind)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('player'));
		$pa_confirm_flag = false;
		foreach(array('arb','arh','ara','arf') as $armor)
		{
			if(${$armor.'s'}!=='∞' && strpos(${$armor.'k'},'P')!==false && $armor==$pa_kind)
			{
				$pa_confirm_flag = substr(${$armor.'k'},3) ? substr(${$armor.'k'},3) : 'O';
				break;
			}
		}
		return $pa_confirm_flag;
	}
	//获取单个部位动力装甲信息（任意对象）
	function get_once_pa_kind_data($pa_kind,$pad)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;			
		$pa_confirm_flag = false;
		foreach(array('arb','arh','ara','arf') as $armor)
		{
			if($pad[$armor.'s']!=='∞' && strpos($pad[$armor.'k'],'P')!==false && $armor==$pa_kind)
			{
				$pa_confirm_flag = substr($pad[$armor.'k'],3) ? substr($pad[$armor.'k'],3) : 'O';
				break;
			}
		}
		return $pa_confirm_flag;
	}
	function check_pa_reduce_dmg(&$pa, &$pd, $active)
	{
		//动力装甲抵消伤害,判定减伤的过程
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('logger','armor','campfire_powerarmor'));
		//先获取装备中的动力装甲信息，这里的$pa_kind指的是powerarmor_kind……卧槽这是谁想出来的缩写
		$pa_kind_array = get_pa_kind_array($pd);
		if($pa_kind_array)
		{
			$AllparReduceDmg = 0;
			//每个部分的分别判定开始
			foreach($pa_kind_array as $kind => $lvl)
			{
				//该部位的装甲每点耐久可以抵消多少伤害
				$par_sReduceDmg = $pd[$kind.'e']*($once_pas_reduce_dmg[$lvl]/100);
				//该部位的装甲 不考虑耐久 理论最大减伤值
				$parReduceDmgTH = $pa['dmg_dealt'] * ($once_pa_reduce_dmg_per[$lvl]/100);
				//该部位的装甲 实际可以提供的减伤值
				$par_S = $kind=='arb' ? $pd[$kind.'s'] * $bpa_reduce_pas_cost_per[$lvl] : $pd[$kind.'s'];
				$parReduceDmgRA = $par_S * $par_sReduceDmg;
				//实际减伤大于理论减伤时 插一个过量防御的旗 没有这个旗的情况下 等于装备的耐久值不够或者正好
				if($parReduceDmgRA > $parReduceDmgTH) $overReduceDmgFlag=true;
				//最终减伤数值
				$parReduceDmg = $overReduceDmgFlag ? $parReduceDmgTH : $parReduceDmgRA;
				//最终削减防具耐久数值，只有减伤数值大于每点耐久可抵消的伤害值时才会判断
				$parReduce_s = 0;
				if($parReduceDmg > $par_sReduceDmg) $parReduce_s = $overReduceDmgFlag ? round($parReduceDmg/$par_sReduceDmg) : $pd[$kind.'s'];
				if($kind=='arb') $parReduce_s = round($parReduce_s/$bpa_reduce_pas_cost_per[$lvl]); 
				//单次处理减伤事件所造成的耐久降低不会超过 100 点
				$parReduce_s = min(100,$parReduce_s);
				//处理防具耐久降低的事件
				if($parReduce_s) \armor\armor_hurt($pa,$pd,$active,$kind,$parReduce_s);
				//累加减伤数值
				$AllparReduceDmg += $parReduceDmg;
			}
			//装甲系统最高能够提供的减伤数值
			$AllparReduceDmgMax = $pa['dmg_dealt']*($max_pa_reduce_dmg_per/100);
			$AllparReduceDmg = min($AllparReduceDmg,$AllparReduceDmgMax);
			$pa['dmg_dealt'] -= $AllparReduceDmg;
			//发log
			if($AllparReduceDmg)
			{
				if ($active)
					$log .= "<span class='brickred b'>{$pd['name']}所穿戴的装甲为其抵消了<span class='brickred b'>{$AllparReduceDmg}点伤害！<br>";
				else  $log .= "<span class='brickred b'>你穿戴的装甲为你抵消了<span class='brickred b'>{$AllparReduceDmg}点伤害！<br>";
			}
		}
	}	
	
	function apply_total_damage_modifier_limit(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		check_pa_reduce_dmg($pa, $pd, $active);
	}
	
	//头部动力装甲增加发现率/先攻率/道具发现率
	function calculate_active_obbs_multiplier(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('campfire_powerarmor'));
		//作战、强袭、偷袭姿态时增加自己主动攻击时的先攻率
		$pa_confirm_flag = get_once_pa_kind_data('arh',$ldata);
		if ($pa_confirm_flag && ($ldata['pose']==1 || $ldata['pose']==2 || $ldata['pose']==4))			
			return $chprocess($ldata,$edata)*$hpa_add_acitve_obbs[$pa_confirm_flag];
		else  return $chprocess($ldata,$edata);
	}
	function calculate_meetman_rate($schmode)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('campfire_powerarmor'));
		//作战、强袭、偷袭姿态时增加遇敌率
		$pa_confirm_flag = get_once_pa_kind_null('arh');
		if ($pa_confirm_flag && ($pose==1 || $pose==2 || $pose==4)) 
			return $chprocess($schmode)*$hpa_add_metman_obbs[$pa_confirm_flag];
		else  return $chprocess($schmode);
	}
	function calculate_itemfind_obbs_multiplier()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('campfire_powerarmor'));
		//探索姿态时增加道具发现率
		$pa_confirm_flag = get_once_pa_kind_null('arh');
		if ($pa_confirm_flag && $pose==3) 
			return $chprocess()*$hpa_add_metman_obbs[$pa_confirm_flag];
		else  return $chprocess();
	}
	//手部动力装甲增加命中率
	function get_hitrate_multiplier(&$pa,&$pd,$active)		
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('campfire_powerarmor'));
		$pa_confirm_flag = get_once_pa_kind_data('ara',$pa);
		if ($pa_confirm_flag)			
			return $chprocess($pa,$pd,$active)*$apa_add_hitrate_obbs[$pa_confirm_flag];
		else  return $chprocess($pa,$pd,$active);
	}
	//腿部动力装甲减少移动探索体力消耗
	function calculate_search_sp_cost()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('campfire_powerarmor'));
		$pa_confirm_flag = get_once_pa_kind_null('arf');
		if ($pa_confirm_flag) 
			return $chprocess()-$fpa_reduce_move_cost[$pa_confirm_flag];
		else  return $chprocess();
	}
	function calculate_move_sp_cost()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('campfire_powerarmor'));
		$pa_confirm_flag = get_once_pa_kind_null('arf');
		if ($pa_confirm_flag) 
			return $chprocess()-$fpa_reduce_explore_cost[$pa_confirm_flag];
		else  return $chprocess();
	}
}

?>
