<?php

namespace campfire_boxes
{
	function init()
	{
		eval(import_module('itemmain'));
		$iteminfo['kggb'] = '军火箱';
		$iteminfo['kgab'] = '弹药箱';
	}

	function itemuse(&$theitem) 
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','itemmain','logger'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if(strpos ( $itmk, 'kgab' ) === 0){
			$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
			$file = __DIR__.'/config/kgabbox.config.php';
			$plist1 = openfile($file);
			$rand1 = rand(0,count($plist1)-1);
			list($in,$ik,$ie,$is,$isk) = explode(',',$plist1[$rand1]);
			$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
			addnews($now,'present',$name,$itm,$in);
			\itemmain\itms_reduce($theitem);
			\itemmain\itemget();	
			return;
		} elseif(strpos ( $itmk, 'kggb' ) === 0){
			$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
			$file = __DIR__.'/config/kggbbox.config.php';
			$plist1 = openfile($file);
			$rand1 = rand(0,count($plist1)-1);
			list($in,$ik,$ie,$is,$isk) = explode(',',$plist1[$rand1]);
			$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
			addnews($now,'present',$name,$itm,$in);
			\itemmain\itms_reduce($theitem);
			\itemmain\itemget();	
			return;
		}
		$chprocess($theitem);
	}		
}

?>
