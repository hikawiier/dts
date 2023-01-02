<?php

namespace c_battle
{
	//主文件：
	//新增界面 + 对原模块的修改
	function init() 
	{
	}

	function init_coop_battle($edata,$active=0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','player','logger','metman','input','c_battle'));
		//这里的$active用来判断需要重载战斗界面的是敌方还是我方
		if ($edata['hp']>0)
		{
			if ($active) 
			{	//玩家盟友的协战过程
				//获取协战者ID
				$mid = \skillbase\skill_getvalue(2600,'mid'); 
				//清理主战者身上的协战标记
				\skillbase\skill_delvalue(2600,'mid');  
				//获取协战者数据
				$m_data=\player\fetch_playerdata_by_pid($mid); 
				//伪造战斗界面
				$battle_title = '协同作战';
				\metman\init_battle(1);
				//伪造头像！
				$fakeiconImg = 'n_'.$m_data['icon'].'.gif';
				$fakeiconImgB = 'n_'.$m_data['icon'].'a.gif';
				$uip['fake_img'] = (file_exists('img/'.$fakeiconImgB)) ? $fakeiconImgB : $fakeiconImg;
				//由玩家盟友发起战斗
				\enemy\battle_wrapper($m_data,$edata,1);
				return;
			}
			else
			{	//NPC盟友的协战过程
				\player\update_sdata();
				$battle_title = '遭遇夹击';
				\metman\init_battle(1);
				\enemy\battle_wrapper($edata,$sdata,0);
				return;
			}
		}
	}

	function meetman_once_again($edata)
	{
		//“连续战斗”的重载界面
		//适用于所有“已经进入过一次战斗” 需要“再度保持战斗”的场合 
		//这个函数没有对$edata的前缀进行处理 呃 现在真的还需要加$w_吗？
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','player','logger','metman','input','c_battle'));
		if ($edata['hp']>0)
		{
			$sdata['action'] = 'chase'.$edata['pid'];
			$sdata['keep_enemy'] = 1;
			\player\update_sdata();
			if($edata['battle_range']>0)
			{
				$log .= "<br>敌人<span class=\"red b\">{$edata['name']}</span>在你身后紧追不舍！<br>";
			}
			elseif($edata['battle_range']<0)
			{
				$log .= "<br>乘胜追击，你紧紧尾随在敌人<span class=\"red b\">{$edata['name']}</span>身后！<br>";
			}
			else
			{
				$log .= "<br>你再度锁定了敌人<span class=\"red b\">{$edata['name']}</span>！<br>";
			}
			include template(MOD_ENEMY_BATTLECMD);
			$cmd = ob_get_contents();
			ob_clean();
			$battle_title = '陷入鏖战';
			\metman\init_battle(1);
			$main = MOD_METMAN_MEETMAN;		
			return;
		}
		return;
	}

	function meetman_then_escape(&$edata)
	{	
		//接管逃跑事件 用以重置双方战斗回合、距离
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','metman','logger'));
		/*$escape_dice = rand(1,100);
		if(!$edata['battle_turns'] || !$sdata['battle_turns'])
		{	//没交过手的情况下，逃跑率100%
			//默认100% 以后给精英类敌人加技能影响逃跑概率
			$escape_succ_obbs = 100;
		}
		else
		{	//有战斗回合记录 逃跑率=40%+|距离|x10
			$dis_obbs = abs($sdata['battle_range']*10);
			$escape_succ_obbs = 40 + $dis_obbs;
		}
		if($escape_dice<=$escape_succ_obbs)
		{*/
			//逃跑成功
			//逃跑作为脱离战斗循环的唯一途径 在这里消除掉追击标记
			$sdata['action'] = '';unset($sdata['keep_enemy']); 
			//重置双方战斗回合、距离
			\c_battle\rs_battle_range_and_turns($sdata,$edata,1);
			\player\player_save($edata);\player\player_save($sdata);
			$log .= "你逃跑了。<br>";
			//$log .= "双方的战斗次数变为了".$sdata['battle_turns']."和".$edata['battle_turns']."<br>";
			$mode = 'command';
			return;
		/*}
		else
		{
			\c_battle\change_battle_range($edata, $sdata, 0);
			\c_battle\change_battle_turns($edata, $sdata, 0);
			$log .= "你试图逃跑。<br>但只听得背后传来一声怒喝：<span class='yellow b'>“小子，哪里跑！”</span><br>原来是你的逃跑骰只有{$escape_dice}!这下要免不了要挨一顿毒打了！<br>";
			battle_wrapper($edata,$sdata,0);
			return;
		}*/
	}

	//战斗外围准备阶段 初始化双方距离、回合数	
	function battle_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\c_battle\rs_battle_range_and_turns($pa, $pd, $active);
		$chprocess($pa, $pd, $active);
	}

	//战斗准备阶段 应用战斗轮与战斗距离步进
	function assault_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		\c_battle\change_battle_turns($pa, $pd, $active);
	}

	//打击准备阶段 进行战斗距离的步进与pa的dot判定
	function attack_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		if($pa['hp']>0) \c_battle\check_dot_effect($pa,$pd,$active);
		if($pa['hp']>0) \c_battle\change_battle_range($pa,$pd,$active);
	}

	//打击进行阶段 对$pa进行暴毙判断
	function attack(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if($pa['death_flag'])
		{ 
			//$pa暴毙了 跳过打击阶段
			//eval(import_module('logger'));
			//$log .= "出现了死人标记！<br>";
			return;
		}
		$chprocess($pa, $pd, $active);
	}

	//打击结束阶段事件 进行pd的dot判定
	function post_player_damaged_enemy_event(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//pd活着的情况下对pd进行dot判定
		//其实不应该加这个条件 想了想有毒转愈的情况 这里不一定是伤害 也可能是回复
		if($pd['hp']>0) \c_battle\check_dot_effect($pd,$pa,1-$active);
		$chprocess($pa, $pd, $active);
	}

	//战斗外围结束阶段 注销死人标记
	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		unset($pa['death_flag']);
		unset($pd['death_flag']);
		$chprocess($pa, $pd, $active);
	}
	
	//重载页面后维持战斗界面
	function prepare_initial_response_content()
	{	
		//我是sb
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','map','player','logger','metman','input','c_battle'));
		$cmd = $main = '';
		if(strpos($action,'chase')===0 && $hp>0){
			$eid = str_replace('chase','',$action);
			if($eid){
				$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$eid' AND hp>0");
				if($db->num_rows($result)>0){
					$edata = \player\fetch_playerdata_by_pid($eid);
					extract($edata,EXTR_PREFIX_ALL,'w');
					\c_battle\meetman_once_again($edata);
					return;
				}
			}
		}
		elseif(strpos($action,'attbycp')===0 && $hp>0){
			$eid = str_replace('attbycp','',$action);
			if($eid){
				$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$eid' AND hp>0");
				if($db->num_rows($result)>0){
					$edata = \player\fetch_playerdata_by_pid($eid);
					extract($edata,EXTR_PREFIX_ALL,'w');
					\c_battle\init_coop_battle($edata);
					return;
				}
			}
		}
		elseif(strpos($action,'attcp')===0 && $hp>0){
			$eid = str_replace('attcp','',$action);
			if($eid){
				$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$eid' AND hp>0");
				if($db->num_rows($result)>0){
					$edata = \player\fetch_playerdata_by_pid($eid);
					extract($edata,EXTR_PREFIX_ALL,'w');
					\c_battle\init_coop_battle($edata,1);
					return;
				}
			}
		}
		$chprocess();
	}
}

?>