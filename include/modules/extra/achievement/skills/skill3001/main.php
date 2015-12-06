<?php

namespace skill3001
{
	function init() 
	{
		define('MOD_SKILL3001_INFO','achievement;');
		define('MOD_SKILL3001_ACHIEVEMENT_ID','101');
	}
	
	function acquire3001(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(3001,'cnt','0',$pa);
	}
	
	function lost3001(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function skill_onload_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if ((!in_array($gametype,$ach_ignore_mode))&&(!\skillbase\skill_query(3001,$pa))) //也可以做一些只有房间模式有效的成就
			\skillbase\skill_acquire(3001,$pa);
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa);
	}
	
	function finalize3001(&$pa, $data)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($data=='')					
			$x=0;						
		else	$x=base64_decode_number($data);		
		$ox=$x;
		$x+=\skillbase\skill_getvalue(3001,'cnt',$pa);		
		$x=min($x,(1<<30)-1);
		
		if (($ox<1)&&($x>=1)){
			\cardbase\get_qiegao(300,$pa);
		}
		if (($ox<5)&&($x>=5)){
			\cardbase\get_qiegao(500,$pa);
		}
		if (($ox<30)&&($x>=30)){
			\cardbase\get_qiegao(800,$pa);
			\cardbase\get_card(40,$pa);
		}
		
		return base64_encode_number($x,5);		
	}
	
	function player_kill_enemy(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		eval(import_module('cardbase','sys','logger','map'));
		if ((\skillbase\skill_query(3001,$pa))&&($pd['type']==9 && $pd['name']=='蓝凝'))
		{
			$x=(int)\skillbase\skill_getvalue(3001,'cnt');
			$x+=1;
			\skillbase\skill_setvalue(3001,'cnt',$x,$pa);
		}
	}	
	
	function show_achievement3001($data)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($data=='')
			$p3001=0;
		else	$p3001=base64_decode_number($data);	
		$c3001=0;
		if ($p3001>=30){
			$c3001=999;
		}else if ($p3001>=5){
			$c3001=2;
		}else if ($p3001>=1){
			$c3001=1;
		}
		include template('MOD_SKILL3001_DESC');
	}
}

?>
