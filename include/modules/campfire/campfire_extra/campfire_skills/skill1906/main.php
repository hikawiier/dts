<?php

namespace skill1906
{
	//眩晕的随机时间区间，单位千分之一秒，其实最好是能和伤害挂钩。但有没有更合适的思路……？
	$min_stun_time1906 = 60000;
	$max_stun_time1906 = 300000;
	function init() 
	{
		define('MOD_SKILL1906_INFO','card;unique;');
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
	
	function post_traphit_events(&$pa, &$pd, $tritm, $damage)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $tritm, $damage);
		eval(import_module('player','logger','skill1906'));
		if ($pa['hp']<=round($pa['mhp']*0.1) && \skillbase\skill_query(1906,$pd))
		{
			$log.="<span class=\"cyan b\">对方的陷阱将你炸得眼冒金星！</span><br>";
			$stun_time = rand($min_stun_time1906,$max_stun_time1906);
			\skill602\set_stun_period($stun_time,$pa);
		}
	}
	
	function attack_finish(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa,$pd,$active);
		if($pd['hp']<=$pd['mhp']*0.2 && \skillbase\skill_query(1906,$pa) && !empty($pa['is_hit']))
		{
			$pa['skill1906_flag'] = 1;
		}
	}
	
	//战斗结束时如果有标记，则对方眩晕
	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa,$pd,$active);
		eval(import_module('logger','skill1906'));
		
		$stun_time = rand($min_stun_time1906,$max_stun_time1906);
		
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
		}elseif(!empty($pd['skill1906_flag'])){
			\skill602\set_stun_period($stun_time,$pa);
			if($active) {
				$log .= $b_log_2;
				$e_log = str_replace('<:pd_name:>', $pa['name'], $b_log_1);
			}else{
				$log .= str_replace('<:pd_name:>', $pa['name'], $b_log_1);
				$e_log = $b_log_2;
			}
		}
		if(!empty($e_log)){
			if($active && !$pd['type']) $pd['battlelog'].=$e_log;
			elseif(!$active && !$pa['type']) $pa['battlelog'].=$e_log;
		}
	}
}

?>