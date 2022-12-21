<?php

namespace battle
{
	//注意，各种攻击函数的$active都是相对于玩家而言的，$active=1代表$pa（攻击者）是玩家
	
	//保存敌人的战斗log
	function save_enemy_battlelog(&$pl)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','logger'));
		if(!$pl['type']){
			if(isset($pl['battle_msg'])) {
				$pl['battlelog'] = $pl['battle_msg'].$pl['battlelog'];
			}
			if(!empty($pl['battlelog'])) \logger\logsave ( $pl['pid'], $now, $pl['battlelog'] ,'b');
		}
	}
	
	function send_battle_msg(&$pa, &$pd, $active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','logger','input'));
		//加入“连续战斗”后 喊话要用$active区分$pa和$pd的对应身份了
		//讲道理 不能因为NPC不能喊话就不给NPC喊话的机会 NPC LIVES MATTER
		if(!empty($message)){
			if($active)
			{
				$log .= "<span class=\"lime b\">你向{$pd['name']}喊道：“{$message}”</span><br>";
				$pd['battle_msg'] = "<span class=\"lime b\">{$pa['name']}向你喊道：“{$message}”</span><br><br>";
				\sys\addchat(6, "{$pa['name']}高喊着“{$message}”杀向了{$pd['name']}");
			}
			else
			{
				$log .= "<span class=\"lime b\">你向{$pa['name']}喊道：“{$message}”</span><br>";
				$pa['battle_msg'] = "<span class=\"lime b\">{$pd['name']}向你喊道：“{$message}”</span><br><br>";
				\sys\addchat(6, "{$pd['name']}高喊着“{$message}”杀向了{$pa['name']}");
			}
		}
	}

	function battle_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		send_battle_msg($pa, $pd, $active);		
	}
	
	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function battle(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		assault_prepare($pa,$pd,$active);
		assault($pa,$pd,$active);
		assault_finish($pa,$pd,$active);
	} 
	
	function battle_wrapper(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		battle_prepare($pa, $pd, $active);
		battle($pa, $pd, $active);
		battle_finish($pa, $pd, $active);
		
		//写回数据库
		eval(import_module('sys','logger','player','metman'));

		$oid = NULL; $oarr = Array(); $m_battle_flag = $active_m_battle_flag = false;
		if(\skillbase\skill_query(2600,$pa))
		{
			if(\skillbase\skill_getvalue(2600,'oid',$pa))
			{
				$oid = \skillbase\skill_getvalue(2600,'oid',$pa); 
				\skillbase\skill_delvalue(2600,'oid',$pa); //消除触发标记
			}
			elseif(!$active && \skillbase\skill_getvalue(2600,'mid',$pa) && $pd['hp']>0)
			{
				$m_battle_flag = true;
			}
			elseif($active && \skillbase\skill_getvalue(2600,'mid',$pa) && $pd['hp']>0)
			{
				$active_m_battle_flag = true;
			}
		}

		if ($pd['hp']<=0 || $pa['hp']<=0)
		{
			//死人了情况下重置距离鱼回合数
			$pa['battle_distance'] = 10; $pd['battle_distance'] = 10; 
			$pa['battle_times'] = 0; $pd['battle_times'] = 0; 
		}

		if ($active) 
		{ 
			//玩家存在同盟 同盟触发协战的情况下
			if($active_m_battle_flag)
			{
				$pa['action'] = 'attcp'.$pd['pid']; //这里记录的依然是敌人的情报
			}
			else
			{
				if($oid)
				{//出现伪装成玩家的协战者 给玩家添加追击标记 这样下一次进入战斗页面又会显示玩家了
					$sdata['action'] = 'chase'.$pd['pid'];
					$uip['fake_img'] = 0; //重置头像
					//玩家的协战者打死了NPC 由玩家来摸尸体 剥削啊！
					if ($pd['hp']<=0 && $pa['hp']>0)
					{
						$sdata['action']='corpse'.$pd['pid'];
					}
				}
				else
				{
					$pa['action'] = 'chase'.$pd['pid']; 
				}
			}
			if ($pd['hp']<=0 && $pa['hp']>0)
			{
				$pa['action']='corpse'.$pd['pid'];
			}
			if ($pa['hp']<=0 && $pd['hp']>0 && $pd['action']=='' && $pd['type']==0)
			{
				$pd['action'] = 'pacorpse'.$pa['pid']; 
			}			
		}
		else
		{
			//敌人存在同盟 同盟触发协战的
			if($m_battle_flag)
			{
				$mid = \skillbase\skill_getvalue(2600,'mid',$pa); //获取协战者ID
				$oid = $pa['pid']; //标记原本的主战者pid

				$m_data=\player\fetch_playerdata_by_pid($mid); //获取协战者数据
				$m_data['battle_distance'] = $pa['battle_distance'] ;
				$m_data['battle_times'] = $pa['battle_times'] ;//从主战者处继承一些属性
				\skillbase\skill_setvalue(2600,'oid',$oid,$m_data); //为协战者添加主战者的ID
				\player\player_save($m_data);

				\skillbase\skill_delvalue(2600,'mid',$pa); //消除主战者身上的协战者ID
				$pd['action'] = 'attbycp'.$mid; 
			}
			//敌人是协战者 读取到了原主战者的pid 
			elseif($oid)
			{
				$pd['action'] = 'chase'.$oid; 
			}
			else
			{
				$pd['action'] = 'chase'.$pa['pid']; 
			}
			if ($pd['hp']<=0 && $pa['hp']>0 && $pa['action']=='' && $pa['type']==0)
			{
				$pa['action']='pacorpse'.$pd['pid'];
			}
			if ($pa['hp']<=0 && $pd['hp']>0)
			{
				$pd['action'] = 'corpse'.$pa['pid']; 
			}
		}

		if ($active)
		{
			$edata=$pd;
			\player\player_save($pa); \player\player_save($pd);
			\metman\metman_load_playerdata($pd);
			if ($pd['type']==0) save_enemy_battlelog($pd);
			\player\load_playerdata($pa);
		}
		else
		{
			$edata=$pa;
			\player\player_save($pa); \player\player_save($pd);
			\metman\metman_load_playerdata($pa);
			if ($pa['type']==0) save_enemy_battlelog($pa);
			\player\load_playerdata($pd);
		}
		
		$battle_title = '战斗发生';
		$main = MOD_METMAN_MEETMAN;
		\metman\init_battle(1);
		
		if (substr($action,0,6)=='corpse')
		{
			\corpse\findcorpse($edata);
		}
		elseif($m_battle_flag || $active_m_battle_flag)
		{
			$sdata['keep_enemy'] = 1;
			include template(MOD_BATTLE_COOPBATTLERESULT);
			$cmd = ob_get_contents();
			ob_clean();
		}
		else
		{
			$sdata['keep_enemy'] = 1;
			include template(get_battleresult_filename());
			$cmd = ob_get_contents();
			ob_clean();
		}
		if (defined('MOD_CLUBBASE')) include template(MOD_CLUBBASE_NPCSKILLPAGE);
	}
	
	function get_battleresult_filename(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','logger','player','metman'));
		//这里之后再加一个新的result页面用来跳转到追击页面吧 不要改原来的了
		return MOD_BATTLE_BATTLERESULT;
	}
}

?>