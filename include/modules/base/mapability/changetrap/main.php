<?php

namespace changetrap
{
	function init()
	{
	}
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','changetrap','input'));
		/*==========和田特殊功能：changetrap菜单部分开始==========*/
		if($mode == 'lp_changetrap')
		{
			if($command == 'menu')
			{
				$log.="你思考了一下，还是离开了研究所。<br>……改变主意了再来吧。<br>";
			}
			elseif($command == 'trap_to_wd')
			{
				change_trap($i_num_ttd,'ttd');			
			}
			elseif($command == 'wd_to_trap')
			{
				change_trap($i_num_dtt,'dtt');			
			}
			else
			{
				$log.="changetrap指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		/*==========和田特殊功能：changetrap菜单部分结束==========*/
		$chprocess();
	}
	/*==========和田特殊功能：changetrap功能部分开始==========*/
	function change_trap($i,$cway)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','remakegun'));
		if($pls!=31)
		{
			$log.="<span class='red'>该地区不存在陷阱改造工作台，请重新输入指令。</span><br>";
			return;
		}
		if(!is_numeric($i) || !$i || $i>6 || $i<1 || (${'itmk'.$i}!=='WD' && $cway=='dtt') || (strpos(${'itmk'.$i},'T')===false && $cway=='ttd'))
		{
			$log.="<span class='red'>要改造的陷阱或爆炸物选择错误，请重新输入指令。</span><br>";
			return;
		}
		if($cway!=='dtt' && $cway!=='ttd')
		{
			$log.="<span class='red'>改造方式选择错误，请重新输入指令。</span><br>";
			return;
		}
		$citm['itm']=&${'itm'.$i}; $citm['itmk']=&${'itmk'.$i};
		$citm['itme']=&${'itme'.$i}; $citm['itms']=&${'itms'.$i}; $citm['itmsk']=&${'itmsk'.$i};
		if(strpos($citm['itm'],'便携式')!==false || strpos($citm['itm'],'埋设式')!==false)
		{
			$log.="<span class='red'>该道具已被改造过，不可重复改造！</span><br>";
			return;
		}
		$change_succ_obbs = round($wd*0.35);
		
		$log.="你将要改造的易爆品放在了工作台上，开始小心翼翼的拆解它……<br>";
		$log.="…………<br>";
		if($cway=='ttd')
		{
			if(rand(1,100)<=$change_succ_obbs)
			{
				$log.="<span class='yellow'>“呼……”</span><br>完成了手中精密的工作，你如释重负般长吁了一口气。<br>这样看来，陷阱的改造工作就<span class='red'>顺利完成</span>了！<br>";
				if(strpos($citm['itmk'],'TNc')!==false)
				{
					$itm0 = '便携式'.$citm['itm'];
					$itmk0 = 'WD';
					$itme0 = round(32675*(rand(85,135)/100));
					$itms0 = '∞';
					$itmsk0 = 'nxd';
				}
				else
				{
					$itm0 = '便携式'.$citm['itm'];
					$itmk0 = 'WD';
					$itme0 = round($citm['itme']*(rand(85,135)/100));
					$itms0 = $citm['itms'];
					$itmsk0 = 'd';
				}
				addnews($now,'ct_succ',$name,$citm['itm'],'陷阱','爆炸物');
			}
			else
			{
				$fail_dmg = strpos($citm['itmk'],'TNc')!==false ? round(32675*(rand(55,95)/100)) : round($citm['itme']*(rand(55,95)/100));
				$log.="正当你谨慎的拆开道具的外壳时，你听到了“滴”的一声……<br>在下一个瞬间，你就被爆炸带来的巨大冲击力掀翻在地。<br><span class='yellow'>在猛烈的爆炸中，你受到了<span class='red'>{$fail_dmg}</span>点伤害！</span><br>";
				if($hp>$fail_dmg)
				{
					$hp -= $fail_dmg;
				}
				else
				{
					$hp = 0;
					$state = 98;
					\player\update_sdata(); $sdata['sourceless'] = 1; $sdata['attackwith'] = '';
					\player\kill($sdata,$sdata);
					\player\player_save($sdata);
					\player\load_playerdata($sdata);
				}
				addnews($now,'ct_fail',$name,$citm['itm'],'陷阱','爆炸物',$fail_dmg);
			}
			\itemmain\itms_reduce($citm,1);
			if($itms0)
			{
				\itemmain\itemget();
			}
		}
		elseif($cway=='dtt')
		{
			if(rand(1,100)<=$change_succ_obbs)
			{
				$log.="<span class='yellow'>“呼……”</span><br>完成了手中精密的工作，你如释重负般长吁了一口气。<br>这样看来，爆炸物的改造工作就<span class='red'>顺利完成</span>了！<br>";
				$itm0 = '埋设式'.$citm['itm'];
				$itmk0 = 'TN';
				$itme0 = ($citm['itms']>5 || $citm['itms']=='∞') ? round($citm['itme']*5) : round($citm['itme']*$citm['itms']);
				$itms0 = 1;
				$itmsk0 = '';
				addnews($now,'ct_succ',$name,$citm['itm'],'爆炸物','陷阱');
			}
			else
			{
				$fail_dmg = round($citm['itme']*(rand(55,95)/100));
				$log.="正当你谨慎的拆开道具的外壳时，你听到了“滴”的一声……<br>在下一个瞬间，你就被爆炸带来的巨大冲击力掀翻在地。<br><span class='yellow'>在猛烈的爆炸中，你受到了<span class='red'>{$fail_dmg}</span>点伤害！</span><br>";
				if($hp>$fail_dmg)
				{
					$hp -= $fail_dmg;
					addnews($now,'ct_fail',$name,$citm['itm'],'爆炸物','陷阱',$fail_dmg);
				}
				else
				{
					$hp = 0;
					$state = 98;
					\player\update_sdata(); $sdata['sourceless'] = 1; $sdata['attackwith'] = '';
					\player\kill($sdata,$sdata);
					\player\player_save($sdata);
					\player\load_playerdata($sdata);
				}
			}
			\itemmain\itms_reduce($citm,1);
			if($itms0)
			{
				\itemmain\itemget();
			}		
		}
		else
		{
			return;
		}
	}
	function parse_news($news, $hour, $min, $sec, $a, $b, $c, $d, $e)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if($news == 'death98') 
			return "<li>{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">$a</span>在改造易爆物品时失误被炸死，实在是喜大普奔！{$e0}";
		if($news == 'ct_succ') 
			return "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}成功将<span class='yellow'>{$c}【{$b}】</span>改造成了<span class='yellow'>{$d}</span>……细作吃矛！</span><br>\n";
		if($news == 'ct_fail') 
			return "<li>{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}试图将<span class='yellow'>{$c}【{$b}】</span>改造成<span class='yellow'>{$d}</span>……但是失败了，还因此受到了<span class='red'>【{$e}】</span>点伤害！</span><br>\n";
		return $chprocess($news, $hour, $min, $sec, $a, $b, $c, $d, $e);
	}
	/*==========Fargo前基地特殊功能：remakegun功能部分结束==========*/
}

?>
