<?php

namespace campfire_item_kghf
{
	function init() 
	{
		eval(import_module('itemmain'));
		$iteminfo['kghf'] = '特效补给';
	}
	
	function itemuse_kghf(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','skillbase'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if(strpos($itm,'测试用补给')!==false)
		{
			$log.="你使用了测试用特效补给来获得BUFF。<br>";
			$lasttime = 15;
			$starttime = $now;
			\skillbase\skill_acquire(2900);
			\skillbase\skill_setvalue(2900,'start',$starttime);
			\skillbase\skill_setvalue(2900,'end',$starttime+$lasttime);
			\skillbase\skill_setvalue(2900,'add_buff_effect',200);
			\skillbase\skill_setvalue(2900,'del_buff_effect',200);
		}
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if ($itmk=='kghf') {
			itemuse_kghf($theitem);
			return;
		}
		$chprocess($theitem);
	}
}

?>
