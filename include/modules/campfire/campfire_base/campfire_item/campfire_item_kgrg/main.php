<?php

namespace campfire_item_kgrg
{
	function init() 
	{
		eval(import_module('itemmain'));
		$iteminfo['kgrg'] = '枪械部件';
	}
	
	function itemuse_urg(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','campfire_areafeatures_transforgun'));
		
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
		\campfire_areafeatures_transforgun\remake_gun('r_item',$r);
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if ($itmk=='kgrg') {
			itemuse_urg($theitem);
			return;
		}
		$chprocess($theitem);
	}
}

?>
