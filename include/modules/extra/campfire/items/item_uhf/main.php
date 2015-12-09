<?php

namespace item_uhf
{
	function init() 
	{
		eval(import_module('itemmain'));
		$iteminfo['HF'] = '食物';
	}
	
	function itemuse_uhf(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','skillbase','skill2900'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		if($itm=='大补锅')
		{
			$log.="喵，你吃饱了！<br>";
			$buff_last_time = 15;
			if (!\skillbase\skill_query(2900,$pa)){
				\skillbase\skill_acquire(2900,$pa);
				$buff_start_time=$now;
			}else{
				$buff_start_time=\skillbase\skill_getvalue(2900,'end',$pa);
				if ($buff_start_time<$now) $buff_start_time=$now;
			}
			\skillbase\skill_setvalue(2900,'start',$buff_start_time,$pa);
			\skillbase\skill_setvalue(2900,'end',$buff_start_time+$buff_last_time,$pa);
		}
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if ($itmk=='HF') {
			itemuse_uhf($theitem);
			return;
		}
		$chprocess($theitem);
	}
}

?>
