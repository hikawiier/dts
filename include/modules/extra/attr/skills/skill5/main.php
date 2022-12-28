<?php

namespace skill5
{
	function init() 
	{
		eval(import_module('wound'));
		//受伤状态简称（用于profile显示）
		$infinfo['p'] = '<span class="purple b">毒</span>';
		//受伤状态名称动词
		$infname['p'] = '<span class="purple b">中毒</span>';
		//受伤状态对应的特效技能编号
		$infskillinfo['p'] = 5;
	}
	
	function acquire5(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost5(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function skill_onload_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (strpos($pa['inf'],'p')!==false) \skillbase\skill_acquire(5,$pa);
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skillbase'));
		if (\skillbase\skill_query(5,$pa)) \skillbase\skill_lost(5,$pa);
		$chprocess($pa);
	}
	
	function deal_poison_move_damage($damage)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		$hp -= $damage;
		$log .= "<span class=\"purple b\">毒发</span>减少了<span class=\"red b\">$damage</span>点生命！<br>";
		if($hp <= 0 ){
			$state = 12;
			\player\update_sdata(); $sdata['sourceless'] = 1;
			\player\kill($sdata,$sdata);
			\player\player_save($sdata);
			\player\load_playerdata($sdata);
		}
	}
	
	function search_area()	//毒发探索掉血
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		if (\skillbase\skill_query(5))
		{
			$damage = round($mhp * 0.03125) + rand(0,10);
			deal_poison_move_damage($damage);
		}
		if ($hp>0) $chprocess();
	}
	
	function move_to_area($moveto)	//毒发移动掉血
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		if (\skillbase\skill_query(5))
		{
			$damage = round($mhp * 0.0625) + rand(0,10);
			deal_poison_move_damage($damage);
		}
		if ($hp>0) $chprocess($moveto);
	}

	//攻击准备阶段 结算$pa受到的异常伤害
	function attack_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		unset($pa['deathmark']);//有点搞笑
		if (\skillbase\skill_query(5,$pa) && !$pa['is_colatk'])
		{
			$tmp_inf_dmg = 1;
			$pa['hp'] -= $tmp_inf_dmg;
			eval(import_module('logger'));
			$log .= $pa['name']."因<span class=\"purple b\">毒发</span>减少了<span class=\"red b\">$tmp_inf_dmg</span>点生命！<br>";
			if($pa['hp']<=0)
			{
				$pa['deathmark'] = 120;
				return;
			}
		}
	}

	function attack(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//pa因为某些情况意外暴毙了 不会触发打击流程
		if($pa['deathmark']) return;
		$chprocess($pa, $pd, $active);
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())	//毒发死亡新闻
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		
		if($news == 'death12') 
		{
			$dname = $typeinfo[$b].' '.$a;
			if(!$e)
				$e0="<span class=\"yellow b\">【{$dname} 什么都没说就死去了】</span><br>\n";
			else  $e0="<span class=\"yellow b\">【{$dname}：“{$e}”】</span><br>\n";
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">$a</span>因<span class=\"red b\">毒发</span>死亡{$e0}</li>";
		} 

		if($news == 'death120') 
		{
			$dname = $typeinfo[$b].' '.$a;
			if(!$e)
				$e0="<span class=\"yellow b\">【{$dname} 什么都没说就死去了】</span><br>\n";
			else  $e0="<span class=\"yellow b\">【{$dname}：“{$e}”】</span><br>\n";
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">$a</span>在与<span class=\"yellow b\">$c</span>的战斗中因<span class=\"red b\">毒发</span>死亡{$e0}</li>";
		} 
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>
