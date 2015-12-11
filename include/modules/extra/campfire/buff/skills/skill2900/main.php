<?php

namespace skill2900
{
	function init() 
	{
		define('MOD_SKILL2900_INFO','hidden');
	}
	
	function acquire2900(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		$msp+=200;$sp+=200;
		$att+=100;$def+=100;
	}
	
	function lost2900(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		$msp-=200;$sp-=200;
		$att-=100;$def-=100;
	}
	
	function skill_onload_event(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		$chprocess($pa);
	}
	
	function itemuse(&$theitem) 
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('logger','skill2900'));
		if ((strpos ( $theitem['itm'], '溶剂SCP-294' ) !==false)&&(\skillbase\skill_query(2900))&&(check_skill2900_state()==1)) 
		{
			$log .= '你把试剂举到嘴边，但是感觉实在是喝不下去了，还是等一会儿再喝吧。<br>';
			$mode = 'command';
			return;
		}
		$chprocess($theitem);
	}
	
	function check_skill2900_state(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','skill2900'));
		if (!\skillbase\skill_query(2900)) return 0;
		$e=\skillbase\skill_getvalue(2900,'end');
		if ($now<$e) return 1;
		return 0;
	}
		
	function bufficons_list()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		\player\update_sdata();
		if (\skillbase\skill_query(2900,$sdata))
		{
			eval(import_module('skill2900','skillbase'));
			$skill2900_start = (int)\skillbase\skill_getvalue(2900,'start'); 
			$skill2900_end = (int)\skillbase\skill_getvalue(2900,'end'); 
			$z=Array(
				'disappear' => 1,
				'clickable' => 0,
				'hint' => '状态「饱腹」<br>体力上限+200<br>攻击力和防御力+100',
			);
			if ($now<$skill2900_end)
			{
				$z['style']=1;
				$z['totsec']=$skill2900_end-$skill2900_start;
				$z['nowsec']=$now-$skill2900_start;
			}
			else 
			{
				$z['style']=4;
			}
			\bufficons\bufficon_show('img/skill2900.gif',$z);
		}
		$chprocess();
	}
}

?>
