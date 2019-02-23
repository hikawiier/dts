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
				$ret .= '将手中武器的类别转化为熟练类别，并使其恢复原初的种类';
			}elseif ($n == '天空熔炉的祝祷之魂') {
				$ret .= '锤锻你的武器，使其质变';
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
		$skill = array ('WP' => $wp, 'WK' => $wk, 'WG' => $wg, 'WC' => $wc, 'WD' => $wd, 'WF' => $wf );
		$frk = array('WG'=>'WJ','WC'=>'WB');
		arsort ( $skill );
		$skill_keys = array_keys ( $skill );
		$nowsk = substr ( $wepk, 0, 2 );
		$maxsk = $skill_keys [0];
		if($stp=='远古')
		{
			$wepe += ceil ( $wepe / 1.5 );
			$kind = "提高了{$wep}的<span class=\"yellow\">攻击力</span>！";
		}
		elseif($stp=='锻者')
		{
			if ($skill [$nowsk] != $skill [$maxsk]) 
			{
				$wepk = $maxsk;
				$kind = "将{$wep}的类别变化成了<span class=\"yellow\">{$iteminfo[$wepk]}</span>！";
			}
			else
			{
				$log .= "你的武器类别和你的最高熟练系别相同，无法改造！<br>";
				return 0;
			}
		}
		elseif($stp=='祝祷')
		{
			if (in_array($nowsk,array_keys($frk))) 
			{
				$wepk = $frk[$nowsk];
				$kind = "将{$wep}的类别变化成了<span class=\"yellow\">{$iteminfo[$wepk]}</span>！";
			}
			else
			{
				$log .= "你的武器无法再进行锤锻了！……也许你可以尝试一下锻者之魂？<br>";
				return 0;
			}
		}
		else
		{
			$log .= "使用道具信息错误。{$type}<br>";
			return 0;
		}
		$log .= "你使用了<span class=\"yellow\">$itm</span>，<br>{$stp}的回响附着在了你的武器之上，用一种你无法理解的语言低声呢喃着。<br>{$stp}之魂{$kind}<br>";
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
			elseif ($itm == '天空熔炉的锻者之魂') 
			{
				if (use_skysoul($itm,'祝祷')) \itemmain\itms_reduce($theitem);
				return;
			}
		}
		$chprocess($theitem);
	}
}

?>
