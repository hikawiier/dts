<?php

namespace item_urg
{
	function init() 
	{
		eval(import_module('itemmain'));
		$iteminfo['RG'] = '枪械部件';
	}
	
	function itemuse_urg(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','remakegun'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		for($i=1;$i<=6;$i++)
		{
			if(${'itm'.$i}==$itm && ${'itmk'.$i}==$itmk && ${'itme'.$i}==$itme && ${'itms'.$i}==$itms && ${'itmsk'.$i}==$itmsk)
			{
				$r=$i;
				break;
			}
		}
		\remakegun\remake_gun('r_item',$r);
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if ($itmk=='RG') {
			itemuse_urg($theitem);
			return;
		}
		$chprocess($theitem);
	}
}

?>
