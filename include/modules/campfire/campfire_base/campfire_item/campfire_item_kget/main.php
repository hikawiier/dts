<?php

namespace campfire_item_kget
{
	function init() 
	{
		eval(import_module('itemmain'));
		$iteminfo['kget'] = '电子装置';
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
		$ritm['itme'] = round($ritm['itme'] * rand(8,10) / 10);
		${$kind.'s'} = ${$kind.'s'}=='∞' ? $ritm['itme'] : ${$kind.'s'}+$ritm['itme'];
		$log.="不知过了多久，伴随着一声清脆的提示音，你看到那些金属丝以肉眼难辨的速度回到了方盒内。<br>当你低头查看时，你才发现，自己的动力装甲已经焕然一新了。<br>你的{${$kind}}增加了<span class='yellow'>{$ritm['itme']}</span>点耐久！<br>";
		$ritm['itme'] = 0;
	}
	function itemuse_kget(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','input','logger','campfire_areafeatures_etconsole'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		if($itm=='便携式控制中心子端')
		{
			ob_clean();
			include template(MOD_campfire_areafeatures_etconsole_MOBGSC_CMD);
			$cmd = ob_get_contents();
			ob_clean();
			return;
		}
		if($itm=='动力装甲修复工具')
		{
			if($itme)
			{
				ob_clean();
				include template(MOD_campfire_item_kget_CHOOSE_REPAIR_AIMMER);
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
						\itemmain\itms_reduce($mitm,1);
						break;
					}
				}
				if($metal_flag)
				{
					$metal_flag = round($metal_flag * rand(8,10) / 10);
					$itme += $metal_flag;
					$log.="你打开了修复工具的装填仓，将金属材料放入其中，你隐约能听到小盒子内传出机械齿轮转动的声音。<br>修复工具的材料储量增加了<span class='yellow'>{$metal_flag}</span>点！<br>";
				}
				else
				{
					$log.="修复工具的装填仓为空，而且你身上没有金属片！<br>";
					return;
				}
			}
		}
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
}

?>
