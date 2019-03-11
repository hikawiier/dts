<?php

namespace areafeatures_etconsole
{
	function init()
	{
	}
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','areafeatures_etconsole','input','weather','npc','itemmain'));
		/*==========无月之影特殊功能：areafeatures_etconsole菜单部分开始==========*/
		if($mode == 'lp_areafeatures_etconsole')
		{
			if($pls!=0)
			{
				$log.="该地图没有areafeatures_etconsole功能，如果遇到了BUG，请您将这句话转述给管理员。<br>";
				return;
			}
			$check_areafeatures_etconsole_flag = 0;
			$check_areafeatures_etconsole_flag = check_areafeatures_etconsole();
			if(!$check_areafeatures_etconsole_flag)
			{
				return;
			}
			if($command=='menu')
			{
				$log.="你思考了一下，随后关掉了控制界面并抽出了能量核心。<br>还是等到需要的时候再来操作控制台吧。<br>";
			}
			elseif($command=='changewth')
			{
				ob_clean();
				include template(MOD_AREAFEATURES_ETCONSOLE_LP_AREAFEATURES_ETCONSOLE_CWTH);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			elseif($command=='delcombo')
			{
				areafeatures_etconsole_bancombo();
			}
			elseif($command=='findthings')
			{
				ob_clean();
				include template(MOD_AREAFEATURES_ETCONSOLE_LP_AREAFEATURES_ETCONSOLE_FIND);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			elseif($command=='extractnpc')
			{
				areafeatures_etconsole_extractnpc();
			}
			elseif($command=='copyhack')
			{
				areafeatures_etconsole_copyhack();
			}
			else
			{
				$log.="areafeatures_etconsole菜单指令选择错误，如果遇到了BUG，请您将这句话转述给管理员。<br>";
			}
		}
		if($areafeatures_etconsole=='areafeatures_etconsole_changewth')
		{
			if($pls!=0)
			{
				$log.="该地图没有areafeatures_etconsolecwth功能，如果遇到了BUG，请您将这句话转述给管理员。<br>";
				return;
			}
			$check_areafeatures_etconsole_flag = 0;
			$check_areafeatures_etconsole_flag = check_areafeatures_etconsole();
			if($command=='menu')
			{
				$log.="你思考了一下，随后关掉了控制界面并抽出了能量核心。<br>还是等到需要的时候再来操作控制台吧。<br>";
			}
			elseif($command=='cwth')
			{
				areafeatures_etconsole_cwth($iweather);
			}
			else
			{
				$log.="areafeatures_etconsolecwth菜单指令选择错误，如果遇到了BUG，请您将这句话转述给管理员。<br>";
			}
		}
		elseif($areafeatures_etconsole== 'areafeatures_etconsole_findthings_mob')
		{
			$finding = substr($command,1);
			if($command=='cfindpc' || $command=='cfinditm'  || $command=='cfindtrap') 
			{
				areafeatures_etconsole_find($finding,$findingname,'',1);
			}	
			elseif($command=='cfindnpc')
			{
				areafeatures_etconsole_find($finding,$findingname,$findingnpctype,1);
			}					
			else
			{
				ob_clean();
				include template(MOD_AREAFEATURES_ETCONSOLE_MOBGSC_CMD);
				$cmd = ob_get_contents();
				ob_clean();
				return;	
			}				
		}	
		elseif($areafeatures_etconsole== 'areafeatures_etconsole_findthings')
		{
			
			if($pls!=0)
			{
				$log.="该地图没有areafeatures_etconsolecwth功能，如果遇到了BUG，请您将这句话转述给管理员。<br>";
				return;
			}
			$check_areafeatures_etconsole_flag = 0;
			$check_areafeatures_etconsole_flag = check_areafeatures_etconsole();
			if($command=='menu')
			{
				$log.="你思考了一下，随后关掉了控制界面并抽出了能量核心。<br>还是等到需要的时候再来操作控制台吧。<br>";
			}
			elseif($command=='cfindpc' || $command=='cfinditm'  || $command=='cfindtrap')
			{
				$finding = substr($command,1);
				areafeatures_etconsole_find($finding,$findingname,0);
			}
			elseif($command=='cfindnpc')
			{
				$finding = substr($command,1);
				areafeatures_etconsole_find($finding,$findingname,$findingnpctype);
			}
			else
			{
				$log.="areafeatures_etconsolefind菜单指令选择错误，如果遇到了BUG，请您将这句话转述给管理员。<br>";
			}
		}
		elseif($areafeatures_etconsole=='areafeatures_etconsole_mob')
		{
			foreach(Array(1,2,3,4,5,6) as $i)
			{
				if(${'itm'.$i}=='便携式控制中心子端' && ${'itms'.$i}>0  && ${'itmk'.$i}=='kget')
				{					
					$mob_flag = true;
					break;
				}
			}
			if($command=='menu')
			{
				$log.="你思考了一下，把子端放回了包裹内。<br>还是等到需要的时候再来操作它吧。<br>";
			}
			elseif($mob_flag && (($command=='gsc_addarea') || ($command =='gsc_hack') || ($command =='gsc_radar') || ($command =='gsc_searching')))
			{
				$gsc_order = substr($command,4);
				areafeatures_etconsole_mob($gsc_order,$radar_mmn);
			}
			else
			{
				$log.="你身上没有控制台的子机！<br>";
				return;
			}
		}
		/*==========无月之影特殊功能：areafeatures_etconsole菜单部分结束==========*/
		$chprocess();
	}
	/*==========无月之影特殊功能：areafeatures_etconsole功能部分开始==========*/
	function areafeatures_etconsole_extractnpc()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','itemmain','logger','addnpc','npc','areafeatures_etconsole'));
		
		if(!sizeof($extract_npc))
		{
			$log.="当你提交了操作后，一个大大的error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：系统中无可释放NPC。</span><br>";
			return;
		}
		elseif($extract_times > sizeof($extract_npc))
		{
			$log.="当你提交了操作后，一个大大的error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：数据释放参数设置错误，请联系幻境管理者！</span><br>";
			return;
		}
		addnews($now,'gsc_exnpc',$name);	
		shuffle($extract_npc); 
		$log.="当你提交了操作后，控制台的屏幕上显示出了黄色的反馈信息。<br><span class='yellow b'>“已释放NPC：<br></span>";
		for($i=0;$i<=$extract_times;$i++){
			$enpc_list=$extract_npc[$i];
			foreach(array_keys($enpc_list['sub']) as $enpc_sub){
				for($z=0;$z<=$enpc_list['sub'][$enpc_sub];$z++){
					\addnpc\addnpc ($enpc_list['type'],$enpc_sub,1);
				}
				$log.="【{$npc_typeinfo[$enpc_list['type']]} {$anpcinfo[$enpc_list['type']]['sub'][$enpc_sub]['name']}】 - <span class='red'>{$enpc_list['sub'][$enpc_sub]}</span>名<br>";
			}
		}
		$log.="<span class='yellow b'>请小心。它们的位置分别在……”</span><br>你还没来得及阅读完下文，控制台就因<span class='red'>能源不足</span>而自动休眠了……这坑爹的能量核心是假的吧……<br>";
		foreach(Array(1,2,3,4,5,6) as $i)
		{
			if(${'itm'.$i}=='能量核心' && ${'itms'.$i}>0)
			{					
				$core['itme']=&${'itme'.$i};$core['itms']=&${'itms'.$i};
				$core['itm']=&${'itm'.$i};$core['itmk']=&${'itmk'.$i};$core['itmsk']=&${'itmsk'.$i};
				\itemmain\itms_reduce($core);
				break;
			}
		}
	}
	function areafeatures_etconsole_copyhack()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','itemmain','logger'));	
		
