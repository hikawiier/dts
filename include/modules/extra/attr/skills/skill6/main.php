<?php

namespace skill6
{
	function init() 
	{
		eval(import_module('wound'));
		//受伤状态简称（用于profile显示）
		$infinfo['u'] = '<span class="red b">烧</span>';
		//受伤状态名称动词
		$infname['u'] = '<span class="red b">烧伤</span>';
		//受伤状态对应的特效技能编号
		$infskillinfo['u'] = 6;
	}
	
	function acquire6(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\c_battle\set_inf_skills_value($pa,'u');
	}
	
	function lost6(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\c_battle\del_inf_skills_value($pa,'u');
	}
	
	function skill_onload_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (strpos($pa['inf'],'u')!==false && !\skillbase\skill_query(6,$pa)) \skillbase\skill_acquire(6,$pa);
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (strpos($pa['inf'],'u')===false && \skillbase\skill_query(6,$pa)) \skillbase\skill_lost(6,$pa);
		$chprocess($pa);
	}
	
	function deal_burn_move_damage($damage)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		$hp -= $damage;
		$log .= "<span class=\"red b\">烧伤发作</span>减少了<span class=\"red b\">$damage</span>点生命！<br>";
		if($hp <= 0 ){
			$state = 18;
			\player\update_sdata(); $sdata['sourceless'] = 1;
			\player\kill($sdata,$sdata);
			\player\player_save($sdata);
			\player\load_playerdata($sdata);
		}
	}
	
	function search_area()	//烧伤探索掉血
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		if (\skillbase\skill_query(6))
		{
			//$damage = round($mhp * 0.03125) + rand(0,10);
			$damage = \c_battle\calculate_inf_dot_damage($sdata,'u');
			deal_burn_move_damage($damage);
			\c_battle\change_inf_turns($sdata,'u');
		}
		if ($hp>0) $chprocess();
	}
	
	function move_to_area($moveto)	//烧伤移动掉血
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		if (\skillbase\skill_query(6))
		{
			//$damage = round($mhp * 0.0625) + rand(0,10);
			$damage = \c_battle\calculate_inf_dot_damage($sdata,'u');
			deal_burn_move_damage($damage);
			\c_battle\change_inf_turns($sdata,'u');
		}
		if ($hp>0) $chprocess($moveto);
	}

	function check_dot_effect(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		if (\skillbase\skill_query(6,$pa))
		{
			//$tmp_inf_dmg = round($pa['mhp']*0.000625) + rand(0,10);
			$tmp_inf_dmg = \c_battle\calculate_inf_dot_damage($pa,'u');
			$pa['hp'] -= $tmp_inf_dmg;
			eval(import_module('logger'));
			$tmp_name = $active ? '你' : $pa['name'];
			$log .= $tmp_name."因<span class=\"red b\">烧伤发作</span>减少了<span class=\"red b\">$tmp_inf_dmg</span>点生命！<br>";
			if($pa['hp']<=0)
			{
				$pa['death_flag'] = 1;
				$pa['deathmark'] = 121;
				return;
			}
		}
	}

	function change_battle_turns_events(&$pa, &$pd, $active) //战斗中在战斗轮步进阶段减少异常状态持续时间
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		if (\skillbase\skill_query(6,$pa)) \c_battle\change_inf_turns($pa,'u');
	}
	
	function get_att_multiplier(&$pa,&$pd,$active)	//烧伤攻击力下降
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa,$pd,$active);
		if (\skillbase\skill_query(6,$pa)) {
			$var = 0.6;
			array_unshift($ret, $var);
		}
		return $ret;
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())	//烧伤发作死亡新闻
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		
		if($news == 'death18') 
		{
			$dname = $typeinfo[$b].' '.$a;
			if(!$e)
				$e0="<span class=\"yellow b\">【{$dname} 什么都没说就死去了】</span><br>\n";
			else  $e0="<span class=\"yellow b\">【{$dname}：“{$e}”】</span><br>\n";
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">$a</span>因<span class=\"red b\">烧伤发作</span>死亡{$e0}</li>";
		} 

		if($news == 'death121') 
		{
			$dname = $typeinfo[$b].' '.$a;
			if(!$e)
				$e0="<span class=\"yellow b\">【{$dname} 什么都没说就死去了】</span><br>\n";
			else  $e0="<span class=\"yellow b\">【{$dname}：“{$e}”】</span><br>\n";
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">$a</span>在与<span class=\"yellow b\">$c</span>的战斗中因<span class=\"red b\">烧伤发作</span>死亡{$e0}</li>";
		} 
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>
