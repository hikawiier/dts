<?php

namespace campfire_empowers
{
	function init() {}
	
	function parse_itmuse_desc($n, $k, $e, $s, $sk){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($n, $k, $e, $s, $sk);
		if(strpos($k,'Y')===0 || strpos($k,'Z')===0){
			if ($n == '天空熔炉的远古之魂') {
				$ret .= '固定强化手中武器的效果值1.5倍';
			}elseif ($n == '天空熔炉的锻者之魂') {
				$ret .= '将手中武器的类别转化为你最为擅长的类别';
			}
		}
		return $ret;
	}
	
	function use_skysoul($itm,$stp='')
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','itemmain','logger','empowers'));
		
		if (! $weps || ! $wepe) 
		{
			$log .= '请先装备武器。<br>';
			return 0;
		}
		$dice = rand ( 0, 99 );
		$dice2 = rand ( 0, 99 );
		$skill = array ('WP' => $wp, 'WK' => $wk, 'WG' => $wg, 'WC' => $wc, 'WD' => $wd, 'WF' => $wf );
		$frk = Array('WJ' => 'WG');
		arsort ( $skill );
		$skill_keys = array_keys ( $skill );
		$nowsk = substr ( $wepk, 0, 2 );
		$maxsk = $skill_keys [0];
		$frk_flag = false;
		if((in_array($wepk,array_keys($frk)) && $frk[$wepk]==$maxsk))
		{
			$frk_flag = true;
		}
		if($stp=='远古')
		{
			$wepe += ceil ( $wepe / 1.5 );
			$kind = "提高了{$wep}的<span class=\"yellow\">攻击力</span>！";
		}
		elseif($stp=='锻者')
		{
			if ($skill [$nowsk] != $skill [$maxsk] && !$frk_flag) 
			{
				$wepk = $maxsk;
				$kind = "将{$wep}的类别改造成了<span class=\"yellow\">{$iteminfo[$wepk]}</span>！";
			}
			else
			{
				$log .= "你的武器类别和你的最高熟练系别相同，无法改造！<br>";
				return 0;
			}
		}
		else
		{
			$log .= "使用道具信息错误。{$type}<br>";
			return 0;
		}
		$log .= "你使用了<span class=\"yellow\">$itm</span>，<br>{$stp}的灵魂附着在了你的武器之上，用一种你无法理解的语言低声喃喃着。<br>{$stp}之魂{$kind}<br>";
		addnews ( $now, 'newwep', $name, $itm, $wep );
		if (strpos ( $wep, '-改' ) === false) 
		{
			$wep = $wep . '-改';
		}
		return 1;
	}
	function itemuse(&$theitem) 
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','itemmain','armor','logger'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if (strpos ( $itmk, 'Y' ) === 0 || strpos ( $itmk, 'Z' ) === 0) 
		{
			if ($itm == '天空熔炉的远古之魂') 
			{
				if (use_skysoul($itm,'远古')) \itemmain\itms_reduce($theitem);
				return;
			}
			elseif ($itm == '天空熔炉的锻者之魂') 
			{
				if (use_skysoul($itm,'锻者')) \itemmain\itms_reduce($theitem);
				return;
			}
		}
		$chprocess($theitem);
	}
}

?>