		$have_it_flag = false;
		foreach(Array(1,2,3,4,5,6) as $i)
		{
			if(${'itm'.$i}=='便携式控制中心子端' && ${'itms'.$i}>0)
			{					
				$have_it_flag = true;
				break;
			}
		}
		if($have_it_flag)
		{
			$log.="当你提交了操作后，一个大大的error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：检测到你身上已携带有子端，不可重复获得。</span><br>";
			return;
		}
		
		$log.="当你提交了操作后，一个<span class='yellow'>方形的黑色金属盒</span>从控制台下方的凹陷处弹射出来，你将它接到了手中。<br>控制台的界面上似乎有关于它的介绍与操作说明，但你还没来得及看，控制台就因<span class='red'>能源不足</span>而自动休眠了……<br>";
		$itm0='便携式控制中心子端';
		$itmk0='kget';
		$itme0=1;
		$itms0=1;
		foreach(Array(1,2,3,4,5,6) as $i)
		{
			if(${'itm'.$i}=='能量核心' && ${'itms'.$i}>0)
			{					
				$core['itme']=&${'itme'.$i};$core['itms']=&${'itms'.$i};
				$core['itm']=&${'itm'.$i};$core['itmk']=&${'itmk'.$i};$core['itmsk']=&${'itmsk'.$i};
				\itemmain\itms_reduce($core);
				break;
			}
		}
		\itemmain\itemget();
	}
	function areafeatures_etconsole_bancombo()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','itemmain','logger','gameflow_combo'));	
		
		if($gamestate==10 || $gamestate==15)
		{
			$log.="该模式下无法使用限制解除功能。<br>";
			return;	
		}
		
		if(!$gamevars['bancombo'])
		{
			$log.="当你提交了操作后，控制台的屏幕上显示出了黄色的反馈信息。<span class='yellow'>“已开启连斗检测相关机制，请刷新页面进行确认，重复提交本功能，可以开……”</span><br>你还没来得及阅读完下文，控制台就因<span class='red'>能源不足</span>而自动休眠了……这坑爹的能量核心是假的吧……<br>但你仔细一看，原来角落里还闪烁着一行暗红色的字体：<br><span class='red'>“即使解除了连斗的判断……在2次禁区增加后，幻境也会关闭进入，这样只剩一人幸存的话游戏就结束了……<br>一定要记住……啊……！”<br></span>";
			$gamevars['bancombo'] = 1;
			if($gamestate == 40) 
			{
				$gamestate = 30;
				$ctobc = true;
			}
			\sys\save_gameinfo();
			addnews($now,'gsc_bancombo',$name);
			if($ctobc)
			{
				addnews($now,'gsc_ctobc',$name);
				\sys\systemputchat($now,'bancombo');
			}
		}				
		elseif($gamevars['bancombo'])
		{
			$log.="当你提交了操作后，控制台的屏幕上显示出了黄色的反馈信息。<span class='yellow'>“已关闭连斗检测相关机制，请刷新页面进行确认，重复提交本功能，可以解……”</span><br>你还没来得及阅读完下文，控制台就因<span class='red'>能源不足</span>而自动休眠了……这坑爹的能量核心是假的吧……<br>";
			$gamevars['bancombo'] = 0;
			\sys\save_gameinfo();
			\gameflow_combo\checkcombo();
			addnews($now,'gsc_recombo',$name);
		}
		else
		{
			$log.="限制解除功能出现错误，如果发现了BUG，请联系管理员。<br>";
			return;
		}
		foreach(Array(1,2,3,4,5,6) as $i)
		{
			if(${'itm'.$i}=='能量核心' && ${'itms'.$i}>0)
			{					
				$core['itme']=&${'itme'.$i};$core['itms']=&${'itms'.$i};
				$core['itm']=&${'itm'.$i};$core['itmk']=&${'itmk'.$i};$core['itmsk']=&${'itmsk'.$i};
				\itemmain\itms_reduce($core);
				break;
			}
		}
	}
	function systemputchat($time,$type,$msg = ''){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if($type == 'bancombo'){
			$msg = '警告：连斗状态已被临时管理者解除！';
		}
		$chprocess($time,$type,$msg);
	}
	function checkcombo($time){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if($gamevars['bancombo'] && $alivenum>0){
			return;
		}
		$chprocess($time);
	}
	function areafeatures_etconsole_mob($c_order,$c_radar)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','map','logger','npc'));	
		
		if($c_order!=='addarea' && $c_order!=='hack' && $c_order!=='radar' && $c_order!=='searching')
		{
			$log.="当你提交了操作后，一个大大的error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：无效的功能类别，请重新选择子端功能。</span><br>";
			return;
		}
		elseif($c_order=='hack' && $hack==1)
		{
			$log.="当你提交了操作后，一个大大的error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：当前禁区已经被解除，请勿重复操作！</span><br>";
			return;
		}
		elseif($c_order=='addarea' && in_array($pls,array_slice($arealist,0,$areanum+1+$areaadd)))
		{
			$log.="当你提交了操作后，一个大大的error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：你所处的位置为禁区或即将成为禁区，在这里使用该功能将会导致生命危险！</span><br>";
			return;
		}
		elseif($c_order=='addarea' && $now>=$areatime-30)
		{
			$log.="当你提交了操作后，一个大大的error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：系统禁区将在30秒内增加，或你已使用过本功能，在下次禁区到来前请不要重复使用！</span><br>";
			return;
		}
		elseif($c_order=='addarea' && $areanum>0 && $now<=($areatime-($areahour*60)+30))
		{
			$log.="当你提交了操作后，一个大大的error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：禁区增加后的30秒内不可重复使用本功能！</span><br>";
			return;
		}
		
		if($c_order=='hack')
		{
			$log.="当你提交了操作后，便携子端的界面开始闪烁，像是在发送信号，<br><span class='yellow'>当界面上的图像稳定下来时，你发现禁区已经解除了。</span><br>";
			$hack = 1;
			save_gameinfo();
			addnews($now,'gsc_hack',$name);
		}
		elseif($c_order=='addarea')
		{
			$log.="当你提交了操作后，便携子端的界面开始显示倒计时，<br><span class='yellow'>禁区还有30秒就要到来了，赶紧找个安全的地方躲一躲吧。<br>";
			$areatime = $now + 30;
			save_gameinfo();
			addnews($now,'gsc_addarea',$name,$sec);
			\sys\systemputchat($now,'gsc_addarea','禁区的到来被提前至30秒后！');
		}
		elseif($c_order=='radar')
		{
			$log.="当你提交了操作后，便携子端的界面开始闪烁，像是在发送信号，<br><span class='yellow'>当界面上的图像稳定下来时，你发现上面显示出了一排数据。</span><br>";
			$mms = $c_radar ? $c_radar : 99;
			$mode = 'radar';
			\radar\use_radar($mms);		
		}
		elseif($c_order=='searching')
		{
			$mob_searching_flag = 0;
			foreach(Array(1,2,3,4,5,6) as $i)
			{
				if(${'itm'.$i}=='能量核心' && ${'itms'.$i}>0)
				{					
					$core['itme']=&${'itme'.$i};$core['itms']=&${'itms'.$i};
					$core['itm']=&${'itm'.$i};$core['itmk']=&${'itmk'.$i};$core['itmsk']=&${'itmsk'.$i};
					$mob_searching_flag = $i;
					break;
				}
			}
			if($mob_searching_flag)
			{
				ob_clean();
				include template(MOD_AREAFEATURES_ETCONSOLE_LP_AREAFEATURES_ETCONSOLE_FIND);
				$cmd = ob_get_contents();
				ob_clean();
			}
			else			
			{
				$log.="当你提交了操作后，便携子端的界面开始闪烁，像是在发送信号，<br>当界面上的图像稳定下来时，一个大大error出现在了画面上，画面下方的错误原因中写着：<br><span class='yellow'>没有用于远程链接的能量核心！</span><br>";
			}	
			return;
		}		
	}
	function areafeatures_etconsole_find($findtype,$findnm,$npctype,$usingmob=0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','map','logger','itemmain','npc'));	
		if(!$findnm/* || preg_match('/[,|<|>|&|;|#|"|\s|\p{C}]+/u',$findnm)*/)
		{
			$log.= "当你提交了操作后，一个大大error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：提交的名称为空或是包含了非法字符，请重新输入。</span><br>";
			return;
		}
		if($findtype!=='findpc' && $findtype!=='findnpc' && $findtype!=='finditm' && $findtype!=='findtrap')
		{
			$log.= "当你提交了操作后，一个大大error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：未选择查询的信息类别或是该类别不存在，请重新选择。</span><br>";
			return;
		}
		if($findtype=='findnpc' && $npctype<1)
		{
			$log.= "当你提交了操作后，一个大大error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：查找NPC信息时，必须正确选择NPC所属的类别。</span><br>";
			return;
		}
		$ending_words = '你只来得及将这些信息记下，控制台就因<span class="red">能源不足</span>而自动休眠了……这坑爹的能量核心是假的吧……<br>';
		$ending_words_b = '然而这时，控制台忽然因<span class="red">能源不足</span>而自动休眠了……真是祸不单行啊……<br>';
		if($findtype=='findpc')
		{
			$findpc_data = \player\fetch_playerdata($findnm);
			if($findpc_data && $findpc_data['state']<5)
			{
				$log.="当你提交了操作后，控制台的屏幕上立即显示出了一组数据：<br>·<span class='yellow'>查询对象：</span>{$findnm}<br>·<span class='yellow'>所处位置：</span>{$plsinfo[$findpc_data['pls']]}<br>·<span class='yellow'>持有武器：</span>{$findpc_data['wep']}<br>·<span class='yellow'>持有金钱：</span>{$findpc_data['money']}元<br>";
				if(!$usingmob) $log.=$ending_words;
			}
			else
			{
				$log.="当你提交了操作后，控制台的屏幕上显示出了一个令人失望的结果。<br><span class='yellow'>显而易见的，你所查询的对象，玩家【{$findnm}】并不存在于系统中，或是他已经死了。</span><br>";
				if(!$usingmob) $log.=$ending_words_b;
			}
		}
		elseif($findtype=='findnpc')
		{
			if($findnm=='all')
			{
				$result = $db->query("SELECT pls FROM {$tablepre}players WHERE type = $npctype AND hp>0");
				$findnm_info = '所有的'.$npc_typeinfo[$npctype];
			}
			else
			{
				$result = $db->query("SELECT pls FROM {$tablepre}players WHERE name = '$findnm' AND type = $npctype AND hp>0");
				$findnm_info = $npc_typeinfo[$npctype].' '.$findnm;
			}			
			while($fn_data = $db->fetch_array($result)) 
			{
				$fn_data_array[] = $fn_data['pls'];
			}
			$fn_num = sizeof($fn_data_array);
			if($fn_num==1)
			{
				$log.="当你提交了操作后，屏幕上立即显示出了一组数据：<br>·<span class='yellow'>查询对象：</span>{$findnm_info}<br>·<span class='yellow'>{$plsinfo[$fn_data_array[0]]}</span> －＞ 存在<span class='clan'>1</span>名符合条件的对象<br>";	
				if(!$usingmob) $log.=$ending_words;	
			}
			elseif($fn_num>1)
			{
				$log.="当你提交了操作后，屏幕上立即显示出了一组数据：<br>·<span class='yellow'>查询对象：</span>{$findnm_info}<br>";
				$fn_array = array_count_values($fn_data_array);
				foreach(array_keys($fn_array) as $fn_pls)
				{
					$log.="·<span class='yellow'>{$plsinfo[$fn_pls]}</span> －＞ 存在<span class='clan'>{$fn_array[$fn_pls]}</span>名符合条件的对象<br>";
				}
				if(!$usingmob) $log.=$ending_words;
			}
			else
			{
				$log.="当你提交了操作后，控制台的屏幕上显示出了一个令人失望的结果。<br><span class='yellow'>显而易见的，你的查询目标，【{$findnm_info}】并不存在于系统中，或是他已经死了。</span><br>";
				if(!$usingmob) $log.=$ending_words_b;
			}
		}
		elseif($findtype=='finditm')
		{
			$result = $db->query("SELECT pls FROM {$tablepre}mapitem WHERE itm = '$findnm'");
			while($fi_data = $db->fetch_array($result)) 
			{
				$fi_data_array[] = $fi_data['pls'];
			}
			$fi_num = sizeof($fi_data_array);
			if($fi_num==1)
			{
				$log.="当你提交了操作后，屏幕上立即显示出了一组数据：<br>·<span class='yellow'>符合条件的道具：</span>{$findnm}<br>·<span class='yellow'>{$plsinfo[$fi_data_array[0]]}</span> －＞ 存在数量：<span class='clan'>1</span><br>";
				if(!$usingmob) $log.=$ending_words;
			}
			elseif($fi_num>1)
			{
				$log.="·<span class='yellow'>符合条件的道具：</span>{$findnm}<br>";
				$fi_array = array_count_values($fi_data_array);
				foreach(array_keys($fi_array) as $fi_pls)
				{
					$log.="·<span class='yellow'>{$plsinfo[$fi_pls]}</span> －＞ 存在数量：<span class='clan'>{$fi_array[$fi_pls]}</span><br>";
				}
				if(!$usingmob) $log.=$ending_words;
			}
			else
			{
				$log.="当你提交了操作后，控制台的屏幕上显示出了一个令人失望的结果。<br><span class='yellow'>显而易见的，你所查询的对象，道具【{$findnm}】并不存在于地图上。</span><br>";
				if(!$usingmob) $log.=$ending_words_b;
			}
		}
		elseif($findtype=='findtrap')
		{
			$result = $db->query("SELECT pls FROM {$tablepre}maptrap WHERE itm = '$findnm'");
			while($ftdata = $db->fetch_array($result)) 
			{
				$ftdata_array[] = $ftdata['pls'];
			}
			$ftnum = sizeof($ftdata_array);
			if($ftnum==1)
			{
				$log.="当你提交了操作后，控制台的屏幕上立即显示出了一组数据：<br>·<span class='yellow'>符合条件的已埋设陷阱：</span>{$findnm}<br>·<span class='yellow'>{$plsinfo[$ft_data_array[0]]}</span> －＞ 已埋设的{$findnm}数量：<span class='clan'>1</span><br>";
				if(!$usingmob) $log.=$ending_words;	
			}
			elseif($ftnum>1)
			{
				$log.="当你提交了操作后，控制台的屏幕上立即显示出了一组数据：<br>·<span class='yellow'>符合条件的已埋设陷阱：</span>{$findnm}<br>";
				$ftarray = array_count_values($ftdata_array);
				foreach(array_keys($ftarray) as $ftpls)
				{
					$log.="·<span class='yellow'>{$plsinfo[$ftpls]}</span> －＞ 已埋设的{$findnm}数量：<span class='clan'>{$ftarray[$ftpls]}</span><br>";
				}
				if(!$usingmob) $log.=$ending_words;
			}
			else
			{
				$log.="当你提交了操作后，控制台的屏幕上显示出了一个令人失望的结果。<br><span class='yellow'>显而易见的，你所查询的对象，地图上不存在已经被埋设的陷阱【{$findnm}】。</span><br>";
				if(!$usingmob) $log.=$ending_words_b;
			}
		}
		if($usingmob)
		{
			$log.="<br>一番操作后，你将子机收回了背包中。<br>";
		}	
		foreach(Array(1,2,3,4,5,6) as $i)
		{
			if(${'itm'.$i}=='能量核心' && ${'itms'.$i}>0)
			{					
				$core['itme']=&${'itme'.$i};$core['itms']=&${'itms'.$i};
				$core['itm']=&${'itm'.$i};$core['itmk']=&${'itmk'.$i};$core['itmsk']=&${'itmsk'.$i};
				\itemmain\itms_reduce($core);
				break;
			}
		}
	}
	function areafeatures_etconsole_cwth($wth)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','weather','logger','itemmain','player'));	
		if($wth == $weather)
		{
			$log.= "当你提交了操作后，一个大大error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：当前天气已为{$wthinfo[$wth]}，不需要再次修改。</span><br>";
		}
		elseif(!isset($wthinfo[$wth]))
		{
			$log.= "当你提交了操作后，一个大大error出现在了画面上，虽然你是一个不讲鹰语的战狼，但是“错误”这个单词你还是认识的。<br><span class='yellow'>画面下方的错误原因中写着：提交的天气数据错误，请重新确认。</span><br>";
		}
		else
		{
			$log.= "当你提交了操作后，控制面板上的数据开始飞速刷新，你不由得分了下神。当你再次抬起头时，窗外的天气已经变成了<span class='yellow'>{$wthinfo[$wth]}</span>！<br>但等你再看向控制台时，它已经因为<span class='red'>能源不足</span>自动休眠了……这能量核心是山寨的吧！？<br>";
			$weather = $wth;
			save_gameinfo();
			addnews($now,'gsc_cwth',$name,$wth);		
			foreach(Array(1,2,3,4,5,6) as $i)
			{
				if(${'itm'.$i}=='能量核心' && ${'itms'.$i}>0)
				{					
					$core['itme']=&${'itme'.$i};$core['itms']=&${'itms'.$i};
					$core['itm']=&${'itm'.$i};$core['itmk']=&${'itmk'.$i};$core['itmsk']=&${'itmsk'.$i};
					\itemmain\itms_reduce($core);
					break;
				}
			}
		}
	}
	function check_areafeatures_etconsole()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','areafeatures_etconsole'));		
		$crm_lose_flag = false;
		$have_corebar_flag = false;
		$crmdata = cache_fetch_npcdata('',1);
		if($crmdata['hp']<=0)
		{
			$crm_lose_flag = true;
		}			
		foreach(Array(1,2,3,4,5,6) as $i)
		{
			if(${'itm'.$i}=='能量核心' && ${'itms'.$i}>0)
			{
				$have_corebar_flag = true;
				break;
			}
		}
		if(!$crm_lose_flag)
		{
			$log.="你偷偷摸摸的爬进了控制室，想使用虚拟实境的操作系统……<br>但是不知从哪个角落里忽然飞来了一个煤气罐，直接将你砸成了<span class='red'>濒死状态</span>！<br><span class='yellow'>看来想要使用控制台，还得先击败某个人才行。</span><br>";
			$hp = 1;	
			return;
		}
		elseif(!$have_corebar_flag)
		{
			$log.="控制台的面板上，写着“能量储备”的应急指示灯正疯狂闪烁着。<br><span class='yellow'>看来如果没有足够的能量支持，这个控制台就只能当成大一号的板砖来用了。</span><br>不过该说是狡猾还是抠门呢，那家伙临走之前还不忘把控制台的能量核心拔掉……<br>";
			return;
		}		
		return 1;
	}
	function cache_fetch_npcdata($nnm,$ntype=0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if($nnm) $result = $db->query("SELECT * FROM {$tablepre}players WHERE name = '$nnm' AND type = '$ntype'");
		else $result = $db->query("SELECT * FROM {$tablepre}players WHERE type = '$ntype'");
		if(!$db->num_rows($result)) return NULL;
		$npcdata = $db->fetch_array($result);
		return $npcdata;
	}
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if($news == 'gsc_cwth') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime b\">{$a}使用了位于无月之影的控制台，将天气变成了{$wthinfo[$b]}！</span><br>\n";
		if($news == 'gsc_hack') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime b\">{$a}使用了无月之影控制台的便携子端，将禁区全部解除了！</span><br>\n";
		if($news == 'gsc_addarea') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime b\">{$a}使用了无月之影控制台的便携子端，使禁区的到来提前至{$b}秒后！</span><br>\n";
		if($news == 'gsc_recombo') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime b\">{$a}使用了位于无月之影的控制台，恢复了幻境的连斗检测机制！</span><br>\n";
		if($news == 'gsc_bancombo') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime b\">{$a}使用了位于无月之影的控制台，关闭了幻境的连斗检测机制！</span><br>\n";
		if($news == 'gsc_ctobc') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"red b\">游戏已解除连斗状态！</span><br>\n";
		if($news == 'gsc_exnpc') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime b\">{$a}使用了位于无月之影的控制台，使大量未经测试的危险NPC被释放了！</span><br>\n";
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e);
	}
	/*==========无月之影特殊功能：areafeatures_etconsole功能部分结束==========*/
}

?>
