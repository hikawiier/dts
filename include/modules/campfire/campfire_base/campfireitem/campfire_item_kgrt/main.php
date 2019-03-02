<?php

namespace campfire_item_kgrt
{
	function init() 
	{
		eval(import_module('itemmain'));
		$iteminfo['kgrt'] = '传送设备';
	}
	
	function itemuse_kgrt(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','campfire_hiddenarea','map'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if($itmk=='kgrt')
		{
			if(!is_numeric($itmsk))
			{
				$log.="这个传送器好像已经坏掉了……<br>";
				return;
			}
			$defaultpls = settype($itmsk,'int');
			\campfire_hiddenarea\campfire_teleport($defaultpls);		
			add_tp_news($name,$pls,$defaultpls);
			//不同的log和是否赠送固定的返程道具，log根据道具名判定，没有则为默认log，有提供固定返程道具或者不给返程的把flag改成true，固定给的直接在log下面加道具类型，记得加itemget()
			$fix_return_flag = false;
			if($itm=='安全（？）传送装置')
			{
				$log.="你被{$itm}传送到了{$plsinfo[$pls]}，不管怎么说到底还是活下来了！所以还算安全……吧？<br>";
				$fix_return_flag = true;
			}
			else
			{
				$log.="你biu的一下就被传送到了{$plsinfo[$pls]}。<br>";
			}
			//最后是默认赠送的返程道具
			if($return_flag)
			{
				$log.="顺便，你还获得了一个返程道具，真是太划算了……？<br>";
				$itm0='安全（？）传送装置';
				$itmk0='kgrt';
				$itme0=1;
				$itms0=1;
				$itmsk0=$itmsk;
				\itemmain\itemget();				
			}
			\itemmain\itms_reduce($theitem);	
		}
	}
	function add_tp_news($name,$pls,$defaultpls)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','logger','map'));	
		if((in_array($pls,$hidden_arealist))&&(in_array($defaultpls,$hidden_arealist)))
		{
			return;
		}
		elseif($pls == $defaultpls)
		{
			addnews($now,'kgtp_back_ground',$name,$pls);
		}
		elseif(in_array($pls,$hidden_arealist))
		{
			addnews($now,'kgtp_tpto_unknown',$name,$defaultpls);
		}
		elseif(in_array($defaultpls,$hidden_arealist))
		{
			addnews($now,'kgtp_backfrom_unknown',$name,$pls);
		}
		else
		{
			addnews($now,'kgtp_normal',$name,$pls,$defaultpls);
		}
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if ($itmk=='kgrt') {
			itemuse_kgrt($theitem);
			return;
		}
		$chprocess($theitem);
	}
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));
		if($b) $b = $plsinfo[$b];
		if($c) $c = $plsinfo[$c];
		if($news == 'kgtp_back_ground') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">位于{$b}的{$a}尝试进行传送，但又回到了原地，真是遗憾。</span><br>\n";
		if($news == 'kgtp_tpto_unknown') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">在一阵短暂的空间扭曲之后，原本位于{$b}的{$a}从幻境中离奇消失了！</span><br>\n";
		if($news == 'kgtp_backfrom_unknown') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">在一阵短暂的空间扭曲之后，原本从幻境中消失的{$a}又回到了{$b}！</span><br>\n";
		if($news == 'kgtp_normal') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">在一阵短暂的空间扭曲之后，原本位于{$b}的{$a}忽然出现在了{$c}！</span><br>\n";
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e);
	}
}

?>
