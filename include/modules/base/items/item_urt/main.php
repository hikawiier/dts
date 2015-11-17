<?php

namespace item_urt
{
	function init() 
	{
		eval(import_module('itemmain'));
		$iteminfo['RT'] = '传送道具';
	}
	
	function itemuse_urt(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','itemmain','logger'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if($itmk=='RT'){
			//传送道具的效果判定区
			//首先是传送器效果,0~98为固定传送，99为随机传送
			if($itmsk==99){
				if($hack){$rpls = rand(0,sizeof($plsinfo)-sizeof($hidden_arealist)-1);}
				else {$rpls = rand($areanum+1,sizeof($plsinfo)-sizeof($hidden_arealist)-1);}
				$pls = $arealist[$rpls];
			}else{
				$pls = $itmsk;
			}
			//然后是不同的log和是否赠送固定的返程道具，log根据道具名判定，没有则为默认log，有提供固定返程道具或者不给返程的把flag改成true，固定给的直接在log下面加道具类型，记得加itemget()
			$fix_return_flag = false;
			if($itm=='测试用传送器'){
				$log.="你biu的一下就被传送到了{$plsinfo[$pls]}。<br>";
			}elseif($itm=='安全（？）传送装置'){
				$log.="你被{$itm}传送到了{$plsinfo[$pls]}，不管怎么说到底还是活下来了！所以还算安全……吧？<br>";
				$fix_return_flag = true;
			}else{
				$log.="你被{$itm}传送到了{$plsinfo[$pls]}，顺便还获得了一个返程道具，真是太划算了！<br>";
			}
			//最后是默认赠送的返程道具
			if(!$fix_return_flag){
				$itm0='安全（？）传送装置';
				$itmk0='RT';
				$itme0=1;
				$itms0=1;
				$itmsk0=99;
				\itemmain\itemget();				
			}
			\itemmain\itms_reduce($theitem);
		}
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if ($itmk=='RT') {
			itemuse_urt($theitem);
			return;
		}
		$chprocess($theitem);
	}
}

?>
