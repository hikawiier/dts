<?php

namespace poweredarmor
{
	function init()
	{
		eval(import_module('armor','itemmain'));
		$armor_iteminfo['DBP']='身体装甲';
		$armor_iteminfo['DHP']='头部装甲';
		$armor_iteminfo['DAP']='手部装甲';
		$armor_iteminfo['DFP']='腿部装甲';
		//装甲分级暂时没有实装，只是一个预留的位置
		//不同级别的装甲差别主要体现在各个功能的数值上
	}
	function check_dmg_pbarmor_attr(&$pa, &$pd, $active)
	{
		//动力装甲抵消伤害
		//每件动力装甲带来的减伤比例
		$ref_per = 25;
		//最高减伤
		$max_ref_per = 99;
		//每点耐久可以抵消多少点伤害
		$itms_ref_per = 10;
		//身体防具的抵消加成倍率
		$body_itms_ref_per = 2;
		//判定减伤的过程
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('logger','armor'));
		$e_pb_array = get_ex_pb_array($pa, $pd, $active);
		$e_pb_num = sizeof($e_pb_array);
		if (($e_pb_num) && $pa['dmg_dealt']>($itms_ref_per*$e_pb_num))
		{
			$ap_dmg_ref_per = min($max_ref_per,$e_pb_num*$ref_per);
			$plus_ref_dmg = $pa['dmg_dealt']*($ap_dmg_ref_per/100);
			$one_ref_dmg = $plus_ref_dmg/$e_pb_num;
			$one_reduce_itms = $one_ref_dmg/$itms_ref_per;
			foreach($e_pb_array as $pea)
			{
				$ors = $pea=='arb' ? max(1,round($one_reduce_itms/$body_itms_ref_per)) : max(1,round($one_reduce_itms));
				if($pd[$pea.'s']>$ors)
				{
					$ord = round($one_ref_dmg);				
				}
				else
				{
					$ors = round($pd[$pea.'s']);	
					$ord = round($pd[$pea.'s']*$itms_ref_per);
				}		
				if ($active)
						$log .= "{$pd['name']}的{$pd[$pea]}抵消了<span class='red'>{$ord}</span>点伤害！<br>";
					else  $log .= "你的{$pd[$pea]}抵消了<span class='red'>{$ord}</span>点伤害！<br>";
				$pa['dmg_dealt'] -= $ord;
				\armor\armor_hurt($pa, $pd, $active, $pea,$ors);
			}
		}
	}
	//获取装备中的动力装甲
	function get_ex_pb_array(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		$ex_powbody_array = Array();
		foreach(array('arb','arh','ara','arf') as $armor)
		{
			if($pd[$armor.'s']!=='∞' && strpos($pd[$armor.'k'],'P')!==false)
			{
				array_push($ex_powbody_array,$armor);
			}
		}
		return $ex_powbody_array;
	}		
	//动力装甲抵消伤害
	function player_damaged_enemy(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('logger'));
		check_dmg_pbarmor_attr($pa, $pd, $active);		
		$chprocess($pa, $pd, $active);
	}
	//头部动力装甲增加发现率/先攻率/道具发现率
	function calculate_active_obbs_multiplier(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//作战、强袭、偷袭姿态时增加自己主动攻击时的先攻率
		if ($ldata['arhk']=='DHP' && $ldata['arhs']!=='∞' && ($ldata['pose']==1 || $ldata['pose']==2 || $ldata['pose']==4))			
			return $chprocess($ldata,$edata)*1.15;
		else  return $chprocess($ldata,$edata);
	}
	function calculate_meetman_rate($schmode)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//作战、强袭、偷袭姿态时增加遇敌率
		if ($arhk=='DHP' && $arfs!=='∞' && ($pose==1 || $pose==2 || $pose==4)) 
			return 1.2*$chprocess($schmode);
		else  return $chprocess($schmode);
	}
	function calculate_itemfind_obbs_multiplier()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//探索姿态时增加道具发现率
		if ($arhk=='DHP' && $arfs!=='∞' && $pose==3) 
			return 1.2*$chprocess();
		else  return $chprocess();
	}
	//手部动力装甲增加命中率
	function get_hitrate(&$pa,&$pd,$active)		
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($pa['arak']=='DAP' && $pa['aras']!=='∞')			
			return $chprocess($pa,$pd,$active)*1.8;
		else  return $chprocess($pa,$pd,$active);
	}
	//腿部动力装甲减少移动探索体力消耗
	function calculate_search_sp_cost()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		if ($arfk=='DFP' && $arfs!=='∞') 
			return $chprocess()-7;
		else  return $chprocess();
	}
	function calculate_move_sp_cost()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		if ($arfk=='DFP' && $arfs!=='∞') 
			return $chprocess()-7;
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
