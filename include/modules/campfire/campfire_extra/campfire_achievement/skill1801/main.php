<?php

namespace skill1801
{
	//各级要完成的成就名，如果不存在则取低的
	$ach1801_name = array(
		1=>'女武神的骑行',
		2=>'恩赫里亚之宴',
		3=>'世界之敌',
	);
	
	//各级显示的要求，如果不存在则取低的
	$ach1801_desc= array(
		1=>'推开大门，进入英灵殿',
		2=>'点亮英灵殿内所有的区域',
		3=>'登上云之阶，并穿过云层',
	);
	
	$ach1801_proc_words = '巡礼阶段';
	
	$ach1801_unit = '层';
	
	//各级阈值，注意是达到这个阈值则升到下一级
	$ach1801_threshold = array(
		1 => 1,
		2 => 2,
		3 => 3,
		999 => NULL
	);
	
	//各级给的切糕奖励
	$ach1801_qiegao_prize = array(
		1 => 444,
	);
	
	//各级给的卡片奖励
	$ach1801_card_prize = array(
		3 => 2002,
	);
	
	function init() 
	{
		define('MOD_SKILL1801_INFO','achievement;');
		define('MOD_SKILL1801_ACHIEVEMENT_ID','1801');
	}
	
	function acquire1801(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(1801,'cnt',0,$pa);
	}
	
	function lost1801(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function ach_finalize_process(&$pa, $data, $achid)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $data, $achid);
		if($achid == 1801){
			$cnt=\skillbase\skill_getvalue(1801, 'cnt', $pa);
			if($ret<1 && $cnt>=1) $ret++;
			if($ret>=1 && $ret<2 && $cnt>=2) $ret++;
			if($ret>=2 && $ret<3 && $cnt>=3) $ret++;			
		}
		return $ret;
	}
	
	//还差一个移动后加cnt的判断，明天再写
	//写在模式98的文件里了，偷个懒对不起，以后有机会再移出来
}
?>