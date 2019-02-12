<?php

namespace skill369
{
	//各级要完成的成就名，如果不存在则取低的
	$ach369_name = array(
		1=>'你相信有幽灵吗？',
	);
	
	//各级显示的要求，如果不存在则取低的
	$ach369_desc= array(
		1=>'在一场游戏中以不少于3种方式复活，并获得胜利',
	);
	
	$ach369_proc_words = '获得纪录';
	
	$ach369_unit = '次';
	
	//各级阈值，注意是达到这个阈值则升到下一级
	$ach369_threshold = array(
		1 => 1,
		999 => NULL
	);
	
	//各级给的切糕奖励
	$ach369_qiegao_prize = array(
		1 => 998,
	);
	
	//各级给的卡片奖励
	$ach369_card_prize = array(
		1 => 181,
	);
	
	function init() 
	{
		define('MOD_SKILL369_INFO','achievement;');
		define('MOD_SKILL369_ACHIEVEMENT_ID','69');
	}
	
	function acquire369(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(369,'cnt','',$pa);
	}
	
	function lost369(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function ach_finalize_process(&$pa, $data, $achid)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $data, $achid);
		if($achid == 369){
			eval(import_module('sys'));
			if($winner === $pa['name']) {
				$cnt=\skillbase\skill_getvalue(369, 'cnt', $pa);
				$cnt = array_filter(explode(',', $cnt));
				if(sizeof($cnt) >= 3) $ret += 1;
			}
		}
		return $ret;
	}
	
	//如果没有以这个方式复活过，则记录
	function post_revive_events(&$pa, &$pd, $rkey)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $rkey);
		if(\skillbase\skill_query(369, $pd)){
			$cnt=\skillbase\skill_getvalue(369, 'cnt', $pd);
			$cnt = array_filter(explode(',', $cnt));
			if(!in_array($rkey, $cnt)) {
				$cnt[] = $rkey;
				\skillbase\skill_setvalue(369, 'cnt', implode(',', $cnt), $pd);
			}
		}
		return;
	}
}

?>