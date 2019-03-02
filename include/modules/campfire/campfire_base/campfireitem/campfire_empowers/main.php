<?php

namespace campfire_empowers
{
	function init() {}
	
	function parse_itmuse_desc($n, $k, $e, $s, $sk){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($n, $k, $e, $s, $sk);
		if(strpos($k,'EI')===0){
			if ($n == '天空熔炉的远古之魂') {
				$ret .= '<br>固定增加1.5倍武器效果值<br>若武器耐久度为无限，则效果提升量为2.25倍';
			}elseif ($n == '天空熔炉的锻者之魂') {
				$ret .= '<br>固定将武器类别转化为熟练类别<br>若一次消耗两个锻者之魂，可使武器发生质变';
			}elseif ($n == '天空熔炉的祝祷之魂') {
				$ret .= '<br>祝福你的武器，令它的攻击面得到拓展';
			}
		}
		return $ret;
	}
	
	function use_skysoul($itm,$stp='',&$itms)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','itemmain','logger','empowers','dualwep','weapon'));
		
		if (! $weps || ! $wepe) 
		{
			$log .= '请先装备武器。<br>';
			return 0;
		}
		//判定哪个是最擅长系，只看纸面数字
		$skill = array();
		foreach($skillinfo as $skiv){
			$skill[$skiv] = ${$skiv};
		}
		arsort ( $skill );
		$skill_keys = array_keys($skill);
		$nowsk = $skillinfo[substr($wepk,1,1)];
		//双系只要有任一系擅长就不会改擅长的那系
		$sec_wepk = \dualwep\get_sec_attack_method($sdata, 1);
		if($sec_wepk) {
			$secsk = $skillinfo[$sec_wepk];
			if(${$secsk} > ${$nowsk}) $nowsk = $secsk;
		}
		$maxsk = $skill_keys [0];
		$sec_maxsk = $skill_keys [1];
		//正常改系列表
		$changek = array('wp' => 'WP', 'wk' => 'WK', 'wg' => 'WG', 'wc' => 'WC', 'wd' => 'WD', 'wf' => 'WF');
		//进阶改系列表
		$evo_changek = array('WG' => 'WJ', 'WC' => 'WB', 'WFG' => 'WFJ', 'WGF' => 'WJF', 'WDG' => 'WDJ', 'WGD' => 'WJD', 'WFC'=>'WFB','WCF'=>'WBF');
		if($stp=='远古')
		{
			//效果判断
			$add_wepe = $weps=='∞' ? 2.25 : 1.5;
			$wepe += ceil ( $wepe / $add_wepe );
			$kind = "提高了{$wep}的<span class=\"yellow\">攻击力</span>！";
		}
		elseif($stp=='锻者')
		{
			if ($skill [$nowsk] != $skill [$maxsk])
			{
				$wepk = $changek[$maxsk]. substr($wepk,2);
				$kind = "将{$wep}的类别变化成了<span class=\"yellow\">{$iteminfo[$wepk]}</span>！";
			}
			elseif ((in_array($wepk,array_keys($evo_changek))) && $itms>=2)
			{
				$wepk = $evo_changek[$wepk];
				$kind = "将{$wep}的类别变化成了<span class=\"yellow\">{$iteminfo[$wepk]}</span>！";
				$itms--;
			}	
			else
			{
				$log .= "你的武器类别和你的最高熟练系别相同，无法改造！<br>";
				return 0;
			}
		}
		elseif($stp=='祝祷')
		{
			//获取你最熟悉的武器类别
			$wepk_a = substr($changek[$maxsk],1);
			//第二熟悉的武器类别
			$wepk_b = substr($changek[$sec_maxsk],1);
			//这是什么傻屌判断
			$new_wepk_a = 'W'.$wepk_a.$wepk_b;
			$new_wepk_b = 'W'.$wepk_b.$wepk_a;
			if (in_array($new_wepk_a,array_keys($dualwep_iteminfo)) && $new_wepk_a!==$wepk && $new_wepk_b!==$wepk) 
			{
				$wepk = $new_wepk_a;
				$kind = "将{$wep}的类别变化成了<span class=\"yellow\">{$iteminfo[$wepk]}</span>！";
			}
			else
			{
				$log .= "你的武器无法再受到祝福了！……也许你可以尝试一下锻者之魂？<br>";
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
		
		if (strpos ( $itmk, 'EI' ) === 0) 
		{
			if ($itm == '天空熔炉的远古之魂') 
			{
				if (use_skysoul($itm,'远古',$itms)) \itemmain\itms_reduce($theitem);
				return;
			}
			elseif ($itm == '天空熔炉的锻者之魂') 
			{
				if (use_skysoul($itm,'锻者',$itms)) \itemmain\itms_reduce($theitem);
				return;
			}
			elseif ($itm == '天空熔炉的祝祷之魂') 
			{
				if (use_skysoul($itm,'祝祷',$itms)) \itemmain\itms_reduce($theitem);
				return;
			}
		}
		$chprocess($theitem);
	}
}

?>
