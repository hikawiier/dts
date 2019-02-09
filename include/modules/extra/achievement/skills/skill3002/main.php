<?php

namespace skill3002
{
	function init() 
	{
		define('MOD_SKILL3002_INFO','achievement;');
		define('MOD_SKILL3002_ACHIEVEMENT_ID','102');
	}
	
	function acquire3002(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(3002,'cnt','0',$pa);
	}
	
	function lost3002(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function skill_onload_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if ((!in_array($gametype,$ach_ignore_mode))&&(!\skillbase\skill_query(3002,$pa))) //也可以做一些只有房间模式有效的成就
			\skillbase\skill_acquire(3002,$pa);
		$chprocess($pa);
	}
	
	function skill_onsave_event(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa);
	}
	
	function finalize3002(&$pa, $data)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($data=='')					
			$x=0;						
		else	$x=base64_decode_number($data);		
		$ox=$x;
		$x+=\skillbase\skill_getvalue(3002,'cnt',$pa);		
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
		if ((\skillbase\skill_query(3002,$pa))&&($pd['type']==77 && $pd['name']=='Mr.Lein'))
		{
			$x=(int)\skillbase\skill_getvalue(3002,'cnt');
			$x+=1;
			\skillbase\skill_setvalue(3002,'cnt',$x,$pa);
		}
	}	
	
	function show_achievement3002($data)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($data=='')
			$p3002=0;
		else	$p3002=base64_decode_number($data);	
		$c3002=0;
		if ($p3002>=30){
			$c3002=999;
		}else if ($p3002>=5){
			$c3002=2;
		}else if ($p3002>=1){
			$c3002=1;
		}
		include template('MOD_SKILL3002_DESC');
	}
}

?>
