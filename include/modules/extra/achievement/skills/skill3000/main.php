<?php

namespace skill3000
{
	function init() 
	{
		define('MOD_SKILL3000_INFO','achievement;');
		define('MOD_SKILL3000_ACHIEVEMENT_ID','100');
	}
	
	function acquire3000(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(3000,'cnt','0',$pa);
	}
	
	function lost3000(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function skill_onload_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if ((!in_array($gametype,$ach_ignore_mode))&&(!\skillbase\skill_query(3000,$pa))) //也可以做一些只有房间模式有效的成就
			\skillbase\skill_acquire(3000,$pa);
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa);
	}
	
	function finalize3000(&$pa, $data)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($data=='')					
			$x=0;						
		else	$x=base64_decode_number($data);		
		$ox=$x;
		$x+=\skillbase\skill_getvalue(3000,'cnt',$pa);		
		$x=min($x,(1<<30)-1);
		
		if (($ox<1)&&($x>=1)){
			\cardbase\get_qiegao(250,$pa);
		}
		if (($ox<5)&&($x>=5)){
			\cardbase\get_qiegao(400,$pa);
		}
		if (($ox<30)&&($x>=30)){
			\cardbase\get_qiegao(700,$pa);
			\cardbase\get_card(39,$pa);
		}
		
		return base64_encode_number($x,5);		
	}
	
	function player_kill_enemy(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		eval(import_module('cardbase','sys','logger','map'));
		if ((\skillbase\skill_query(3000,$pa))&&($pd['type']==1 && $pd['name']=='红暮'))
		{
			$x=(int)\skillbase\skill_getvalue(3000,'cnt');
			$x+=1;
			\skillbase\skill_setvalue(3000,'cnt',$x,$pa);
		}
	}	
	
	function show_achievement3000($data)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($data=='')
			$p3000=0;
		else	$p3000=base64_decode_number($data);	
		$c3000=0;
		if ($p3000>=30){
			$c3000=999;
		}else if ($p3000>=5){
			$c3000=2;
		}else if ($p3000>=1){
			$c3000=1;
		}
		include template('MOD_SKILL3000_DESC');
	}
}

?>
