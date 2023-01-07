<?php

namespace c_battle
{
	//属性攻击相关

	//滞留型伤害/效果阶段 这该叫啥？
	function check_dot_effect(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//协战状态不判定
		if($pa['is_colatk'] || $pd['is_colatk']) return;
	}
		
	//通过属性抗性计算异常状态的附加率对抗骰
	function calculate_ex_inf_def_rate(&$pa, &$pd, $active, $key)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill2103','logger'));
		//获取抗性
		$tmp_inf_def_rate_dice = 0;
		if (\skillbase\skill_query(2103,$pd)) 
		{
			$tmp_inf_key = $exdef_kind[$key];
			$tmp_inf_def_rate = (\skillbase\skill_getvalue(2103,$tmp_inf_key,$pd))+(\skillbase\skill_getvalue(2103,'a',$pd));
			if($tmp_inf_def_rate>0) $tmp_inf_def_rate_dice = rand(0,ceil($tmp_inf_def_rate/2))+ceil($tmp_inf_def_rate/2);
		}
		//$log.="-异常抗性骰:{$tmp_inf_def_rate_dice}";
		return $tmp_inf_def_rate_dice;
	}

	//判断是初次获取异常还是加深异常状态 $hurtmaxlvl代表该来源施加的异常等级上限 
	//比如因为吃坏东西导致的中毒上限是0级 注意：传进来的值不能超过登记在配置文件里的强制等级上限
	function get_inf_check(&$pa,$hurtposition,$hurtmaxlvl=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('c_battle','wound','logger'));
		//存在该异常的情况下 判断能否加深异常状态
		//if($hurtmaxlvl) echo "存在来源异常上限为【".$hurtmaxlvl."】";
		if(\skillbase\skill_query($infskillinfo[$hurtposition],$pa))
		{
			//echo $pa['name'].'已存在异常【'.$hurtposition.'】，';
			$inf_lvlup = 0;
			$inf_lvl = \skillbase\skill_getvalue($infskillinfo[$hurtposition],'lvl',$pa);
			//echo $hurtposition.'等级【'.$inf_lvl.'】，';
			//有传入等级限制的情况下 先判断传入的等级限制
			if(isset($hurtmaxlvl)) $inf_lvlup = $hurtmaxlvl>$inf_lvl ? $inf_lvl+1 : -1;
			else $inf_lvlup = $ex_inf_lvl_arr[$hurtposition]['max']>$inf_lvl ? $inf_lvl+1 : -1;
			//echo '检查值【'.$inf_lvlup.'】<br>';
			//可以加深异常状态 +1级 记得以后用一个函数替换掉这里
			if($inf_lvlup>0) \skillbase\skill_setvalue($infskillinfo[$hurtposition],'lvl',$inf_lvlup,$pa);
			//不能加深异常状态 +1s 记得以后用一个函数替换掉这里
			if($inf_lvlup<0) 
			{
				$inf_lastturns = \skillbase\skill_getvalue($infskillinfo[$hurtposition],'lastturns',$pa);
				\skillbase\skill_setvalue($infskillinfo[$hurtposition],$inf_lastturns+1,$pa);
			}
			return $inf_lvlup;
		}
		else
		{
			//初次获得异常状态
			//echo $pa['name'].'中了异常【'.$hurtposition.'】<br>';
			\wound\get_inf($hurtposition,$pa);
			return 0;
		}
		return -1;
	}

	//初次获得异常技能时，初始化对应的基础参数
	function set_inf_skills_value(&$pa,$inf_name)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('c_battle','wound','logger'));
		$skill_id = $infskillinfo[$inf_name];
		if($skill_id)
		{
			if(!\skillbase\skill_getvalue($skill_id,'lvl',$pa)) \skillbase\skill_setvalue($skill_id,'lvl',$ex_inf_lvl_arr[$inf_name]['lvl'],$pa);
			//echo "异常".$inf_name.'的基础等级是'.\skillbase\skill_getvalue($skill_id,'lvl',$pa).'<br>';
			if(!\skillbase\skill_getvalue($skill_id,'last_turns',$pa)) \skillbase\skill_setvalue($skill_id,'last_turns',$ex_inf_lvl_arr[$inf_name]['last_turns'],$pa);	
			//echo "异常".$inf_name.'的持续时间是'.\skillbase\skill_getvalue($skill_id,'last_turns',$pa).'<br>';
			if($ex_inf_lvl_arr[$inf_name]['dot_dmg'] && !\skillbase\skill_getvalue($skill_id,'dot_dmg',$pa)) \skillbase\skill_setvalue($skill_id,'dot_dmg',$ex_inf_lvl_arr[$inf_name]['dot_dmg'][0],$pa);
			//echo "异常".$inf_name.'的DOT伤害是'.\skillbase\skill_getvalue($skill_id,'dot_dmg',$pa).'<br>';
			if($ex_inf_lvl_arr[$inf_name]['dot_r_dmg'] && !\skillbase\skill_getvalue($skill_id,'dot_r_dmg',$pa)) \skillbase\skill_setvalue($skill_id,'dot_r_dmg',$ex_inf_lvl_arr[$inf_name]['dot_r_dmg'][0],$pa);
			//echo "异常".$inf_name.'的百分比DOT是'.\skillbase\skill_getvalue($skill_id,'dot_r_dmg',$pa).'<br>';
		}
		return;
	}

	//根据施加异常的来源决定异常的各项参数
	function set_inf_skills_lvl(&$pa,$inf_name,$key)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('c_battle','wound','logger'));
		$skill_id = $infskillinfo[$inf_name];
		if($skill_id)
		{
			//来源提供了异常的初始等级
			if($ex_inf_lvl_arr[$inf_name]['source_lvl'][$key][0]) \skillbase\skill_setvalue($skill_id,'lvl',$ex_inf_lvl_arr[$inf_name]['source_lvl'][$key][0],$pa);
			echo "通过特定来源初始化了".$pa['name']."的异常".$inf_name.'的等级：'.\skillbase\skill_getvalue($skill_id,'lvl',$pa).'<br>';
			//来源提供了异常的初始持续时间
			if($ex_inf_lvl_arr[$inf_name]['source_lastturns'][$key]) \skillbase\skill_setvalue($skill_id,'last_turns',$ex_inf_lvl_arr[$inf_name]['last_turns'][$key],$pa);
			echo "通过特定来源初始化了".$pa['name']."的异常".$inf_name.'的持续时间：'.\skillbase\skill_getvalue($skill_id,'last_turns',$pa).'<br>';
		}
		return;
	}

	//失去异常技能时，删除对应的基础参数
	function del_inf_skills_value(&$pa,$inf_name)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('wound'));
		$skill_id = $infskillinfo[$inf_name];
		if($skill_id)
		{
			\skillbase\skill_delvalue($skill_id,'lvl',$pa);
			\skillbase\skill_delvalue($skill_id,'last_turns',$pa);
			\skillbase\skill_delvalue($skill_id,'dot_dmg',$pa);
			\skillbase\skill_delvalue($skill_id,'dot_r_dmg',$pa);
		}
		return;
	}

	//计算dot伤害
	function calculate_inf_dot_damage(&$pa,$inf_name)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('c_battle','wound'));
		$damage = 0;
		$skill_id = $infskillinfo[$inf_name];
		if($skill_id)
		{
			$dot_lvl = \skillbase\skill_getvalue($skill_id,'lvl',$pa);
			//echo "开始计算".$pa['name'].'受到的来自'.$inf_name.'的DOT伤害<br>';
			$dot_dmg = \skillbase\skill_getvalue($skill_id,'dot_dmg',$pa);
			//echo "基础伤害=".$dot_dmg."；<br>";
			$dot_r_dmg = \skillbase\skill_getvalue($skill_id,'dot_r_dmg',$pa);
			if($dot_r_dmg) $dot_r_dmg = $pa['mhp']*$dot_r_dmg;
			//echo "百分比伤害=".$dot_r_dmg."；<br>";
			$dot_up_fluc = $ex_inf_lvl_arr[$inf_name]['dot_up_fluc'][$dot_lvl] ? $ex_inf_lvl_arr[$inf_name]['dot_up_fluc'][$dot_lvl] : 0;
			$dot_down_fluc = $ex_inf_lvl_arr[$inf_name]['dot_down_fluc'][$dot_lvl] ? $ex_inf_lvl_arr[$inf_name]['dot_down_fluc'][$dot_lvl] : 0;
			$dot_fluc = rand(100-$dot_down_fluc,100+$dot_up_fluc)/100;
			//echo "浮动系数=".$dot_fluc."；<br>";
			$damage = ceil(($dot_dmg+$dot_r_dmg)*$dot_fluc);
			//echo "计算浮动后最终伤害=".$damage."<br>";
		}
		return $damage;
	}

	//异常状态持续回合-1
	function change_inf_turns(&$pa,$inf_name)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('c_battle','wound','logger'));
		$skill_id = $infskillinfo[$inf_name];
		if($skill_id)
		{
			$last_turns = \skillbase\skill_getvalue($skill_id,'last_turns',$pa);
			if($last_turns)
			{
				$last_turns--;
				if($last_turns<=0)
				{
					\wound\heal_inf($inf_name,$pa);
					$p_name = $pa['type'] ? $pa['name'] : '你';
					$log .= "{$p_name}从{$infname[$inf_name]}状态中恢复了！<br>";
				}
				else
				{
					\skillbase\skill_setvalue($skill_id,'last_turns',$last_turns,$pa);
				}
				//echo "【DEBUG】{$pa['name']}的异常{$inf_name}持续时间减少了。现在是：".\skillbase\skill_getvalue($skill_id,'last_turns',$pa)."<br>";
			}
		}
		return;
	}


	//修改因非直接伤害而在战斗中暴毙而死时的log
	//在原文件里直接改了，不搬了。
}

?>