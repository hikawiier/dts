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
		\skillbase\skill_delvalue(2601,'oid'); 
		\skillbase\skill_delvalue(2601,'mdmg'); 
	}

	function check_unlocked2601(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	//协战不会改变战斗距离、轮次
	function rs_battle_range_and_turns(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if(strpos($pa['action'],'attcp')!==false || strpos($pd['action'],'attcp')!==false)	return;
		elseif(strpos($pa['action'],'attbycp')!==false || strpos($pd['action'],'attbycp')!==false)	return;
		$chprocess($pa,$pd,$active);
	}

	function change_battle_range(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		if($pa['is_colatk'] || $pd['is_colatk']) return;
		$chprocess($pa,$pd,$active);
	}

	function change_battle_turns(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		if($pa['is_colatk'] || $pd['is_colatk']) return;
		$chprocess($pa,$pd,$active);
	}

	//获取协战伤害系数
	function skilll2601_get_final_dmg_multiplier_fix(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//协战最终伤害系数修正 默认为30%
		$m_dmg_fix = \skillbase\skill_getvalue(2601,'mdmg',$pa); 
		if($m_dmg_fix) return $m_dmg_fix;
		return 30;
	}

	//协战伤害修正
	function get_final_dmg_multiplier(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$r=Array();
		if ($pa['is_colatk'])
		{
			$tmp_r = skilll2601_get_final_dmg_multiplier_fix($pa);
			$r = $tmp_r/100;
			eval(import_module('logger'));
			if($tmp_r<100)
			{
				if ($active)
				$log.= "<span class=\"yellow b\">对方见招拆招，游刃有余，".$pa['name']."打出的攻击仅发挥出{$tmp_r}%的威力！</span><br>";
				else  $log.="<span class=\"yellow b\">你见招拆招，游刃有余，敌人的攻击仅发挥出{$tmp_r}%的威力！</span><br>";
			}
			else
			{
				if ($active)
				$log.= "<span class=\"yellow b\">对方腹背受敌，连遭掣肘，从".$pa['name']."那受到的伤害增加了{$tmp_r}%！</span><br>";
				else  $log.="<span class=\"yellow b\">你腹背受敌，连遭掣肘，从敌人那受到的伤害增加了{$tmp_r}%！</span><br>";
			}
			$r=Array($r);
		}
		return array_merge($r,$chprocess($pa,$pd,$active));
	}

	//协战特殊攻击通告
	function player_attack_enemy(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if($pa['is_colatk']) 
		{
			eval(import_module('logger'));
			if($active)
			{
				//玩家盟友发起协战
				$log .= "抓住机会，<span class=\"red b\">{$pa['name']}</span>与你一同向<span class=\"red b\">{$pd['name']}</span>发起攻击！<br>";
				$pd['battlelog'] .= "<span class=\"red b\">{$pa['name']}</span>在同伴的掩护下突然对你发起攻击！";
			}
			else
			{
				//NPC盟友发起协战
				$log .= "在你们战作一团的时候，<span class=\"red b\">{$pa['name']}</span>在同伴的掩护下突然对你发起攻击！<br>";
			}
			return;
		}
		$chprocess($pa,$pd,$active);	
	}

	function check_can_counter(&$pa, &$pd, $active)
	{
		//被协战者打了不能反击 $pa是挨打的一方 所以检查$pd
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($pd['is_colatk']) return 0;	
		return $chprocess($pa,$pd,$active);
	}

	function player_cannot_counter(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//被协战打了不能反击 也不会发通告
		if($pd['is_colatk']) return;
		$chprocess($pa,$pd,$active);
	}

	function battle_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//协战攻击标记初始化
		if (\skillbase\skill_query(2601,$pa))
		{
			$pa['is_colatk'] = 1;
		}
		$chprocess($pa, $pd, $active);
	}

	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;

		$chprocess($pa,$pd,$active);
		
		//协战结束后处理一些返回的数据
		eval(import_module('c_battle'));
		if ($pa['is_colatk'])
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
			unset($pa['is_colatk']);
		}
	}
}

?>
