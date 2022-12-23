<?php

namespace c_battle
{
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
			//逃跑成功 重置双方战斗回合、距离
			$sdata['battle_turns']=0;$sdata['battle_range']=10;//每次看见这个10都有点绷不住
			$edata['battle_turns']=0;$edata['battle_range']=10;
			//逃跑作为脱离战斗循环的唯一途径 在这里消除掉追击标记
			$sdata['action'] = '';unset($sdata['keep_enemy']); 
			\player\player_save($edata);\player\player_save($sdata);
			$log .= "你逃跑了。<br>";
			//$log .= "双方的战斗次数变为了".$sdata['battle_turns']."和".$edata['battle_turns']."<br>";
			$mode = 'command';
			return;
		/*}
		else
		{
			$edata['battle_range'] = max(0,$edata['battle_range']-1);
			$sdata['battle_range'] = 0-$edata['battle_range'];
			$log .= "你试图逃跑。<br>但只听得背后传来一声怒喝：<span class='yellow b'>“小子，哪里跑！”</span><br>原来是你的逃跑骰只有{$escape_dice}!这下要免不了要挨一顿毒打了！<br>";
			battle_wrapper($edata,$sdata,0);
			return;
		}*/
	}

	function get_battle_range(&$pa, &$pd, $active)
	{
		//根据武器射程对比初始化战斗距离
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
		return $r;
	}

	function battle_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		if((strpos($pd['action'],'penemy')!==false && !$active) || (strpos($pa['action'],'enemy')!==false && $active))
		{	//从meetman_alternative()进入战斗时 初始化距离、回合数
			$pa['battle_range'] = get_battle_range($pa, $pd, $active);
			$pd['battle_range'] = 0-$pa['battle_range'];
			$pd['battle_turns'] = $pa['battle_turns'] = 0;
			eval(import_module('logger'));
			$log .="初次交战，距离与轮次初始化完成。<br>";
		}
		$chprocess($pa, $pd, $active);
	}

	function assault_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//战斗准备阶段 存在战斗距离的情况下拉近一格距离
		if(!\skillbase\skill_query(2601,$pa) && $pa['battle_range'] && $pd['battle_range'])
		{
			$pa['battle_range'] = max(0,$pa['battle_range']-1);
			$pd['battle_range'] = 0-$pa['battle_range'];
			eval(import_module('logger'));
			$log .= "{$pa['name']}向{$pd['name']}逼近了一步！现在二人的距离分别为{$pa['battle_range']}与{$pd['battle_range']}<br>";
		}
		$chprocess($pa, $pd, $active);
	}

	function assault_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//战斗结算阶段 计算战斗轮变化
		if(!\skillbase\skill_query(2601,$pa))
		{
			$pa['battle_turns']++;
			$pd['battle_turns']=$pa['battle_turns'];
			eval(import_module('logger'));
			$log .="双方完成了一轮交手。现在{$pa['name']}和{$pd['name']}的战斗轮次分别是{$pa['battle_turns']}与{$pd['battle_turns']}<br>";
		}
		$chprocess($pa, $pd, $active);
	}

	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//战斗轮与距离计算移至他处
		$chprocess($pa,$pd,$active);
	}
	
	function prepare_initial_response_content()
	{	
		//重载页面后维持战斗界面
		//会有一些怪问题 比如载入页面后如果因为卡了等原因提交了别的操作 会把加载界面的指令覆盖掉
		//等遇到这种问题了再去解决吧……好懒啊……
		//卧槽！这个部分明明没有用，但是去掉了又不行！我不理解啊！震惊！
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','map','player','logger','metman','input','c_battle'));
		$cmd = $main = '';
		if(strpos($action,'chase')===0 && $gamestate<40 && $hp>0){
			$eid = str_replace('chase','',$action);
			if($eid){
				$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$eid' AND hp>0");
				if($db->num_rows($result)>0){
					$edata = \player\fetch_playerdata_by_pid($eid);
					extract($edata,EXTR_PREFIX_ALL,'w');
					\c_battle\meetman_once_again($edata);
				}
			}
		}
		elseif(strpos($action,'attbycp')===0 && $gamestate<40 && $hp>0){
			$eid = str_replace('attbycp','',$action);
			if($eid){
				$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$eid' AND hp>0");
				if($db->num_rows($result)>0){
					$edata = \player\fetch_playerdata_by_pid($eid);
					extract($edata,EXTR_PREFIX_ALL,'w');
					\c_battle\init_coop_battle($edata);
				}
			}
		}
		elseif(strpos($action,'attcp')===0 && $gamestate<40 && $hp>0){
			$eid = str_replace('attcp','',$action);
			if($eid){
				$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$eid' AND hp>0");
				if($db->num_rows($result)>0){
					$edata = \player\fetch_playerdata_by_pid($eid);
					extract($edata,EXTR_PREFIX_ALL,'w');
					\c_battle\init_coop_battle($edata,1);
				}
			}
		}
		$chprocess();
	}

	function post_act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess();
		//chase、attbycp、attcp这3个标记只能通过battle_wrapper内的逻辑消除
		//这样可以避免刷新页面后因为卡了等原因按到其他按钮导致提前脱离战斗
		//可以避免……吧……
		/*eval(import_module('player'));
		if(empty($sdata['keep_enemy']) && ( strpos($action, 'chase')===0)){
			$action = '';
			unset($sdata['keep_enemy']);
		}
		if(empty($sdata['keep_enemy']) && ( strpos($action, 'attbycp')===0)){
			$action = '';
			unset($sdata['keep_enemy']);
		}*/
	}

	//act()其他部分都写在enemy里了，不挪出来的原因是已经和原本的指令判断紧紧抱在一起了……为什么会这样呢……？
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','player','logger','metman','input','c_battle'));
		//各种标记下刷新页面会重载战斗界面
		if ($command == 'enter' && strpos($action,'chase')===0 && $gamestate<40 && $hp>0)
		{
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
		elseif($command == 'enter' && strpos($action,'attbycp')===0 && $gamestate<40 && $hp>0)
		{
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
		elseif($command == 'enter' && strpos($action,'attcp')===0 && $gamestate<40 && $hp>0)
		{
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