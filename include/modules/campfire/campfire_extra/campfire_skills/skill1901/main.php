<?php

namespace skill1901
{
	function init() 
	{
		define('MOD_SKILL1901_INFO','card;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1901] = '溯源';
	}
	
	function acquire1901(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		//理论上现有剩下的最大复活次数
		$now_rmtime = floor((sizeof($arealist)-$areanum-1)/$areaadd);
		\skillbase\skill_setvalue(1901,'rmtime',$now_rmtime,$pa);
	}
	
	function lost1901(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1901(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function get_remaintime1901(&$pa = NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return (int)\skillbase\skill_getvalue(1901,'rmtime',$pa);
	}
	
	function add_area_once1901()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		$areatime = $now + 30;
		save_gameinfo();
		addnews($now, 'addarea1901');
		\sys\systemputchat($now,'addarea1901','由于不可抗力，禁区的到来被提前至30秒后！');
	}
	
	function check_time_reduce1901()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger'));
		$tmp_skill1901_rmtime = get_remaintime1901();
		//理论上现有剩下的最大复活次数
		$now_rmtime = floor((sizeof($arealist)-$areanum-1)/$areaadd);
		if($now_rmtime < $tmp_skill1901_rmtime) 
		{
			$tmp_skill1901_rmtime = $now_rmtime;
			\skillbase\skill_setvalue(1901,'rmtime',$tmp_skill1901_rmtime);
			$log.= "<span class=\"grey b\">时光飞逝……你所剩下的机会正在逐渐减少。</span><br>";
		}
	}
	
	function post_addarea_process($atime, $areaaddlist)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if(\skillbase\skill_query(1901) && check_unlocked1901()){
			check_time_reduce1901();
		}
		$chprocess($atime, $areaaddlist);
	}
	
	//复活判定注册
	function set_revive_sequence(&$pa, &$pd)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd);
		if(\skillbase\skill_query(1901,$pd) && check_unlocked1901($pd)){
			$pd['revive_sequence'][250] = 'skill1901';
		}
		return;
	}	

	//复活判定
	function revive_check(&$pa, &$pd, $rkey)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger'));
		$ret = $chprocess($pa, $pd, $rkey);
		//说起来现在其他的复活技能，被弓类武器击杀是无法复活的
		//在增加禁区前的30秒内无法复活
		if('skill1901' == $rkey && in_array($pd['state'],Array(20,21,22,23,24,25,27,29,39,40,41,43)) && $areatime>$now+30){
			if(get_remaintime1901($pd) > 0)
			$ret = true;
			add_area_once1901();
		}
		return $ret;
	}
	
	//发复活状况
	function post_revive_events(&$pa, &$pd, $rkey)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $rkey);
		if('skill1901' == $rkey){
			//$pd['hp']=$pd['mhp'];
			$pd['skill1901_flag']=1;
			$rmtime = (int)\skillbase\skill_getvalue(1901,'rmtime',$pd);
			\skillbase\skill_setvalue(1901,'rmtime',$rmtime-1,$pd);
			$pd['rivival_news'] = array('revival1901', $pd['name']);
		}
		return;
	}
	
	function kill(&$pa, &$pd)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$ret = $chprocess($pa,$pd);
		
		eval(import_module('sys','logger'));
		
		if(!empty($pd['skill1901_flag'])){
			if ($pd['o_state']==27)	//陷阱
			{
				$log.= "<span class=\"lime b\">但是，被一种奇妙的力量所指引着，你又从死亡的边缘爬了回来！</span><br>";
				if(!$pd['sourceless']){
					$w_log = "<span class=\"lime b\">但是，{$pd['name']}被一种奇妙的力量所指引，又从死亡的边缘爬了回来！</span><br>";
					\logger\logsave ( $pa['pid'], $now, $w_log ,'b');
				}
			}
		}
		return $ret;
	}
	
	function player_kill_enemy(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$chprocess($pa,$pd,$active);
		
		eval(import_module('sys','logger'));
		if (isset($pd['skill1901_flag']) && $pd['skill1901_flag'])
		{
			if ($active)
			{
				$log.='<span class="lime b">但是，敌人被一种奇妙的力量所指引，又从死亡的边缘爬了回来！</span><br>';
				$pd['battlelog'].='<span class="lime b">但是，被一种奇妙的力量所指引着，你又从死亡的边缘爬了回来！</span>';
			}
			else
			{
				$log.='<span class="lime b">但是，被一种奇妙的力量所指引着，你又从死亡的边缘爬了回来！</span><br>';
				$pd['battlelog'].='<span class="lime b">但是，敌人被一种奇妙的力量所指引，又从死亡的边缘爬了回来！</span>';
			}
		}
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		
		if($news == 'revival1901') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"brickred b\">{$a}被奇妙的力量所指引，从死亡的边缘爬了回来！</span></li>";
		if($news == 'addarea1901') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"red b\">由于不可抗力，禁区的到来被提前至30秒后！</span></li>";
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>