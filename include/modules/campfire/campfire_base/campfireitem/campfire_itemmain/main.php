<?php

namespace campfire_itemmain
{
	function init() 
	{	
	}
	function parse_itmuse_desc($n, $k, $e, $s, $sk){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($n, $k, $e, $s, $sk);
		if(strpos($k,'Y')===0 || strpos($k,'Z')===0){
			if ($n == '自适应型溶剂TA-E3') {
				$ret .= '能够将你的称号转变为天赋异禀，并返还较少的技能点';
			}elseif ($n == '自适应型溶剂TA-C3') {
				$ret .= '能够将你的称号转变为天赋异禀，并返还全部的技能点';
			}elseif ($n == '自适应型溶剂TA-00') {
				$ret .= '能够将你的称号转变为天赋异禀，并获得更多的技能点';
			}elseif (strpos($n,'自适应型溶剂')!==false) {
				$ret .= '似乎能让你变得更有智慧的神秘溶剂';
			}elseif (strpos($n,'灵能人形')!==false) {
				$ret .= '被注入了神秘力量的小纸人……有什么办法能让它动起来吗？';
			}
		}
		return $ret;
	}
	
	function get_alone_card_skill(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','clubbase','cardbase'));
		$cid = ($pa == NULL || $pa['pid']==$ppid) ? $card : $pa['card'];
		if(is_array($cards[$card]['skills']))
		{
			$card_array = $cards[$cid];  
		}
		else
		{
			return;
		}
		foreach ($card_array['skills'] as $sk => $sklvl)
		{
			$flag = 0;
			if($card_array['club'])
			{
				foreach(array_keys($clublist) as $clubnum)
				{
					if(in_array($sk,$clublist[$clubnum]['skills']))
					{
						$flag = 1;
					}
				}
			}		
			if (defined('MOD_SKILL'.$sk) && !$flag) 
			{
				if ($pa == NULL || $pa['pid']==$ppid)
				{
					\skillbase\skill_acquire($sk);
					\skillbase\skill_setvalue($sk,'lvl',$sklvl);	
				}
				else
				{
					\skillbase\skill_acquire($sk,$pa);
					\skillbase\skill_setvalue($sk,'lvl',$sklvl,$pa);	
				}
			}					
		}
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','logger','itemmain','input','clubbase','cardbase'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if(strpos($itm,'自适应型溶剂')!==false && $itmk=='Z')
		{
			$clever_flag = true;
			if(strpos($itm,'TA-00'))
			{
				$add_skillpoint = round($lvl*2+3);	
			}
			elseif(strpos($itm,'TA-C3'))
			{
				$add_skillpoint = round($lvl)+3;	
			}
			elseif(strpos($itm,'TA-E3'))
			{
				$add_skillpoint = round($lvl/2)+3;	
			}
			else
			{
				$add_skillpoint = rand(1,3);	
				$clever_flag = false;
			}
			if(($club==18 || $club==17) && $clever_flag)
			{
				$log.="你感觉自己的胃在看到试剂的第一眼时就开始不断抽搐，这东西究竟有多恶心啊……还是不要随便喝了。<br>";
				return;
			}
			$log.="你看着面前散发着不祥气息的粉色试剂，皱着眉头将它一饮而尽。<br>没有想象中的那么难喝，尝起来似乎是草莓味的……<br><span class='lime b'>你感觉自己好像变得更聪明了，又好像没有。</span><br>";
			if($clever_flag)
			{
				if (defined('MOD_CLUBBASE')) \clubbase\club_lost();
				$club = 18;
				if (defined('MOD_CLUBBASE')) \clubbase\club_acquire($club);
				get_alone_card_skill();	
				$log.="你的称号变为了<span class='yellow b'>{$clubinfo[18]}</span>！<br>";
			}
			$skillpoint+=$add_skillpoint;
			$log.="你获得了<span class='yellow b'>{$add_skillpoint}</span>点技能点！<br>";
			\itemmain\itms_reduce($theitem);
			return;
		}
		elseif(strpos($itm,'灵能人形')!==false)
		{
			$scai_flag = false;
			for($i=0;$i<=6;$i++)
			{
				if(${'itms'.$i} && strpos(${'itm'.$i},'寻物者')!==false)
				{
					$scai_flag = true;
					if(${'itme'.$i}<5)
					{
						$log.="你将人形插在了黄铜圆盘的凹槽上，看起来正合适。<br><span class='yellow b'>寻物者的数量增加了！</span><br>";
						${'itme'.$i}++;
						\itemmain\itms_reduce($theitem);
					}
					else
					{
						$log.="<span class='red b'>寻物者的数量已经达到上限了！</span><br>";
					}	
					break;
				}
			}
			if(!$scai_flag)
			{
				$log.="你翻了翻背包，看起来没有用得上这东西的地方。<br>";
			}	
			return;
		}
		$chprocess($theitem);
	}
}

?>
