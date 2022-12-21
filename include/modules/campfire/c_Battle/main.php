<?php

namespace c_battle
{
	function init() 
	{
	}

	function init_coop_battle($edata,$active=0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','metman','logger'));
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
		eval(import_module('sys','player','metman','enemy','logger'));
		if ($edata['hp']>0)
		{
			$sdata['action'] = 'chase'.$edata['pid'];
			$sdata['keep_enemy'] = 1;
			\player\update_sdata();
			$battle_title = '陷入鏖战';
			\metman\init_battle(1);
			if($edata['battle_distance']>0)
			{
				$log .= "<br>敌人<span class=\"red b\">{$tdata['name']}</span>在你身后紧追不舍！<br>";
			}
			elseif($edata['battle_distance']<0)
			{
				$log .= "<br>乘胜追击，你紧紧尾随在敌人<span class=\"red b\">{$tdata['name']}</span>身后！<br>";
			}
			else
			{
				$log .= "<br>你再度锁定了敌人<span class=\"red b\">{$tdata['name']}</span>！<br>";
			}
			include template(\enemy\get_battlecmd_filename());
			//记得之后在check_battle_skill_available()里加一段和战斗距离相关的判定 在负距离情况下过滤掉带有“攻击性”的技能
			$cmd = ob_get_contents();
			ob_clean();
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
		if(!$edata['battle_times'] || !$sdata['battle_times'])
		{	//没交过手的情况下，逃跑率100%
			//默认100% 以后给精英类敌人加技能影响逃跑概率
			$escape_succ_obbs = 100;
		}
		else
		{	//有战斗回合记录 逃跑率=40%+|距离|x10
			$dis_obbs = abs($sdata['battle_distance']*10);
			$escape_succ_obbs = 40 + $dis_obbs;
		}
		if($escape_dice<=$escape_succ_obbs)
		{*/
			//逃跑成功 重置双方战斗回合、距离
			$sdata['battle_times']=0;$sdata['battle_distance']=10;//每次看见这个10都有点绷不住
			$edata['battle_times']=0;$edata['battle_distance']=10;
			$sdata['action'] = '';
			\player\player_save($edata);\player\player_save($sdata);
			$log .= "你逃跑了。双方的战斗次数变为了".$sdata['battle_times']."和".$edata['battle_times']."<br>";
			$mode = 'command';
			return;
		/*}
		else
		{
			$edata['battle_distance'] = max(0,$edata['battle_distance']-1);
			$sdata['battle_distance'] = 0-$edata['battle_distance'];
			$log .= "你试图逃跑。<br>但只听得背后传来一声怒喝：<span class='yellow b'>“小子，哪里跑！”</span><br>原来是你的逃跑骰只有{$escape_dice}!这下要免不了要挨一顿毒打了！<br>";
			battle_wrapper($edata,$sdata,0);
			return;
		}*/
	}

	function get_battle_distance(&$pa, &$pd, $active)
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
			$pa['battle_distance'] = get_battle_distance($pa, $pd, $active);
			$pd['battle_distance'] = 0-$pa['battle_distance'];
			$pd['battle_times'] = $pa['battle_times'] = 0;
			echo "初次交战，距离与轮次初始化完成。<br>";
		}
		echo "{$pa['name']}和{$pd['name']}的战斗距离分别是{$pa['battle_distance']}与{$pd['battle_distance']}<br>";
		$chprocess($pa, $pd, $active);
	}

	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if(!\skillbase\skill_query(2601,$pa))
		{
			//非协战情况下 计数战斗距离与战斗轮数变化
			if($pa['battle_distance'] && $pd['battle_distance'])
			{	//双方存在距离 故拉近距离
				$pa['battle_distance'] = max(0,$pa['battle_distance']-1);
				$pd['battle_distance'] = 0-$pa['battle_distance'];
				echo "{$pa['name']}和{$pd['name']}之间的距离被拉近了一格！现在分别是{$pa['battle_distance']}与{$pd['battle_distance']}<br>";
			}
			$pa['battle_times']++;
			$pd['battle_times']=$pa['battle_times'];
			echo "增加了一次战斗回合。现在{$pa['name']}和{$pd['name']}的战斗回合数分别是{$pa['battle_times']}与{$pd['battle_times']}<br>";
		}
		$chprocess($pa,$pd,$active);
	}
	
	function prepare_initial_response_content()
	{	
		//重载页面后维持战斗界面
		//会有一些怪问题 比如载入页面后如果因为卡了等原因提交了别的操作 会把加载界面的指令覆盖掉
		//等遇到这种问题了再去解决吧……好懒啊……
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','player','metman'));
		$cmd = $main = '';
		if(strpos($action,'chase')===0 && $gamestate<40 && $hp>0){
			$eid = str_replace('chase','',$action);
			if($eid){
				$result = $db->query("SELECT * FROM {$tablepre}players WHERE pid='$eid' AND hp>0");
				if($db->num_rows($result)>0){
					$edata = \player\fetch_playerdata_by_pid($eid);
					extract($edata,EXTR_PREFIX_ALL,'w');
					meetman_once_again($edata);
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
					init_coop_battle($edata);
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
					init_coop_battle($edata,1);
				}
			}
		}
		$chprocess();
	}

	function post_act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess();
		eval(import_module('player'));
		if(empty($sdata['keep_enemy']) && ( strpos($action, 'chase')===0)){
			$action = '';
			unset($sdata['keep_enemy']);
		}
		if(empty($sdata['keep_enemy']) && ( strpos($action, 'attbycp')===0)){
			$action = '';
			unset($sdata['keep_enemy']);
		}
	}

	//act()部分都写在enemy里了，不挪出来的原因是已经和原本的指令判断紧紧抱在一起了……为什么会这样呢……？
}

?>