<?php

namespace enemy
{
	function init() {}
	
	function findenemy(&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','logger','player','metman'));
		
		\player\update_sdata();
		
		$battle_title = '发现敌人';
		\metman\init_battle();
		$log .= "你发现了敌人<span class=\"red b\">{$tdata['name']}</span>！<br>对方好像完全没有注意到你！<br>";
		
		include template(get_battlecmd_filename());
		$cmd = ob_get_contents();
		ob_clean();

		$main = MOD_METMAN_MEETMAN;
		
		return;
	}
	
	function get_battlecmd_filename(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','logger','player','metman'));
		return MOD_ENEMY_BATTLECMD;
	}
	
	function calculate_active_obbs(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('enemy'));
		//echo "面对NPC的先制率基础值：".$active_obbs_npc.'% <br>';;
		if($edata['type']) $basevar = $active_obbs_npc;
		else $basevar = $active_obbs_pc;
		$ldata['active_words'] = str_replace('<:BASEVAR:>', $basevar, $ldata['active_words']);
		return $basevar;
	}
	
	function calculate_active_obbs_multiplier(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1.0;
	}
	
	function calculate_active_obbs_change(&$ldata,&$edata,$active_r)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return $active_r;
	}
	
	function get_final_active_obbs(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('enemy'));
		//calculate_active_obbs()是加算，返回1-150的数值
		
		$ldata['active_words'] = '<:BASEVAR:>';
		$active_r = min(max(calculate_active_obbs($ldata,$edata),1), 150);
		//echo "先攻率基础：$active_r <br>";
		
		//calculate_active_obbs_multiplier()是乘算，返回0-1的小数
		$ldata['active_words']='('.$ldata['active_words'].')';
		$active_r *= calculate_active_obbs_multiplier($ldata,$edata);
		
		//calculate_active_obbs_change()是最后改变，返回0-100的数值，这里只放特判，一般增减请用前两个函数
		$active_r = calculate_active_obbs_change($ldata,$edata,$active_r);
		//先攻率最大最小值判定
		$active_r = max($active_obbs_range[0], min($active_obbs_range[1], $active_r));
		//echo $active_r;
		return $active_r;
	}
	
	//判定主动，判定成功代表可以主动选择是否战斗，失败则被动强制进入战斗
	function check_enemy_meet_active(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$active_r = get_final_active_obbs($ldata,$edata);
		//echo "最终先攻率：$active_r <br>";
		$active_dice = rand(0,99);
		return ($active_dice < $active_r);
	}
	
	function meetman_alternative($edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','metman','logger'));
		//以发现状态进入战斗时 初始化交战回合与距离
		//记得去改逃跑操作 逃跑的时候也要初始化双方的回合次数
		//battle_distance的初始值10 为什么？我不知道啊！
		$sdata['battle_times']=0;$edata['battle_times']=0;
		$sdata['battle_distance']=10;$edata['battle_distance']=10;
		if ($edata['hp']>0)
		{
			extract($edata,EXTR_PREFIX_ALL,'w');
			if (check_enemy_meet_active($sdata,$edata)) {
				$action = 'enemy'.$edata['pid'];
				$sdata['keep_enemy'] = 1;
				findenemy($edata);
				return;
			} else {
				battle_wrapper($edata,$sdata,0);
				return;
			}
		}
		else $chprocess($edata);
	}

	function meetman_once_again(&$edata,$log_kind=0)
	{
		//判断“追击”的外围阶段
		//适用于所有需要【已经进入过一次战斗后】再度【保持战斗场景】的场合 
		//一般情况下，进入这里的$edata前缀已经是带“w_”的，如果没有，请在操作这个函数前对$edata进行处理
		//log_kind：0=换了武器；1=挨打；2=打人
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','metman','logger'));
		if ($edata['hp']>0)
		{
			//有没有高手能告诉我init_battle()传的参是干嘛的
			//上了个厕所回来忽然想明白了 原来是给雾天遇敌用的
			$sdata['action'] = 'enemy'.$edata['pid'];
			$sdata['keep_enemy'] = 1;
			\player\update_sdata();
			$battle_title = '陷入鏖战';
			\metman\init_battle(1);
			//呃呃呃呃是打人还是挨打这个状态怎么传回来，也是个问题……
			//哈哈 我想到啦！
			if($log_kind)
			{
				$log .= "<br>乘胜追击，你再度锁定了敌人<span class=\"red b\">{$tdata['name']}</span>！<br>";
				$log .= "<br>但是敌人<span class=\"red b\">{$tdata['name']}</span>在你身后紧追不舍！<br>";
			}
			else
			{
				$log .= "<br>稍作休整，你再度锁定了敌人<span class=\"red b\">{$tdata['name']}</span>！<br>";
			}
			include template(get_battlecmd_filename());
			$cmd = ob_get_contents();
			ob_clean();
			$main = MOD_METMAN_MEETMAN;		
			return;
		}
		return;
	}

	function meetman_then_escape(&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','metman','logger'));
		$escape_obbs = rand(1,100);
		if(!$edata['battle_times'] || !$sdata['battle_times'])
		{	//没交过手的情况下，逃跑率100%
			$escape_succ_obbs = 100;
		}
		else
		{	//有战斗回合记录 逃跑率=40%+|距离|x10
			$dis_obbs = abs($sdata['battle_distance']*10);
			$escape_succ_obbs = 40 + $dis_obbs;
		}
		if($escape_obbs<=$escape_succ_obbs)
		{
			//逃跑成功 重置双方战斗回合、距离
			$sdata['battle_times']=0;$sdata['battle_distance']=0;
			$edata['battle_times']=0;$edata['battle_distance']=0;
			\player\player_save($edata);
			$log .= "你逃跑了。双方的战斗次数变为了".$sdata['battle_times']."和".$edata['battle_times']."<br>";
			$mode = 'command';
			return;
		}
		else
		{
			//逃跑失败 准备挨打
			//不对 仔细想了想逃跑失败距离值不应该变啊！不然硬扛亏死了
			//不对不对 逃跑失败减距离意味着逃跑更难了 所以还是要减滴
			$sdata['battle_distance']++;
			$edata['battle_distance']--;
			$log .= "你试图逃跑。<br>但只听得背后传来一声怒喝：<span class='yellow b'>“小子，哪里跑！”</span><br>原来是你的逃跑随机数只有{$escape_obbs}!想成功的话不能超过{$escape_succ_obbs}！<br>";
			battle_wrapper($edata,$sdata,0);
			return;
		}
	}
	
	function battle_wrapper(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function post_act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess();
		eval(import_module('player'));
		if(empty($sdata['keep_enemy']) && strpos($action, 'enemy')===0){
			$action = '';
			unset($sdata['keep_enemy']);
		}
	}
	
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','map','player','logger','metman','input'));
		if($command == 'enter')
			$sdata['keep_enemy'] = 1;
		if($mode == 'combat') 
		{			
			$enemyid = str_replace('enemy','',$action);
			
			if(!$enemyid || strpos($action,'enemy')===false){
				$log .= "<span class=\"yellow b\">你没有遇到敌人，或已经离开战场！</span><br>";
				$mode = 'command';
				return;
			}
		
			$result = $db->query ( "SELECT * FROM {$tablepre}players WHERE pid='$enemyid'" );
			if (! $db->num_rows ( $result )) {
				$log .= "对方不存在！<br>";
				
				$mode = 'command';
				return;
			}
		
			$edata=\player\fetch_playerdata_by_pid($enemyid);
			extract($edata,EXTR_PREFIX_ALL,'w');
			
			if ($edata ['pls'] != $pls || $edata ['pzone'] != $pzone) {
				$log .= "<span class=\"yellow b\">" . $edata ['name'] . "</span>已经离开了<span class=\"yellow b\">$plsinfo[$pls]</span>。<br>";
				
				$mode = 'command';
				return;
			} elseif ($edata ['hp'] <= 0) {
				$log .= "<span class=\"red b\">" . $edata ['name'] . "</span>已经死亡，不能被攻击。<br>";
				if(\corpse\check_corpse_discover($edata))
				{
					$action = 'corpse'.$edata['pid'];
					$sdata['keep_enemy'] = 1;
					\corpse\findcorpse ( $edata );
				}
				return;
			}			
			\player\update_sdata();
			$ldata=$sdata;
			
			//逃跑被挪到下面 用来接住处理过的$edata
			//增加3个新按钮 
			//'once_again'替代了战斗结果的确认按钮 用于跳转到“追击”界面
			//'defend' 之后会做成技能
			//'b_csubwep' 好怪的名字……用来在战斗中切换武器
			if($command == 'once_again')
			{
				meetman_once_again($edata);
				return;
			}
			if ($command == 'back') 
			{
				//快润！
				meetman_then_escape($edata);
				return;
			}
			if ($command == 'defend') 
			{
				//挨打时除了逃跑以外也可以防御
				$log .= "你双手抱头蹲在墙角，祈祷对方不要打得太狠。<br>";
				battle_wrapper($edata,$ldata,0);
				return;
			}
			if (strpos($command,'b_csubwep') === 0)
			{
				$s = substr($command,9,1);
				eval(import_module('weapon'));
				\weapon\change_subweapon($s,2);
				meetman_once_again($edata);		
				return;
			} 
			battle_wrapper($ldata,$edata,1);
			return;
		}
		$chprocess();
	}
}

?>