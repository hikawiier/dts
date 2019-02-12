<?php

namespace campfire_itemmain
{
	function init() 
	{
		eval(import_module('itemmain'));
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
		
		if(strpos($itm,'自适应型溶剂')!==false && $itmk=='Y')
		{
			if($club==18 || $club==17)
			{
				$log.="你感觉自己的胃在看到试剂的第一眼时就开始不断抽搐，这东西究竟有多恶心啊……还是不要随便喝了。<br>";
				return;
			}
			if (defined('MOD_CLUBBASE')) \clubbase\club_lost();
			$club = 18;
			if (defined('MOD_CLUBBASE')) \clubbase\club_acquire($club);
			get_alone_card_skill();	
			if(strpos($itm,'TA-00'))
			{
				$skillpoint += round($lvl+$lvl/2+3);	
			}
			elseif(strpos($itm,'TA-C3'))
			{
				$skillpoint = round($lvl+$lvl/2+3);	
			}
			elseif(strpos($itm,'TA-E3'))
			{
				$skillpoint = $lvl+3;	
			}
			else
			{
				$skillpoint += 3;	
			}
			$log.="你看着面前散发着不祥气息的粉色试剂，皱着眉头将它一饮而尽。<br>没有想象中的那么难喝，尝起来似乎是草莓味的……<br><span class='lime'>你感觉自己好像变得更聪明了，又好像没有。</span><br>";
			\itemmain\itms_reduce($theitem);
			return;
		}
		$chprocess($theitem);
	}
}

?>
