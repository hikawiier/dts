<?php

namespace weather0
{
	function init() 
	{
		eval(import_module('weathermain'));
		//天气名
		$wthinfo[0]='晴天';
		//天气对物品发现率的影响，加法结合
		$weather_itemfind_obbs[0]=10;
		//天气对人物遭遇率的影响，加法结合
		$weather_meetman_obbs[0]=10;
		//天气对先攻率的影响，加法结合
		$weather_active_obbs[0]=10;
		//天气对攻击力的影响，百分比，加法结合
		$weather_attack_modifier[0]=10;
		//天气对防御力的影响，百分比，加法结合
		$weather_defend_modifier[0]=10;
		//在建筑物环境内是否会受到天气影响，0=不影响，1=影响
		$weather_futher_effect[0]=0;
		//在建筑物环境内是否会受到天气影响，0=不影响，1=影响
		$weather_futher_effect[0]=0;
	}
	
	function calculate_weather_itemfind_obbs()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','weather'));
		return $weather_itemfind_obbs[$weather];
	}
	
	function calculate_itemfind_obbs()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return $chprocess()+calculate_weather_itemfind_obbs();
	}
	
	function calculate_weather_meetman_obbs(&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','weather'));
		return $weather_meetman_obbs[$weather];
	}
	
	function calculate_meetman_obbs(&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return $chprocess($edata)+calculate_weather_meetman_obbs($edata);
	}
	
	function calculate_weather_active_obbs(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','weather'));
		return $weather_active_obbs[$weather];
	}
	
	function calculate_active_obbs(&$ldata,&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return $chprocess($ldata,$edata)+calculate_weather_active_obbs($ldata,$edata);
	}
	
	function calculate_weather_attack_modifier(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','weather'));
		return 1+$weather_attack_modifier[$weather]/100;
	}
	
	function get_att_multiplier(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return $chprocess($pa,$pd,$active)*calculate_weather_attack_modifier($pa,$pd,$active);
	}
	
	function calculate_weather_defend_modifier(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','weather'));
		return 1+$weather_defend_modifier[$weather]/100;
	}
	
	function get_def_multiplier(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return $chprocess($pa,$pd,$active)*calculate_weather_defend_modifier($pa,$pd,$active);
	}
	
	function get_hitrate(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','weather'));
		if($weather == 12)
			return $chprocess($pa,$pd,$active)+20;
		else  return $chprocess($pa,$pd,$active);
	}
}

?>
