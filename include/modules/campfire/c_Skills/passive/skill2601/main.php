<?php

namespace skill2601
{
	function init() 
	{
		define('MOD_SKILL2601_INFO','unique;hidden;');
		eval(import_module('clubbase'));
		$clubskillname[2601] = '协战';
	}
	
	function acquire2601(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost2601(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}

	function check_unlocked2601(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	function skilll2601_get_final_dmg_multiplier_fix()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//协战最终伤害系数修正
		//协战（群殴）本质上是一个更具演出性质的技能
		//所以被群殴时应该加深伤害还是减轻伤害，不应该是一个平衡性问题，而是一个叙事问题
		//如果你想凸显被群殴的人的宗师气度、大家风范，那被群殴时的伤害就会有所降低
		//如果你想让被群殴的人看起来英雄迟暮、或是让发起群殴的人同仇敌忾，那被群殴的伤害就应该有所提高
		//默认为0.3
		return 0.3;
	}

	//协战伤害修正
	function get_final_dmg_multiplier(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$r=Array();
		if (\skillbase\skill_query(2601,$pa))
		{
			$tmp_r = skilll2601_get_final_dmg_multiplier_fix();
			$log_r = $tmp_r*100;
			eval(import_module('logger'));
			if($log_r<100)
			{
				if ($active)
				$log.= "<span class=\"yellow b\">对方见招拆招，游刃有余，".$pa['name']."打出的攻击仅发挥出{$log_r}%的威力！</span><br>";
				else  $log.="<span class=\"yellow b\">你见招拆招，游刃有余，敌人的攻击仅发挥出{$log_r}%的威力！</span><br>";
			}
			else
			{
				if ($active)
				$log.= "<span class=\"yellow b\">对方腹背受敌，连遭掣肘，从".$pa['name']."那受到的伤害增加了{$log_r}%！</span><br>";
				else  $log.="<span class=\"yellow b\">你腹背受敌，连遭掣肘，从敌人那受到的伤害增加了{$log_r}%！</span><br>";
			}
			$r=Array($tmp_r);	
		}
		return array_merge($r,$chprocess($pa,$pd,$active));
	}

	//协战特殊攻击通告
	function player_attack_enemy(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$cp_atk_flag = 0;
		if(\skillbase\skill_query(2601,$pa)) 
		{
			eval(import_module('logger'));
			if($active) {
				//玩家盟友发起协战
				$log .= "<span class=\"yellow b\">抓住机会，".$pa['name']."与你一同对{$pd['name']}发起夹击！</span><br>";
				$pd['battlelog'] .= "手持<span class=\"red b\">{$pa['wep']}</span>的<span class=\"yellow b\">{$pa['name']}</span>抓住机会同时向你发起攻击！";
				$cp_atk_flag = 1;
			}
			else
			{
				//NPC盟友发起协战
				$log .= "<span class=\"yellow b\">在你们战作一团的时候，{$pa['name']}抓住机会，与同伴一起向你发起攻击！</span><br>";
				$cp_atk_flag = 1;
			}
		}
		if(!$cp_atk_flag)
		{
			$chprocess($pa,$pd,$active);
		}		
	}

	function check_can_counter(&$pa, &$pd, $active)
	{
		//被协战者打了不能反击 $pa是挨打的一方 所以检查$pd
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(2601,$pd))
		{
			$pa['atk_by_cp'] = 1;
			return 0;	
		}
		return $chprocess($pa,$pd,$active);
	}

	function player_cannot_counter(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('logger'));
		if(isset($pa['atk_by_cp']))
		{
			//被协战打了不能反击 也不会发通告
			return;
		}
		else
		{
			$chprocess($pa,$pd,$active);
		}
	}

	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;

		$chprocess($pa,$pd,$active);
		
		//协战结束后处理一些返回的数据
		eval(import_module('c_battle'));
		if (\skillbase\skill_query(2601,$pa))
		{
			$oid = \skillbase\skill_getvalue(2601,'oid',$pa); 
			if($oid)
			{
				if ($active) 
				{
					//玩家协战者发起攻击后 给玩家添加一个追击标记 让玩家本人继续接管战斗
					$sdata['action'] = 'chase'.$pd['pid'];
					//重置假头像
					$uip['fake_img'] = 0; 
					//玩家的协战者打死了NPC 由玩家来摸尸体 剥削啊！
					if ($pd['hp']<=0 && $sdata['hp']>0)
					{
						$sdata['action']='corpse'.$pd['pid'];
					} 
				}
				else
				{
					//被NPC的盟友打过后 把玩家的追击标记对象改回原NPC
					$pd['action'] = 'chase'.$oid; 
				}
				//eval(import_module('logger'));
				//$log.="返回了原主战的pid：".$oid;
			}
		}
	}
}

?>
