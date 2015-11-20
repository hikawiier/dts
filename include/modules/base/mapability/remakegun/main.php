<?php

namespace remakegun
{
	function init()
	{
	}
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','remakegun','input'));
		/*==========Fargo前基地特殊功能：remakegun菜单部分开始==========*/
		if($mode == 'lp_remakegun')
		{
			if($command == 'menu')
			{
				$log.="你思考了一下，还是从基地的废墟中走了出来。<br>……以后需要用到它的时候再来吧。<br>";
			}
			elseif($command == 'remake_gun')
			{
				remake_gun('r_local',$rg_num);				
			}
			elseif($command == 'repair_gun')
			{
				if($wepk=='WG' || $wepk=='WJ' || $wepk=='WDG' || $wepk=='WGK')
				{
					$wep_skind = $wepsk ? str_split($wepsk) : Array();
				}
				$wg_sk = $wep_skind[$sk_num];
				repair_gun($wg_sk);				
			}
			else
			{
				$log.="remakegun指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		if($mode == 'remakegun_confirm')
		{
			if($command == 'menu')
			{
				$log.="你仔细思考了一下，还是决定不冒险对枪械进行改造。<br>等以后改变主意了再说吧。<br>";
			}
			elseif($command == 'rg_cfm')
			{
				remake_gun_result($r_way,$i_num);
			}
			else
			{
				$log.="remakegun确认指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		/*==========Fargo前基地特殊功能：remakegun菜单部分结束==========*/
		$chprocess();
	}
	/*==========Fargo前基地特殊功能：remakegun功能部分开始==========*/
	function repair_gun($r_sk)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','remakegun'));
		if($pls!=28)
		{
			$log.="<span class='red'>该地区不存在改造工作台，请重新输入指令。</span><br>";
			return;
		}
		if($wepk!=='WG' && $wepk!=='WJ' && $wepk!=='WDG' && $wepk!=='WGK')
		{
			$log.="<span class='red'>你所装备的武器不是远程武器或重型枪械，无法对其进行改造！</span><br>";
			return;
		}
		if(strpos($wepsk,$r_sk)===false)
		{
			$log.="<span class='red'>你的武器上没有该属性，请重新选择！</span><br>";
			return;
		}
		$rubbish_flag = false;
		for($i=1;$i<=6;$i++)
		{
			if((strpos(${'itm'.$i},'一堆废铁')!==false || strpos(${'itm'.$i},'某种机械设备')!==false || strpos(${'itm'.$i},'非法枪械部件')!==false) && (${'itms'.$i}>0))
			{
				$rubbish_flag = $i;
				break;
			}
		}
		if($rubbish_flag)
		{
			$rub['itm']=&${'itm'.$i}; $rub['itmk']=&${'itmk'.$i};
			$rub['itme']=&${'itme'.$i}; $rub['itms']=&${'itms'.$i}; $rub['itmsk']=&${'itmsk'.$i};
			$log.="你将武器放在了工作台上，开始小心翼翼的拆解它……<br>";
			$log.="…………<br>";
			//读取枪械上包含的属性，以及属性数量
			$wep_sk = $wepsk ? str_split($wepsk) : Array();
			$wep_sk_num = sizeof($wep_sk);
			$wep_sk_rarity = 0;
			//将要摘除的属性从枪械属性中去除，并计算其他属性的稀有度之合
			foreach($wep_sk as $wep_skey => $wep_skinfo)
			{
				if($wep_skinfo == $r_sk)
				{
					unset($wep_sk[$wep_skey]);
				}
				else
				{
					$wep_sk_rarity += in_array($wep_skinfo,array_keys($sk_rarity)) ? $sk_rarity[$wep_skinfo] : 5;			
				}
			}
			if($wep_sk_rarity<=25){$wep_sk_rarity*=0.9;}
			elseif($wep_sk_rarity>25 && $wep_sk_rarity<=40){$wep_sk_rarity*=0.8;}
			elseif($wep_sk_rarity>40 && $wep_sk_rarity<=55){$wep_sk_rarity*=0.75;}
			elseif($wep_sk_rarity>55 && $wep_sk_rarity<=70){$wep_sk_rarity*=0.7;}
			else{$wep_sk_rarity*=0.6;}
			$wepsk_rarity_obbs = $wep_sk_rarity;
			//基础成功率
			$base_repairsucc_obbs = 0;
			if($wg<=100){$base_repairsucc_obbs = $wg*0.25;}
			elseif($wg>100 && $wg<=200){$base_repairsucc_obbs = 25+(($wg-100)*0.2);}	
			elseif($wg>200 && $wg<=400){$base_repairsucc_obbs = 45+(($wg-200)*0.14);}
			else{$base_repairsucc_obbs = 73+(($wg-400)*0.1);}
			$base_repairsucc_obbs = min(99,$base_repairsucc_obbs);	
			//零件增益
			if($rub['itm']=='某种机械设备'){$rubbish_add_obbs=18;}
			elseif($rub['itm']=='非法枪械部件'){$rubbish_add_obbs=23;}
			else{$rubbish_add_obbs=0;}
			//要摘除属性的稀有度，一发和多重为特判的50
			$repairsk_reduce_obbs = ($r_sk=='o' || $r_sk=='j') ? 37 : $sk_rarity[$r_sk];
			//最终概率
			$final_repairsucc_obbs = round($base_repairsucc_obbs + $rubbish_add_obbs - $repairsk_reduce_obbs - $wepsk_rarity_obbs);
			$final_repairsucc_obbs = max(1,$final_repairsucc_obbs);
			//$log.="开始计算：<br>打算去除的属性：{$r_sk}<br>去除失败会扣除效果：{$down_effect}<br>基础成功率{$base_repairsucc_obbs}%<br>零件增效{$rubbish_add_obbs}%<br>要抹去属性的稀有度{$repairsk_reduce_obbs}%<br>除它之外的武器属性稀有度之合{$wepsk_rarity_obbs}%<br>最终成功率{$final_repairsucc_obbs}%<br>";
			if($final_repairsucc_obbs<=25){$final_obbs_word="<span class='red'>{$final_repairsucc_obbs}%</span><br>";}
			elseif($final_repairsucc_obbs>25 && $final_repairsucc_obbs<=50){$final_obbs_word="<span class='yellow'>{$final_repairsucc_obbs}%</span>";}
			elseif($final_repairsucc_obbs>50 && $final_repairsucc_obbs<=75){$final_obbs_word="<span class='clan'>{$final_repairsucc_obbs}%</span>";}
			else{$final_obbs_word="<span class='lime'>{$final_repairsucc_obbs}%</span>";}
			if(rand(1,100)<=$final_repairsucc_obbs)
			{
				$wepsk = str_replace($r_sk,'',$wepsk);	
				$log.="<span class='yellow'>“呼……”</span><br>完成了手中精密的工作，你如释重负般长吁了一口气。<br>这样看来，枪械的修复工作就<span class='red'>顺利完成</span>了！<br>而且经过了改造时的测量，你计算出了本次修复工作的理论成功率为{$final_obbs_word}。<br>";
				$log.="<br><span class='yellow'>你的武器<span class='lime'>【{$wep}】</span>在经过修复后发生了如下改变：</span><br>";
				$log.="<span class='yellow'>属性减少  -＞  <span class='red'>【{$itemspkinfo[$r_sk]}】</span></span><br><br>";
			}
			else
			{
				$down_effect = round(($wep_sk_rarity+$repairsk_reduce_obbs) * ($wepe/200));
				$wepe -= $down_effect;
				$log.="虽然你尽可能让自己小心的操作，但还是出现了操作上的失误。<br>这样看来，枪械的修复工作<span class='red'>彻底失败</span>了。<br>但是，经过了改造时的测量，你计算出了本次修复工作的理论成功率为{$final_obbs_word}。<br>希望下次能够成功吧……<br>";
				$log.="<br><span class='yellow'>你的武器<span class='lime'>【{$wep}】</span>在经过修复后发生了如下改变：</span><br>";
				$log.="<span class='yellow'>效果降低  -＞  <span class='red'>【{$down_effect}】</span></span><br><br>";
			}
			\itemmain\itms_reduce($rub);
		}
		else
		{
			$log.="<span class='red'>你身上没有可以用来修复枪械的零件，请保证身上至少拥有【一堆废铁】【某种机械设备】【非法枪械部件】中的任意一种道具！</span><br>";
			return;
		}
	}
	function remake_gun_result($r_way,$i)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','remakegun'));
		if($r_way!=='r_local' && $r_way!=='r_item')
		{
			$log.="<span class='red'>改造方式选择错误，请重新输入指令。</span><br>";
			return;
		}
		if($r_way=='r_local' && $pls!=28)
		{
			$log.="<span class='red'>该地区不存在改造工作台，请重新输入指令。</span><br>";
			return;
		}
		if(!is_numeric($i) || !$i || $i>6 || $i<0 || ${'itmk'.$i}!=='RG' || ${'itms'.$i}<=0)
		{
			$log.="<span class='red'>要使用的枪械配件选择错误，请重新输入指令。{$i}</span><br>";
			return;
		}
		if($wepk!=='WG' && $wepk!=='WJ' && $wepk!=='WDG' && $wepk!=='WGK')
		{
			$log.="<span class='red'>你所装备的武器不是远程武器或重型枪械，无法对其进行改造！</span><br>";
			return;
		}	
		$log.="你小心翼翼的将枪械拆开，开始对内部的复杂结构进行改造……<br>";
		$log.="…………<br>";
		$rgi['itm']=&${'itm'.$i}; $rgi['itmk']=&${'itmk'.$i};
		$rgi['itme']=&${'itme'.$i}; $rgi['itms']=&${'itms'.$i}; $rgi['itmsk']=&${'itmsk'.$i};
		$rg_sk = $rgi['itmsk'] ? str_split($rgi['itmsk']) : Array();
		$wep_sk = $wepsk ? str_split($wepsk) : Array();
		$rg_sk_num = sizeof($rg_sk);
		$wep_sk_num = sizeof($wep_sk);		
		if($rg_sk_num && $wep_sk_num)
		{
			$merge_sk = array_intersect($rg_sk,$wep_sk);
			$merge_flag = false;
			if(sizeof($merge_sk)>0)
			{
				$merge_flag = true;
				$merge_effect = 0;				
			}
		}
		if($rg_sk_num)
		{	
			$rg_sk_rarity = 0;
			foreach($rg_sk as $rg_skey => $rg_skinfo)
			{
				if((in_array($rg_skinfo,$merge_sk))&&($merge_flag))
				{
					$merge_effect += in_array($rg_skinfo,array_keys($sk_rarity)) ? $sk_rarity[$rg_skinfo] : 5;
					unset($rg_sk[$rg_skey]);
				}
				else
				{
					$rg_sk_rarity += in_array($rg_skinfo,array_keys($sk_rarity)) ? $sk_rarity[$rg_skinfo] : 5;
				}
			}			
		}
		if($wep_sk_num)
		{
			$wep_sk_rarity = 0;
			foreach($wep_sk as $wep_skey => $wep_skinfo)
			{
				$wep_sk_rarity += in_array($wep_skinfo,array_keys($sk_rarity)) ? $sk_rarity[$wep_skinfo] : 5;
			}
			if($wep_sk_rarity<=25){$wep_sk_rarity*=0.9;}
			elseif($wep_sk_rarity>25 && $wep_sk_rarity<=40){$wep_sk_rarity*=0.8;}
			elseif($wep_sk_rarity>40 && $wep_sk_rarity<=55){$wep_sk_rarity*=0.75;}
			elseif($wep_sk_rarity>55 && $wep_sk_rarity<=70){$wep_sk_rarity*=0.7;}
			else{$wep_sk_rarity*=0.6;}
		}
		$changesucc_obbs_max = $r_way=='r_local' ? 99 : 75;		
		$base_changesucc_obbs = 0;
		if($wg<=100){$base_changesucc_obbs = $wg*0.25;}
		elseif($wg>100 && $wg<=200){$base_changesucc_obbs = 25+(($wg-100)*0.2);}	
		elseif($wg>200 && $wg<=400){$base_changesucc_obbs = 45+(($wg-200)*0.14);}
		else{$base_changesucc_obbs = 73+(($wg-400)*0.1);}
		$base_changesucc_obbs = min($changesucc_obbs_max,$base_changesucc_obbs);		
		$r_effect_obbs = ($r_itme+$merge_effect)/25; 
		$r_skrarity_obbs = $rg_sk_rarity;
		$wep_skrarity_obbs = $wep_sk_rarity;	
		$final_changesucc_obbs = ceil($base_changesucc_obbs-$r_effect_obbs-$r_skrarity_obbs-$wep_skrarity_obbs);
		$final_changesucc_obbs = max(1,$final_changesucc_obbs);		

		if(rand(1,100)<=$final_changesucc_obbs)
		{
			$log.="<span class='yellow'>“呼……”</span><br>完成了手中精密的工作，你如释重负般长吁了一口气。<br>这样看来，枪械改造的工作<span class='red'>顺利完成</span>了！<br>你掂了掂手中的爱枪，感觉它变得更顺手了。<br>";
			$add_sk = implode("",$rg_sk);
			$add_effect = $rgi['itme'] + $merge_effect;
			$wepsk.= $add_sk;
			$wepe += $add_effect;
			$log.="<br><span class='yellow'>你的武器<span class='lime'>【{$wep}】</span>在经过<span class='lime'>【{$rgi['itm']}】</span>的改造后发生了如下改变：</span><br>";
			if($add_effect>0)
			{
				$log.="<span class='yellow'>效果提高  -＞  <span class='red'>{$add_effect}</span></span><br>";
			}			
			if(sizeof($rg_sk)>0)
			{
				$log.="<span class='yellow'>属性增加  -＞  </span>";
				foreach($rg_sk as $n_rg_sk => $i_rg_sk)
				{
					$log.="<span class='red'>【{$itemspkinfo[$i_rg_sk]}】 </span>";
				}
				$log.="<br><br>";
			}
		}
		else
		{
			$log.="虽然你尽可能让自己小心的操作，但还是由于一个小失误弄坏了精密的配件。<br>这样看来，枪械改造的工作<span class='red'>彻底失败</span>了。<br>虽然如此，你还是把枪械部件的残骸收了起来。<br>";
			$basic_down_effect = round($rg_sk_rarity) + $rgi['itme'];
			$down_effect = $wepe-$basic_down_effect<=0 ? $wepe-1 : $basic_down_effect;
			$wepe -= $down_effect;
			if((rand(1,100)<=round($rg_sk_rarity/10)) && strpos($wepsk,'o')===false)
			{
				$log.="而且由于你的粗心大意，对你的枪械造成了<span class='red'>不可挽回的损害</span>！<br>看来它的寿命被极大的缩短了……<br>";
				$down_sk = 'o';
				$wepsk.= $down_sk;
			}
			$log.="<br><span class='yellow'>你的武器<span class='lime'>【{$wep}】</span>在经过<span class='lime'>【{$rgi['itm']}】</span>的改造后发生了如下改变：</span><br>";
			if($down_effect>=0)
			{
				$log.="<span class='yellow'>效果降低  -＞  <span class='red'>{$down_effect}</span></span><br>";
			}	
			if($down_sk)
			{
				$log.="<span class='yellow'>属性增加  -＞  <span class='red'>【一发】</span></span><br><br>";
			}
			$itm0='一堆废铁';
			$itmk0='X';
			$itme0=1;
			$itms0=1;
			\itemmain\itemget();
		}
		\itemmain\itms_reduce($rgi);
	}
	function remake_gun($r_way,$i)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','remakegun','input'));
		
		if($r_way!=='r_local' && $r_way!=='r_item')
		{
			$log.="<span class='red'>改造方式选择错误，请重新输入指令。</span><br>";
			return;
		}
		if($r_way=='r_local' && $pls!=28)
		{
			$log.="<span class='red'>该地区不存在改造工作台，请重新输入指令。</span><br>";
			return;
		}
		if(!is_numeric($i) || !$i || $i>6 || $i<0 || ${'itmk'.$i}!=='RG' || ${'itms'.$i}<=0)
		{
			$log.="<span class='red'>要使用的枪械配件选择错误，请重新输入指令。{$i}</span><br>";
			return;
		}
		if($wepk!=='WG' && $wepk!=='WJ' && $wepk!=='WDG' && $wepk!=='WGK')
		{
			$log.="<span class='red'>你所装备的武器不是远程武器或重型枪械，无法对其进行改造！</span><br>";
			return;
		}		
		$r_itm=&${'itm'.$i}; $r_itmk=&${'itmk'.$i};
		$r_itme=&${'itme'.$i}; $r_itms=&${'itms'.$i}; $r_itmsk=&${'itmsk'.$i};
		//读取枪械部件和武器上的属性及属性数量
		$rg_sk = $r_itmsk ? str_split($r_itmsk) : Array();
		$wep_sk = $wepsk ? str_split($wepsk) : Array();
		$rg_sk_num = sizeof($rg_sk);
		$wep_sk_num = sizeof($wep_sk);		
		//计算可安装的属性，分别计算部件的非重复属性和武器自带属性的稀有度之合。重复的属性会按照其稀有度折算成效果
		if($rg_sk_num && $wep_sk_num)
		{
			//枪械部件和武器都有属性的情况下，才可能出现属性重复
			$merge_sk = array_intersect($rg_sk,$wep_sk);
			$merge_flag = false;
			if(sizeof($merge_sk)>0)
			{
				$merge_flag = true;
				$merge_effect = 0;				
			}
		}
		if($rg_sk_num)
		{	
			//枪械部件有属性的情况下，才需要计算稀有度和属性重复
			$rg_sk_rarity = 0;
			foreach($rg_sk as $rg_skey => $rg_skinfo)
			{
				if((in_array($rg_skinfo,$merge_sk))&&($merge_flag))
				{
					$merge_effect += in_array($rg_skinfo,array_keys($sk_rarity)) ? $sk_rarity[$rg_skinfo] : 5;
					unset($rg_sk[$rg_skey]);
				}
				else
				{
					$rg_sk_rarity += in_array($rg_skinfo,array_keys($sk_rarity)) ? $sk_rarity[$rg_skinfo] : 5;
				}
			}			
		}
		if($wep_sk_num)
		{
			//武器有属性的情况下，才需要计算武器属性稀有度
			$wep_sk_rarity = 0;
			foreach($wep_sk as $wep_skey => $wep_skinfo)
			{
				$wep_sk_rarity += in_array($wep_skinfo,array_keys($sk_rarity)) ? $sk_rarity[$wep_skinfo] : 5;
			}

			if($wep_sk_rarity<=25){$wep_sk_rarity*=0.9;}
			elseif($wep_sk_rarity>25 && $wep_sk_rarity<=40){$wep_sk_rarity*=0.8;}
			elseif($wep_sk_rarity>40 && $wep_sk_rarity<=55){$wep_sk_rarity*=0.75;}
			elseif($wep_sk_rarity>55 && $wep_sk_rarity<=70){$wep_sk_rarity*=0.7;}
			else{$wep_sk_rarity*=0.6;}

		}
		//计算安装的成功率
		$changesucc_obbs_max = $r_way=='r_local' ? 99 : 75;//成功率上限
		
		$base_changesucc_obbs = 0;
		if($wg<=100){$base_changesucc_obbs = $wg*0.25;}//受射系熟练影响的基础成功率
		elseif($wg>100 && $wg<=200){$base_changesucc_obbs = 25+(($wg-100)*0.2);}	
		elseif($wg>200 && $wg<=400){$base_changesucc_obbs = 45+(($wg-200)*0.14);}
		else{$base_changesucc_obbs = 73+(($wg-400)*0.1);}
		$base_changesucc_obbs = min($changesucc_obbs_max,$base_changesucc_obbs);		

		$r_effect_obbs = ($r_itme+$merge_effect)/25; //受部件效果影响的成功率
		$r_skrarity_obbs = $rg_sk_rarity;//受部件非重复属性稀有度影响的成功率
		$wep_skrarity_obbs = $wep_sk_rarity;//受武器属性稀有度影响的成功率		

		$final_changesucc_obbs = ceil($base_changesucc_obbs-$r_effect_obbs-$r_skrarity_obbs-$wep_skrarity_obbs);
		$final_changesucc_obbs = max(1,$final_changesucc_obbs);		

		$remake_info = Array('r_local' => '工具改造' ,'r_item' => '手动改造');
		$log.="你确定要使用<span class='yellow'>【{$r_itm}】</span>对武器<span class='yellow'>【{$wep}】</span>进行<span class='yellow'>{$remake_info[$r_way]}</span>吗？<br>这样做的成功率为";
		if($final_changesucc_obbs<=25){$log.="<span class='red'>{$final_changesucc_obbs}%</span>！<br>";}
		elseif($final_changesucc_obbs>25 && $final_changesucc_obbs<=50){$log.="<span class='yellow'>{$final_changesucc_obbs}%</span>！<br>";}
		elseif($final_changesucc_obbs>50 && $final_changesucc_obbs<=75){$log.="<span class='clan'>{$final_changesucc_obbs}%</span>！<br>";}
		else{$log.="<span class='lime'>{$final_changesucc_obbs}%</span>！<br>";}

		ob_clean();
		include template(MOD_REMAKEGUN_LP_REMAKEGUN_CONFIRM);
		$cmd = ob_get_contents();
		ob_clean();
		return;
	}
}

?>
