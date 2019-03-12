<?php

namespace skill1902
{
	//使对手先手率降低100
	$skill_1902_obbs = 100;
	$min_skill_1902_obbs =15;
	
	function init() 
	{
		define('MOD_SKILL1902_INFO','card;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1902] = '灵动';
	}
	
	function acquire1902(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1902'));
		\skillbase\skill_setvalue(1902,'var',$skill_1902_obbs,$pa);
	}
	
	function lost1902(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1902'));
		\skillbase\skill_delvalue(1902,'var',$pa);
	}
	
	function check_unlocked1902(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function get_var1902(&$ldata, &$edata){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = 0;
		if(\skillbase\skill_query(1902,$edata) && check_unlocked1902($edata)){
			$ret = \skillbase\skill_getvalue(1902,'var',$edata);
		}
		return $ret;
	}
	
	//实际上是使主动探索者的先制率下降
	function calculate_active_obbs(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$var_1902 = get_var1902($ldata, $edata);
		if($var_1902) $ldata['active_words'] = \attack\add_format(-$var_1902, $ldata['active_words'],0);
		return $chprocess($ldata,$edata)-$var_1902;
	}
	
	
	//技能持有者每进行一次攻击，该技能效果降下降一半
	function attack_finish(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa,$pd,$active);
		if(\skillbase\skill_query(1902,$pa))
		{
			$skill1902_var = (int)\skillbase\skill_getvalue(1902,'var',$pa);
			$skill1902_var = min(150,max($min_skill_1902_obbs,$skill1902_var/2));
			\skillbase\skill_setvalue(1902,'var',$skill1902_var,$pa);
		}
	}
}

?>