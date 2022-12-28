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

		if(\skillbase\skill_query(2601,$pa))
		{
			//在这个地方丢掉协战技能
			//写在battle_finish()里必须厘清继承关系 不然有被无限连打的风险 但是厘不清啦！
			\skillbase\skill_lost(2601,$pa);
			if($active)
			{ //偷个懒 为玩家召唤的协战对象的log替换人称
				$log = str_replace('你',$pa['name'],$log);
			}
		}	

		if ($pd['hp']<=0 || $pa['hp']<=0)
		{
			//死人了情况下重置距离鱼回合数
			$pa['battle_range'] = $pd['battle_range'] = 10; 
			$pa['battle_turns'] = $pd['battle_turns'] = 0; 
		}

		if ($active) 
		{ 
			if ($pd['hp']<=0 && $pa['hp']>0)
			{
				$pa['action']='corpse'.$pd['pid'];
			}
			if ($pa['hp']<=0 && $pd['hp']>0 && $pd['action']=='' && $pd['type']==0)
			{
				$pd['action'] = 'pacorpse'.$pa['pid']; 
			}		
			if(($pa['action']=='' || strpos($pa['action'],'enemy')!==false) && $pa['type']==0)
			{ //玩家身上只有发现敌人或被发现的标记 发一个追击标记
				$pa['action'] = 'chase'.$pd['pid']; 
				//echo "玩家主动触发了追击标记：".$pa['action']."<br>";
			}	
		}
		else
		{
			if ($pd['hp']<=0 && $pa['hp']>0 && $pa['action']=='' && $pa['type']==0)
			{
				$pa['action']='pacorpse'.$pd['pid'];
			}
			if ($pa['hp']<=0 && $pd['hp']>0)
			{
				$pd['action'] = 'corpse'.$pa['pid']; 
			}
			if(($pd['action']=='' || strpos($pd['action'],'enemy')!==false) && $pd['type']==0)
			{
				$pd['action'] = 'chase'.$pa['pid']; 
				//echo "玩家被动触发了追击标记：".$pd['action']."<br>";;
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
		elseif(strpos($action,'attcp')===0 || strpos($action,'attbycp')===0)
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