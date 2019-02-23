<?php

namespace campfire_item_kget
{
	function init() 
	{
		eval(import_module('itemmain'));
		$iteminfo['kget'] = '精密仪器';
	}
	
	function parse_itmuse_desc($n, $k, $e, $s, $sk){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$ret = $chprocess($n, $k, $e, $s, $sk);
		if(strpos($k,'kget')===0){
			if ($n == '动力装甲修复工具') {
				$ret .= '可以恢复动力装甲类防具的耐久值，需要消耗金属材料填充';
			}elseif ($n == '便携式控制中心子端') {
				$ret .= '幻境控制系统的移动子端，可解除禁区或使禁区提前到来，且内置最高级的电子雷达';
			}elseif ($n == '再启动指令集') {
				$ret .= '使用后能达到使游戏重新开始的效果，但需要满足一定的条件';
			}elseif ($n == '迷你纺织者「睡美人」') {
				$ret .= '可以增加身体防具的效果值，需要消耗针线包填充';
			}
		}
		return $ret;
	}
	
	
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','campfire_item_kget','input'));
		if($uec_cmd == 'choose_repair_aimner')
		{
			if($command=='menu')
			{
				$log.="你踌躇再三，还是将修复工具放回了背包内，等到以后有需要的时候再用吧。<br>";
			}
			elseif(in_array($command,Array('arb','arh','ara','arf')))
			{
				repair_powered_armor($command);	
			}
			else
			{
				$log.="动力装甲修复工具指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		$chprocess();
	}
	function repair_powered_armor($kind)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','input','logger'));
		if(!${$kind.'s'} || strpos(${$kind.'k'},'P')===false)
		{
			$log.="你所选择的装备不是动力装甲，请重新选择！<br>";	
			return;
		}		
		for($i=0;$i<=6;$i++)
		{
			if(${'itme'.$i} && ${'itms'.$i} && strpos(${'itm'.$i},'动力装甲修复工具')!==false)
			{
				$ritm['itm']=&${'itm'.$i}; $ritm['itmk']=&${'itmk'.$i};
				$ritm['itme']=&${'itme'.$i}; $ritm['itms']=&${'itms'.$i}; $ritm['itmsk']=&${'itmsk'.$i};
				break;
			}
		}
		if(!$ritm)
		{
			$log.="你身上没有动力装甲修复工具！<br>";	
			return;
		}
		$log.="当你提交了操作后，看起来只有巴掌大的方盒忽然从中间展开，<br>许多细微的金属丝从中弹射而出，将你的动力装甲包裹其中。<br>漫长的修复作业开始了……<br>";
		$log.="……<br>";
		${$kind.'s'} = ${$kind.'s'}=='∞' ? $ritm['itme'] : ${$kind.'s'}+$ritm['itme'];
		$log.="不知过了多久，伴随着一声清脆的提示音，你看到那些金属丝以肉眼难辨的速度回到了方盒内。<br>当你低头查看时，你才发现，自己的动力装甲已经焕然一新了。<br>你的{${$kind}}增加了<span class='yellow'>{$ritm['itme']}</span>点耐久！<br>";
		$ritm['itme'] = 0;
	}
	function itemuse_kget(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','input','logger','areafeatures_etconsole'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		if($itm=='便携式控制中心子端')
		{
			ob_clean();
			include template(MOD_AREAFEATURES_ETCONSOLE_MOBGSC_CMD);
			$cmd = ob_get_contents();
			ob_clean();
			return;
		}
		if($itm=='动力装甲修复工具')
		{
			if($itme)
			{
				ob_clean();
				include template(MOD_CAMPFIRE_ITEM_KGET_CHOOSE_REPAIR_AIMMER);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			else
			{
				$metal_flag = 0;
				for($i=0;$i<=6;$i++)
				{
					if(${'itms'.$i} && strpos(${'itm'.$i},'金属材料')!==false)
					{
						$mitm['itm']=&${'itm'.$i}; $mitm['itmk']=&${'itmk'.$i};
						$mitm['itme']=&${'itme'.$i}; $mitm['itms']=&${'itms'.$i}; $mitm['itmsk']=&${'itmsk'.$i};
						$metal_flag += $mitm['itme']*$mitm['itms'];	
						$mitm['itms']=1;
						\itemmain\itms_reduce($mitm);
						break;
					}
				}
				if($metal_flag)
				{
					$metal_flag = round($metal_flag * rand(8,10) / 10);
					$itme += $metal_flag;
					$log.="你打开了修复工具的盒盖，将金属材料放入其中。<br>隐约能听到从小盒子内传出齿轮咬合的声音。<br>装填工作开始了。<br>……<br>修复工具的材料储量增加了<span class='yellow'>{$metal_flag}</span>点！<br>";
				}
				else
				{
					$log.="修复工具需要装填素材才能工作，去找点金属材料吧！<br>";
					return;
				}
			}
			return;
		}
		if($itm=='再启动指令集')
		{
			//条件：非房间模式（除荣耀、极速模式外），使用者为唯一幸存者
			if($gametype>=10 & $gametype!=18 && $gametype!=19)
			{
				$log.="当前模式下不可使用！<br>";
				return;
			}
			if($alivenum>1)
			{
				$log.="场上还存在其他幸存者，你无法提交该指令！<br>";
				return;
			}
			$restart_flag = false;
			//条件A：可入场状态，游戏开始10分钟后可用，使用者不能有职业（用来区分新入场玩家）
			if($gamestate<30 && $gamestate>=20 && $now>=$starttime+600 && !$club)
			{
				$restart_flag=true;
			}
			//条件B：不可入场状态，游戏开始30分钟后可用
			elseif($gamestate>=30 && $gamestate<40 && $now>=$starttime+1800)
			{
				$restart_flag=true;
			}
			else
			{
				$log.="{$itm}的使用条件未满足。<br>";
			}
			if($restart_flag)
			{
				$hp = 0;
				$state = 202;
				\player\update_sdata(); $sdata['sourceless'] = 1; $sdata['attackwith'] = '';
				\player\kill($sdata,$sdata);
				\player\player_save($sdata);
				\player\load_playerdata($sdata);
				$gamestate = 40;
				addnews($time,'combo');
				save_gameinfo();
			}
			return;
		}
		if($itm=='迷你纺织者「睡美人」')
		{
			//判定是否填充针线
			if($itme==0)
			{
				$reloading_effects=0;
				for($i=0;$i<=6;$i++)
				{
					if(${'itms'.$i} && strpos(${'itm'.$i},'针线包')!==false)
					{
						$mitm['itm']=&${'itm'.$i}; $mitm['itmk']=&${'itmk'.$i};
						$mitm['itme']=&${'itme'.$i}; $mitm['itms']=&${'itms'.$i}; $mitm['itmsk']=&${'itmsk'.$i};
						$reloading_effects += $mitm['itme']*$mitm['itms'];	
						$mitm['itms'] = 1;
						\itemmain\itms_reduce($mitm);
						break;
					}				
				}
				if(!$reloading_effects)
				{
					$log .= '纺织者需要素材才能工作，去找点针线包来吧！<br>';
					return;
				}
				else
				{
					$reloading_effects = round($reloading_effects * rand(8,10) / 10);
					$itme += $reloading_effects;
					$log.="你打开了纺织者的盖子，将针线放入其中。<br>隐约能听到从纺织者体内传出齿轮咬合的声音。<br>装填工作开始了。<br>……<br>纺织者的素材储量增加了<span class='yellow'>{$reloading_effects}</span>点！<br>";
				}	
			}
			//开始快速打针线包
			else
			{
				if (($arb == $noarb) || ! $arb) 
				{
					$log .= '你没有装备防具，不能使用纺织者。<br>';
				} 
				else 
				{
					$arbe += $itme;
					$log .= "你启动了纺织者。<br>你眼睁睁地看着这位“睡美人”从中轴上裂开，像张开的大嘴一般将你的防具一口吃了进去。<br>在你还在回味那有些惊悚的一幕时，强化作业已经开始了。<br>……<br>在纺织者的强化下，<span class=\"yellow b\">$arb</span>的防御力变成了<span class=\"yellow b\">$arbe</span>。<br>";
					$itme = 0;
				}
			}
			return;
		}
		$log.="{$itm}该怎么用呢？<br>";
		return;
	}
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if ($itmk=='kget') {
			itemuse_kget($theitem);
			return;
		}
		$chprocess($theitem);
	}
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if($news == 'death202') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">$a</span>向系统中枢发送了重启指令，并登出了幻境系统！";
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e);
	}
}

?>
