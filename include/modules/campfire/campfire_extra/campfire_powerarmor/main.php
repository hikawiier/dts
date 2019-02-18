<?php

namespace campfire_powerarmor
{
	function init()
	{
		eval(import_module('armor','itemmain'));
		$armor_iteminfo['DBP']='身体装甲';
		$armor_iteminfo['DHP']='头部装甲';
		$armor_iteminfo['DAP']='手部装甲';
		$armor_iteminfo['DFP']='腿部装甲';
		//装甲分为T,S,A,B,C,O六个等级，在类别后面加上对应字母来区分，例如“DBPT”就是T等级的身体装甲
		//没有等级的装甲默认为O等级
		//不同级别的装甲差别主要体现在各个功能的数值上
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
		//先获取装备中的动力装甲信息
		$pa_kind_array = get_pa_kind_array($pd);
		if (sizeof($pa_kind_array))
		{
			$mix_pa_reduce_dmg = 0;
			//最多能抵消多少伤害
			$max_able_reduce_dmg = $pa['dmg_dealt']*($max_pa_reduce_dmg_per/100);
			//开始抵消伤害
			foreach($pa_kind_array as $kind => $lvl)
			{
				//理论上单次装备能抵消的伤害最大值和应消耗的耐久
				$once_pa_reduce_dmg = $max_able_reduce_dmg * ($once_pa_reduce_dmg_per[$lvl]/100);
				$once_pas_cost = $once_pa_reduce_dmg / $once_pas_reduce_dmg[$lvl];
				if($once_pas_cost>=1)
				{
					//只有受到会消耗超过1点耐久的伤害时才会触发动力装甲抵消伤害
					//身体部位的动力甲可以低消耗抵消伤害，放在这里是为了高频率触发
					if($kind=='arb')  $once_pas_cost = $once_pas_cost/$bpa_reduce_pas_cost_per[$lvl];
					//计算实际抵消的伤害和消耗的装甲能量
					$pa_reduce_dmg = $pd[$kind.'s'] > $once_pas_cost ? round($once_pa_reduce_dmg) : round($pd[$kind.'s'] * $once_pas_reduce_dmg[$lvl]);
					$pas_cost = $pd[$kind.'s'] > $once_pas_cost ? round($once_pas_cost) : $pd[$kind.'s'];
					//处理伤害，降低耐久
					$pa['dmg_dealt'] -= $pa_reduce_dmg;
					$mix_pa_reduce_dmg += $pa_reduce_dmg;
					\armor\armor_hurt($pa,$pd,$active,$kind,$pas_cost);
				}	
			}
			//发log
			if($mix_pa_reduce_dmg)
			{
				if ($active)
					$log .= "<span class='yellow'>{$pd['name']}身上的动力装甲抵消了<span class='red'>{$mix_pa_reduce_dmg}</span>点伤害！</span><br>";
				else  $log .= "<span class='yellow'>你身上的动力装甲抵消了<span class='red'>{$mix_pa_reduce_dmg}</span>点伤害！</span><br>";
			}		
		}
	}		
	//动力装甲抵消伤害
	function player_damaged_enemy(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('logger'));
		check_pa_reduce_dmg($pa, $pd, $active);		
		$chprocess($pa, $pd, $active);
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
	function get_hitrate(&$pa,&$pd,$active)		
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
		
	//已废弃，用来获取重复的属性数量
	function get_ex_num_array($ex_array,$ex_nm)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		$ex_array_num = array_count_values($ex_array);
		return $ex_array_num[$ex_nm];
	}
	//已废弃，用来获取某属性都存在于哪些部位的装备上，但是感觉太蠢了
	function get_ex_place(&$pa, &$pd, $active,$ex_nm)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		$ex_aa = Array();
		foreach(array('wep','arb','arh','ara','arf','art') as $armor)
		{
			if($pd[$armor.'s'] && strpos($pd[$armor.'sk'],$ex_nm)!==false)
			{
				array_push($ex_aa,$armor);
			}
		}
		return $ex_aa;
	}
}

?>
