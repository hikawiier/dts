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
		//哈哈 该来的躲不掉
		//加入“追击”后 喊话要用$active区分$pa和$pd的对应身份了
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

	function get_battle_distance(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','weapon'));
		$r1 = \weapon\get_weapon_range($pa, $active);
		$r2 = \weapon\get_weapon_range($pd, 1-$active);
		$r = max(0,$r1-$r2);
		if($r1 === 0)
		{
			//先制者武器为爆炸物，距离值恒定为1
			$r = 1;			
		}
		if(!$active)
		{
			$r = 0-$r;
		}
		return $r;
	}
	
	function battle_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		send_battle_msg($pa, $pd, $active);		
		if(!$pa['battle_times'] && !$pd['battle_times'])
		{	//双方战斗次数为0时 初始化双方战斗距离
			if($active)
			{
				$pa['battle_distance'] = get_battle_distance($pa, $pd, $active);
				$pd['battle_distance'] = get_battle_distance($pa, $pd, 1-$active);
			}
			else
			{
				$pa['battle_distance'] = get_battle_distance($pa, $pd, 1-$active);
				$pd['battle_distance'] = get_battle_distance($pa, $pd, $active);
			}
			echo "初次交战，距离初始化完成。<br>";
		}
		echo "{$pa['name']}和{$pd['name']}的状态是{$active}，距离分别是{$pa['battle_distance']}与{$pd['battle_distance']}<br>";
	}
	
	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;

		if($pa['battle_distance'] && $pd['battle_distance'])
		{	//双方存在距离 故拉近距离
			$pa['battle_distance'] = max(0,$pa['battle_distance']-1);
			$pd['battle_distance'] = min(0,$pd['battle_distance']+1);
			echo "{$pa['name']}和{$pd['name']}之间的距离被拉近了一格！现在分别是{$pa['battle_distance']}与{$pd['battle_distance']}<br>";
		}
		$pa['battle_times']++;$pd['battle_times']++;
		//增加一次战斗内的交手回合
		echo "增加了一次战斗回合。现在{$pa['name']}和{$pd['name']}的战斗回合数分别是{$pa['battle_times']}与{$pd['battle_times']}<br>";
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

		if ($pd['hp']<=0 || $pa['hp']<=0)
		{
			//死人了情况下重置距离鱼回合数
			$pa['battle_distance'] = 10; $pd['battle_distance'] = 10; 
			$pa['battle_times'] = 0; $pd['battle_times'] = 0; 
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
		else
		{
			//卧槽 这咋写 我开始迷惑了……
			//不想改act()里原本的判断逻辑 那只能这么写了！
			$sdata['action'] = 'enemy'.$edata['pid'];
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
		return MOD_BATTLE_BATTLERESULT;
	}
}

?>