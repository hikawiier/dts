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
				$log .= "抓住机会，".$pa['name']."同时对<span class=\"red b\">{$pd['name']}</span>发起攻击！<br>";
				$pd['battlelog'] .= "手持<span class=\"red b\">{$pa['wep']}</span>的<span class=\"yellow b\">{$pa['name']}</span>趁机向你发起了攻击！年轻人真是不讲武德！";
				$cp_atk_flag = 1;
			}
			else
			{
				//NPC盟友发起协战
				$log .= "在你们战作一团的时候，<span class=\"red b\">{$pa['name']}</span>趁机对你作出攻击！年轻人真是不讲武德！<br>";
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
				/*eval(import_module('logger'));
				$log.="返回了原主战的pid：".$oid;*/
			}
		}
	}
}

?>
