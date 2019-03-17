<?php

namespace skill1906
{
	//眩晕的随机时间区间，单位千分之一秒，其实最好是能和伤害挂钩。但有没有更合适的思路……？
	$min_stun_time1906 = 60000;
	$max_stun_time1906 = 300000;
	function init() 
	{
		define('MOD_SKILL1906_INFO','feature;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1906] = '虏获';
	}
	
	function acquire1906(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost1906(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1906(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}

	function get_hostagestuts1906($p)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		$stt1906 = \skillbase\skill_getvalue(1906,'stt',$p);
		$lt1906 = \skillbase\skill_getvalue(1906,'var',$p);
		$h1906 = $stt1906+$lt1906;
		$now_time = $now;
		if($now<$h1906) return 1;
		else return 0;
	}
	
	function apply_total_damage_modifier_invincible(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (!\skillbase\skill_query(1906,$pd)) return $chprocess($pa,$pd,$active);
		eval(import_module('sys','logger','skill1906'));
		if (\skillbase\skill_query(1906,$pd) && get_hostagestuts1906($pd)){	//有人质的情况下不会受到伤害
			$pa['dmg_dealt']=0;
			if ($active) $log .= "<span class=\"lime b\">敌人忽然抓过虏获来的人质作为盾牌，你暴风般的攻击差点打在了人质身上！这让你不得不放弃了继续攻击的机会。</span><br>";
			else $log .= "<span class=\"lime b\">你将虏获的人质作为盾牌，这让敌人暴风般的攻击差点打在了人质身上！敌人不得不放弃了继续攻击的机会。</span><br>";
		}
		$chprocess($pa,$pd,$active);
	}
	
	function apply_total_damage_modifier_seckill(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if (\skillbase\skill_query(1906,$pd) && get_hostagestuts1906($pd)){
			//有人质的情况下跳过即死判断，就不发log了
			$pa['seckill'] = 0;
			return;
		}
		$chprocess($pa,$pd,$active);
	}
	
	function post_traphit_events(&$pa, &$pd, $tritm, $damage)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $tritm, $damage);
		eval(import_module('sys','player','logger','skill1906'));
		if ($pd['hp']<=round($pd['mhp']*0.1) && \skillbase\skill_query(1906,$pa) && !get_hostagestuts1906($pa))
		{
			$log.="<span class=\"cyan b\">但是你被炸得眼冒金星，一下子晕了过去！</span><br>";
			$stun_time = rand($min_stun_time1906,$max_stun_time1906);
			\skill602\set_stun_period($stun_time,$pd);
			\skillbase\skill_setvalue(602,'stn',$pa['pid'],$pd);
			$true_stun_time = round($stun_time/1000);
			$now_time = $now;
			//同时记录眩晕时间，作为保有人质的时间
			\skillbase\skill_setvalue(1906,'stt',$now_time,$pa);
			\skillbase\skill_setvalue(1906,'var',$true_stun_time,$pa);
		}
	}
	
	function attack_finish(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa,$pd,$active);
		if($pd['hp']<=$pd['mhp']*0.2 && \skillbase\skill_query(1906,$pa) && !empty($pa['is_hit']) && !get_hostagestuts1906($pa))
		{
			$pa['skill1906_flag'] = 1;
		}
	}
	
	//战斗结束时如果有标记，则对方眩晕
	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa,$pd,$active);
		eval(import_module('sys','player','logger','skill1906'));
		$now_time = $now;
		$stun_time = rand($min_stun_time1906,$max_stun_time1906);
		$true_stun_time = round($stun_time/1000);
		//同时记录眩晕时间，作为保有人质的时间
		
		$b_log_1 = '<span class="yellow b">你一巴掌将<:pd_name:>打得失去了意识！大概需要'.($stun_time/1000).'秒才能醒来。</span><br>';
		$b_log_2 = '<span class="yellow b">对方一巴掌将你打得失去了意识！大概需要'.($stun_time/1000).'秒才能醒来。</span><br>';
		
		if(!empty($pa['skill1906_flag'])){
			\skill602\set_stun_period($stun_time,$pd);
			if($active) {
				$log .= str_replace('<:pd_name:>', $pd['name'], $b_log_1);
				$e_log = $b_log_2;
			}else{
				$log .= $b_log_2;
				$e_log = str_replace('<:pd_name:>', $pd['name'], $b_log_1);
			}
			\skillbase\skill_setvalue(1906,'stt',$now_time,$pa);
			\skillbase\skill_setvalue(1906,'var',$true_stun_time,$pa);
			\skillbase\skill_setvalue(602,'stn',$pa['pid'],$pd);
		}elseif(!empty($pd['skill1906_flag'])){
			\skill602\set_stun_period($stun_time,$pa);
			if($active) {
				$log .= $b_log_2;
				$e_log = str_replace('<:pd_name:>', $pa['name'], $b_log_1);
			}else{
				$log .= str_replace('<:pd_name:>', $pa['name'], $b_log_1);
				$e_log = $b_log_2;
			}
			\skillbase\skill_setvalue(1906,'stt',$now_time,$pd);
			\skillbase\skill_setvalue(1906,'var',$true_stun_time,$pd);
			\skillbase\skill_setvalue(602,'stn',$pd['pid'],$pa);
		}
		if(!empty($e_log)){
			if($active && !$pd['type']) $pd['battlelog'].=$e_log;
			elseif(!$active && !$pa['type']) $pa['battlelog'].=$e_log;
		}
	}
}

?>