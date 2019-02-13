<?php

namespace campfire_hiddenarea
{
	function init() 
	{
		eval(import_module('itemmain'));
		//新的道具类别
		$iteminfo['kgrt'] = '传送道具';
		//不会有物品掉落的地区列表
		$map_noitemdrop_arealist[] = 99;
		$map_noitemdrop_arealist[] = 98;
		$map_noitemdrop_arealist[] = 97;	
	}	
	function parse_itmuse_desc($n, $k, $e, $s, $sk)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($n, $k, $e, $s, $sk);
		if(strpos($k,'kgrt')!==false)
		{
			if ($k == 'kgrt'){
				$ret .= '使用后可以将你传送到指定地点';
			}
		}
		return $ret;
	}		
	function campfire_teleport($tpto=99,$npls)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger'));		
		if($tpto==99)
		{
			$pls_available = \map\get_safe_plslist(0);//如果只能移动到危险区域，就移动到危险区域
			shuffle($pls_available);
			$tpls = $pls_available[0];
		}
		else
		{
			$tpls = $tpto;
		}		
		$pls = $tpls;
		if((array_search($tpls,$hidden_arealist))&&(array_search($npls,$hidden_arealist)))
		{
		}	
		elseif($tpls == $npls)
		{
			addnews($now,'kgtp_background',$name,$npls);
		}
		elseif(array_search($tpls,$hidden_arealist))
		{
			addnews($now,'kgtp_tptounknown',$name,$npls);
		}
		elseif(array_search($npls,$hidden_arealist))
		{
			addnews($now,'kgtp_backfromunknown',$name,$tpls);
		}
		else
		{
			addnews($now,'kgtp_normal',$name,$npls,$tpls);
		}
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger'));	
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if ($itmk=='kgrt') {
			if(!is_numeric($itmsk))
			{
				$log.="这个传送器好像已经坏掉了……<br>";
				return;
			}
			campfire_teleport($itmsk,$pls);
			\itemmain\itms_reduce($theitem);	
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
			return;
		}
		$chprocess($theitem);
	}
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));

		if($b) $b = $plsinfo[$b];
		if($c) $c = $plsinfo[$c];

		if($news == 'kgtp_background') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">位于{$b}的{$a}尝试进行传送，但又回到了原地，真是遗憾。</span><br>\n";
		elseif($news == 'kgtp_tptounknown') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">在一阵短暂的空间扭曲之后，原本位于{$b}的{$a}从幻境中离奇消失了！</span><br>\n";
		elseif($news == 'kgtp_backfromunknown') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">在一阵短暂的空间扭曲之后，原本从幻境中消失的{$a}又回到了{$b}！</span><br>\n";
		elseif($news == 'kgtp_normal') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">在一阵短暂的空间扭曲之后，原本位于{$b}的{$a}忽然出现在了{$c}！</span><br>\n";

		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e);
	}
}

?>
