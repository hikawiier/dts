<?php

namespace skill2600
{
	//这个技能初始化有点麻烦
	//第一次初始化时就生成的npc 在生成的时候不一定能确保自己的盟友也生成了 所以不一定能找到pid
	//解决这个问题的思路有两个方法：
	//一是修改rs_game()的过程 在所有npc都生成过后 再根据预设条件去找指定盟友的pid
	//二是在技能里只保存盟友的type和name，用fetch_playerdata('name','type')拉取盟友的数据
	//那么第二种方法有什么不好的地方呢？ ……呃呃…… 因为你要往技能字段里存中文 虽然是转码过的……

	function init() 
	{
		define('MOD_SKILL2600_INFO','unique;');
		eval(import_module('clubbase'));
		$clubskillname[2600] = '盟誓';
	}
	
	function acquire2600(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost2600(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}

	function check_unlocked2600(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	//从条件上判断能否触发协战
	function skill2600_check_can_colatk(&$pa,$mid)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','skill2600'));
		//看看你的伙伴能不能帮你打架
		$tmp_mdata = \player\fetch_playerdata_by_pid($mid);
		if($tmp_mdata['hp'] > 0 && $tmp_mdata['pls']==$pa['pls'] && $tmp_mdata['pzone']==$pa['pzone']) return 1;
		return 0;
	}
	
	//从概率上判断能否触发协战
	function skilll2600_check_colatk_obbs(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//召唤协战队友的概率 默认30%
		$colatk_obbs = \skillbase\skill_getvalue(2600,'mobbs',$pa) ? \skillbase\skill_getvalue(2600,'mobbs',$pa) : 30;
		$dice = rand(0,99);
		if($dice <= $colatk_obbs) return 1;
		else return 0;
	}

	//从名字与类别获取协战对象pid
	//如果有多个同名对象会出BUG，暂时不修！
	function skill2600_get_mate_pid(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','skill2600'));
		$tmp_mt = \skillbase\skill_getvalue(2600,'mt',$pa);
		$tmp_mn = \skillbase\skill_getvalue(2600,'mn',$pa);;
		$tmp_mdata = \player\fetch_playerdata($tmp_mn,$tmp_mt);
		if(isset($tmp_mdata['pid']) && $tmp_mdata['hp'] > 0){
			$tmp_mpid = $tmp_mdata['pid'];
			return $tmp_mpid;
		}
		return;
	}

	function check_can_counter(&$pa, &$pd, $active)
	{
		//叫人来挡刀的情况下不会反击 不发通告 $pa是挨打的一方
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if($pa['help_by_cp']) return 0;	
		return $chprocess($pa,$pd,$active);
	}

	function player_cannot_counter(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if($pa['help_by_cp'])
		{
			//在这里注销掉挡刀标记 
			$mid = \skillbase\skill_getvalue(2600,'help',$pa);
			\skillbase\skill_delvalue(2600,'help',$pa);
			unset($pa['help_by_cp']);
			$m_data=\player\fetch_playerdata_by_pid($mid); 
			eval(import_module('logger'));
			$log.="<span class='yellow b'>你怪招频出，打得{$pa['name']}难以招架，但{$m_data['name']}突然冲出来挡在你的面前！<br></span>";
			return;
		}
		$chprocess($pa,$pd,$active);
	}

	//打击结束阶段 判断对方是否会寻求盟友挡刀
	function attack_finish(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//pd被人打了 判断能不能叫协战队友来挡刀 
		//这个功能目前只提供给NPC/NPC
		//因为NPC替玩家挡刀暂时不好处理被打死了的情况 至于玩家替NPC挡刀 那就更不用说了……
		//另外被反击打死了也不会触发这个功能，因为再套娃下去就搞不懂了！！而且被反击打死还有空闲拉人来挡刀就有点过分了！！
		if ($pa['hp']>0 && $pd['hp']>0 && $pd['hp']<($pd['mhp']*0.15) && \skillbase\skill_query(2600,$pd) && $pd['type']) //被打到濒死（15%血以下）情况下才能叫队友来挡刀 被打死了不行
		{
			$mid = skill2600_get_mate_pid($pd); 
			if($mid && skill2600_check_can_colatk($pd,$mid))
			{
				//获取协战者数据
				$m_data=\player\fetch_playerdata_by_pid($mid); 
				if($m_data['type']) //盟友非玩家才能触发
				{
					//添加召唤了挡刀的标记
					\skillbase\skill_setvalue(2600,'help',$mid,$pd); 
					$pd['help_by_cp'] = 1;
					//挡刀条件触发的情况下 为玩家（pa）更新标记
					$pa['action'] = 'chase'.$mid; 
					$m_data['battle_range'] = $pd['battle_range'];
					$m_data['battle_turns'] = $pd['battle_turns'];
					\player\player_save($m_data);
				}
			}
		}
		$chprocess($pa, $pd, $active);
	}

	//战斗外围结束阶段 判断是否能召唤协战队友
	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa,$pd,$active);
		eval(import_module('c_battle'));
		//战斗的发起者（pa）才能够召唤协战
		if ($pa['hp']>0 && $pd['hp']>0 && \skillbase\skill_query(2600,$pa) && !\skillbase\skill_query(2601,$pa) && skilll2600_check_colatk_obbs($pa))
		{
			$mid = skill2600_get_mate_pid($pa); //获取协战队友的pid
			if($mid && skill2600_check_can_colatk($pa,$mid))
			{
				//存在可以发起协战的对象 在技能里存入协战对象的pid
				\skillbase\skill_setvalue(2600,'mid',$mid,$pa);
				if($active)
				{
					//玩家存在同盟 同盟触发协战的情况下 为玩家（pa）更新标记
					$pa['action'] = 'attcp'.$pd['pid']; //这里记录的是敌人的pid
				}
				else
				{
					//NPC存在同盟 同盟触发协战的情况下 为玩家（pd）更新标记
					$pd['action'] = 'attbycp'.$mid; 
					//清理主战者身上的协战者ID
					\skillbase\skill_delvalue(2600,'mid',$pa); 
				}
				//获取协战者数据
				$m_data=\player\fetch_playerdata_by_pid($mid); 
				//初始化协战对象的部分属性
				$m_data['battle_range'] = $pa['battle_range'];
				$m_data['battle_turns'] = $pa['battle_turns'];
				//为协战者添加协战技能
				\skillbase\skill_acquire(2601,$m_data);
				//为协战者添加主战者的ID与伤害系数（如果有）
				$oid = $pa['pid'];
				\skillbase\skill_setvalue(2601,'oid',$oid,$m_data); 
				$m_dmg_fix = \skillbase\skill_getvalue(2600,'mdmg',$pa);
				if($m_dmg_fix) \skillbase\skill_setvalue(2601,'mdmg',$m_dmg_fix,$m_data); 
				//保存协战者数据
				\player\player_save($m_data);
				eval(import_module('logger'));
				$log.="【DEBUG】触发了协战者pid：".$mid;
			}
		}
	}
}

?>
