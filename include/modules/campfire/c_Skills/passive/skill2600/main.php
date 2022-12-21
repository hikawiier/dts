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
		return NULL;
	}

	function skill2600_check_can_coop_atk(&$pa,$mid)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','skill2600'));
		//看看你的伙伴能不能帮你打架
		$tmp_mdata = \player\fetch_playerdata_by_pid($mid);
		if($tmp_mdata['hp'] > 0 && $tmp_mdata['pls']==$pa['pls'] && $tmp_mdata['pzone']==$pa['pzone']){
			return 1;
		}
		return 0;
	}

	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;

		$chprocess($pa,$pd,$active);

		//判断能不能召唤协战队友
		eval(import_module('skill2600'));
		if (\skillbase\skill_query(2600,$pa) && !\skillbase\skill_query(2601,$pa) && $pd['hp']>0) //只有先手攻击才能召唤协战 反击不行
		{
			$mid = skill2600_get_mate_pid($pa); //获取协战队友的pid
			if($mid && skill2600_check_can_coop_atk($pa,$mid))
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
				$m_data['battle_distance'] = $pa['battle_distance'];
				$m_data['battle_times'] = $pa['battle_distance'];
				//为协战者添加协战技能
				\skillbase\skill_acquire(2601,$m_data);
				//为协战者添加主战者的ID
				$oid = $pa['pid'];
				\skillbase\skill_setvalue(2601,'oid',$oid,$m_data); 
				//保存协战者数据
				\player\player_save($m_data);
				eval(import_module('logger'));
				$log.="触发了协战者pid：".$mid;
			}
		}
	}
}

?>
