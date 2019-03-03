<?php

namespace skill1800
{
	//各级要完成的成就名，如果不存在则取低的
	$ach1800_name = array(
		1=>'奇迹的背面',
	);
	
	//各级显示的要求，如果不存在则取低的
	$ach1800_desc= array(
		1=>'在改造陷阱★一发逆转神话★时因改造失败而死',
	);
	
	$ach1800_proc_words = '获得纪录';
	
	$ach1800_unit = '次';
	
	//各级阈值，注意是达到这个阈值则升到下一级
	$ach1800_threshold = array(
		1 => 1,
		999 => NULL
	);
	
	//各级给的切糕奖励
	$ach1800_qiegao_prize = array(
		1 => 233,
	);
	
	//各级给的卡片奖励
	$ach1800_card_prize = array(
		1 => 2000,
	);
	
	function init() 
	{
		define('MOD_SKILL1800_INFO','achievement;');
		define('MOD_SKILL1800_ACHIEVEMENT_ID','1800');
	}
	
	function acquire1800(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(1800,'cnt',0,$pa);
	}
	
	function lost1800(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function ach_finalize_process(&$pa, $data, $achid)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($pa, $data, $achid);
		if($achid == 1800){
			$cnt=\skillbase\skill_getvalue(1800, 'cnt', $pa);
			if($cnt == 203) $ret += 1;
		}
		return $ret;
	}
	
	//记录奇迹雷死亡的部分写在地雷改造模组里了
}

?>