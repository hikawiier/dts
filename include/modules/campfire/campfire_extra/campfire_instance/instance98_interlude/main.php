<?php

namespace instance98
{
	function init() {
		eval(import_module('map','gameflow_combo','skillbase','trap'));
		$valid_skills[98] = array(1901,1002);
		$deathlimit_by_gtype[98] = 666;
	}
	
	//在陷阱之海，你会很痛苦
	function calculate_real_trap_obbs()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		if($pls == 92 || $pls == 93 || $pls == 94)	return $chprocess()+100;			
		else return $chprocess();
	}
	function get_trap_escape_rate()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		if($pls == 92 || $pls == 93 || $pls == 94)	return $chprocess()*0.1;	
		else return $chprocess();
	}
	
	function calculate_trapdef_proc_rate()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		if ($pls == 92 || $pls == 93 || $pls == 94)	return $chprocess()*0.2;		
		else return $chprocess();
	}
	
	function check_keep_corpse_in_searchmemory()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if(98 == $gametype) return true;
		$chprocess();
	}
	
	function addnpc_event($ntype, $nsub=0, $num=1){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		//请虚拟主播义义乌退群罢
		if(98 == $gametype && $ntype == 42)
		{
			//$result = $db->query("SELECT cid FROM {$tablepre}chat WHERE send='一一五'");
			//if(!$result) \sys\addchat(6, '来英灵殿吧，本小姐今天就要……哎哎哎……怎么突然黑屏了？！', '一一五');
			return;
		}	
		$chprocess($ntype, $nsub, $num);
	}
	
	function bear_keysword_event()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','map'));
		//最大能为多少人提供帮助
		$max_help_limits = 4;
		$gamevars['bear_keysword'] =(int)$gamevars['bear_keysword'];
		if($gamevars['bear_keysword'] && $gamevars['bear_keysword']<=$max_help_limits && !\skillbase\skill_query(1998,$sdata))
		{
			$log.="你获得了【守护精灵的援护】。<br>";
			\skillbase\skill_acquire(1998);
			$gamevars['bear_keysword'] += 1;
			\sys\save_gameinfo();
		}
	}
	
	function check_can_destroy($edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if($gametype==98 && $edata['type']==1006) return false;
		return $chprocess($edata);
	}
	
	function getcorpse_action(&$edata, $item)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','player','logger','corpse'));
		//捡起黑精灵的武器时 改变道具类型
		if($item == 'wep' && $edata['wep'] == '邻接之路『前向星』')
		{
			$edata['wep'] = '无名的光矢';
			$edata['wepk'] = 'GA';
			$edata['wepe'] = 1;
			$edata['weps'] = 1;
			$edata['wepsk'] = 'O';
			$log.="当你触碰到那把长弓的瞬间，一声微不可察的叹息在你的耳边响起。<br>而当你回过神来，却发现自己碰到的只是一根散发着些微荧光，好像马上便要溃散的光矢。<br>";
		}
		$chprocess($edata, $item);
	}
	
	function story_branch_event($story,$branch)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain'));
		if($story == 'bear_branches')
		{
			if($branch=='branch_a')
			{
				$state = 6;
				$url = 'end.php';
				\sys\gameover ( $now, 'end7', $name );
			}
			elseif($branch=='branch_b')
			{
				$gamevars['bear_keysword'] = 1;
				\sys\save_gameinfo();
				addnews($now, 'bearkey',$name);
				for($i=0;$i<=6;$i++)
				{
					if(${'itms'.$i} && strpos(${'itm'.$i},'黑熊键刃')!==false)
					{
						$itm['itm']=&${'itm'.$i}; $itm['itmk']=&${'itmk'.$i};
						$itm['itme']=&${'itme'.$i}; $itm['itms']=&${'itms'.$i}; $itm['itmsk']=&${'itmsk'.$i};
						$itm['itms']=1;
						\itemmain\itms_reduce($itm);
						break;
					}
				}
			}
			else
			{
				$log.="输入了错误的选择支：{$branch}。<br>";
			}	
			return;
		}
		else
		{
			$log.="错误的剧情判断：{$story}。<br>";
			return;
		}	
	}	
	
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','player','logger','itemmain'));
		if($gametype==98)
		{
			$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
			$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
			if(strpos($itm,'游戏解除钥匙')!==false)
			{
				$log.="你尝试与手中的解除钥匙建立起联系……<br>像是说明书一样的画面随之出现在了你的战术界面之上。<br>值得一提的是，你看到一项名为【精神锁定解除】的指令似乎被人粗暴的涂抹过，从而让战术界面无法识别其特征码。……不过这和你要找的东西没关系就是了。<br>你收敛起心思，继续浏览其他部分的信息，很快便找到了自己需要的指令。<br>";
				$log.="<span class='yellow b'>“我是挑战者 {$name}，申请开放J-10区域！”</span><br>";
				$log.="响应着你的呼唤，世界没有异变，大地也没有颤抖，那钥匙仅是闪烁了一下，旋即便化作光点四散而去了。<br>你估摸着你的指令大概是生效了，但因为没有大场面发生，让你有点心里没底。<br>";
				\itemmain\itms_reduce($theitem);
				$log.="看来只能亲自去英灵殿看看了……？<br>";
				if(!$gamevars['valhalla'])
				{
					$gamevars['valhalla'] = 1;
					\sys\save_gameinfo();
					addnews($now, 'valopen98',$name);
				}		
				return;
			}
			elseif($itm == '破灭之诗')
			{
				$rp = 0;
				$log .= '在你唱出那单一的旋律的霎那，<br>周围的空间发生了剧烈的颤抖……<br>但不知为何，你总觉得少了点什么。<br>';
				eval(import_module('weather'));
				$log .= '世界响应着这旋律，产生了异变……<br>';
				\weather\wthchange($itm,$itmsk);
				$hack = 1;
				$log .= '因为破灭之歌的作用，全部锁定被打破了！<br>';
				addnews($now,'hackb',$name);
				\sys\systemputchat($now,'hack');
				save_gameinfo();
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
				return;
			}
			elseif(strpos($itm,'褪色的笔记本')!==false)
			{
				if(\skillbase\skill_getvalue(1003,'used_bearbook'))
				{
					$log.="你感觉自己暂时没法从这里学到什么新的<del>嘴臭</del>知识了。<br>也许可以将它分享给你的伙伴们看看。<br>";
					return;
				}	
				$nowitme = $itme;
				$add_skillpoint = $itme>1 ? rand(1,$itme) : 1;	
				$itme -= $add_skillpoint;
				$skillpoint+=$add_skillpoint;
				$log.="你打开了笔记本，粗略的翻阅着。<br>这似乎是一本语录，上面记载了不少流行于远古时代的短句。<br>";
				$log.="你越看越是觉得这些短句朗朗上口，富有哲理。饶是满腹经纶的你也感觉受益匪浅！<br>你获得了<span class='yellow b'>{$add_skillpoint}</span>点技能点！<br>";
				if($add_skillpoint==$nowitme)
				{
					$itme0=1;$itms0=1;
					$log.="<span class='yellow b'>就在你认真翻阅时，你在笔记本的缝隙间发现了一张像是卡片的东西！</span><br>";
					if($add_skillpoint>50)
					{
						$itm0='画有熊样生物的的迷之卡牌';$itmk0='VO';$itmsk0='13';
					}
					else
					{
						$log.="<span class='yellow b'>但这张卡似乎已经破烂到看不出卡面上画的什么了……</span><br>";
						$itm0='本来画有熊样生物的迷之卡牌';$itmk0='VO3';$itmsk0='';
					}
					\itemmain\itemget();
				}
				$log.="语录的内容包含了巨大的信息量，你只看了一会儿就感觉晕头转向，你赶忙将它合上了。<br>";
				if($itme)
				{
					$log.="不过，看起来里面还有不少值得学习的内容，你决定把它保存下来。<br>也许可以将它分享给你的伙伴们看看。<br>";
				}
				else
				{
					$log.="不过，你觉得自己已经花10分钟完全搞懂里面的内容了。<br>你随手就把它丢掉了。<br>";
					\itemmain\itms_reduce($theitem);				
				}
				\skillbase\skill_setvalue(1003,'used_bearbook',1);
				return;
			}
			elseif(strpos($itm,'按钮的基座')!==false)
				$log.="看起来这基座上本该有个按钮的，不过不知道被谁撬走了。现在它只是一个空壳子。<br>";
				return;
			}
			elseif(strpos($itm,'黑熊键刃')!==false)
			{
				if(!\skillbase\skill_getvalue(1003,'used_bearkeysword'))
				{
					\skillbase\skill_setvalue(1003,'used_bearkeysword',1);
				}	
				$bear_keysword=true;
				ob_clean();
				include template(MOD_INSTANCE98_STORY_BRANCH_CONFIRM);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			elseif(strpos($itm,'Way of Life')!==false)
			{
				$way_of_life=true;
				ob_clean();
				include template(MOD_INSTANCE98_STORY_BRANCH_CONFIRM);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
		}
		$chprocess($theitem);
	}
	
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','input','map','explore'));
		if($mode == 'teleport_confirm')
		{
			if($command == 'menu')
			{
				$log.="你仔细思考了一下，自己在这边还有没办完的事情。<br>";
			}	
				
			elseif($command == 'confirm') 
			{
				$log.="你踏上银色的阶梯，拾级而上，直到临近天穹。你回头望去，来时的阶梯已消失不见。<br>……<br>当你拨开迷雾，来到天空的另一端时，呈现在你眼前的是一副奇妙的景象——<br>";
				$pls = 94;//伪造移动
				//\explore\move_to_area(94);
				$log.="{$areainfo[$pls]}";
				bear_keysword_event();
			}
			else
			{
				$log.="teleport_confirm相关：所输入的指令无效。{$command}<br>";
			}				
		}
		if($mode == 'bear_branches' || $mode == 'way_of_life')
		{
			\instance98\story_branch_event($mode,$command);
		}	
		$chprocess();
	}
	
	function living_npc($t=0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys'));
		$result = $db->query("SELECT pid FROM {$tablepre}players WHERE type=$t AND hp > 0");
		$npcnum = $db->num_rows($result);
		return $npcnum;
	}
	
	function move($moveto = 99) {
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys','instance98','map','player','logger'));
		if(98 == $gametype)
		{
			$ban ="你在迷雾中找寻着通往<span class=\"yellow b\">{$plsinfo[$moveto]}</span>的道路……却总是被突然杀出的敌人拦下。<br>看起来你需要彻底击败<span class=\"yellow b\">{$plsinfo[$moveto+1]}</span>内的敌人才能继续探索。<br>";
			if($moveto==34)
			{
				if($gamevars['valhalla'] && $weather>17)
				{
					$log.="在战术界面的指示下，你推开了属于英灵殿的那扇厚重的木门。<br>在那一刹那，一道白色的光芒将你包裹其中。<br>突然出现的强光使你条件反射闭上了眼，而当你再睁开眼时，呈现在眼前的是一条";
					$pls = 98;//伪造移动
					\explore\move_to_area(98);
					return;
				}
				else					
				{
					$randpls = rand($areanum+1,sizeof($arealist));
					while($randpls==34) $randpls = rand($areanum+1,sizeof($arealist));
					$pls = $arealist[$randpls];
					$log.="你沿着地图指定的坐标方向，朝着英灵殿的位置前行……本该是这样的。<br>可回过神来，你却发现自己已身处{$pls}。<br>这是怎么一回事呢……？也许该问问黑熊精灵。<br>";
					return;
				}
			}
			elseif(($moveto==97 && living_npc(20)) || ($moveto==96 && living_npc(21)) || ($moveto==95 && living_npc(22)))
			{
				$log.=$ban;
				return;
			}
		}
		$chprocess($moveto);
	}
	
	function check_event1003()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger'));
		if(\skillbase\skill_getvalue(1003,'used_bearbook'))
		{
			\skillbase\skill_setvalue(1003,'used_bearbook',0);
			$log.="你感觉自己又可以继续学习那本语录了。<br>";
		}
	}
	
	function post_addarea_process($atime, $areaaddlist)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		if(\skillbase\skill_query(1003) && check_unlocked1003()){
			check_event1003();
		}
		$chprocess($atime, $areaaddlist);
	}
	
	function findteam(&$edata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys','player','metman','logger'));
		$skill602_end = floor(\skillbase\skill_getvalue(602,'end',$edata)); 
		$ct = floor(getmicrotime()*1000);
		if($ct<$skill602_end) $flag602=true;
		$chprocess($edata);
	}	
	
	function senditem()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','map','logger','player','metman','input'));
		$mateid = str_replace('team','',$action);
		if(!$mateid || strpos($action,'team')===false){
			$log .= '<span class="yellow b">你没有遇到队友，或已经离开现场！</span><br>';			
			$mode = 'command';
			return;
		}	
		$edata=\player\fetch_playerdata_by_pid($mateid);
		if($command=='getup')
		{
			$log .= "<span class='yellow b'>你一巴掌打醒了你的队友……虽然有点残忍，不过总比让人晕着好！</span><br>";
			$x = "<span class=\"yellow b\">$name</span>将你从眩晕状态中打醒了！";
			if(!$edata['type']) \logger\logsave($edata['pid'],$now,$x,'t');
			$ct = floor(getmicrotime()*1000);
			\skillbase\skill_setvalue(602,'end',$ct,$edata); 
			\player\player_save($edata);
			$stn = (int)\skillbase\skill_getvalue(602,'stn',$edata);
			if($stn)
			{
				$edata = \player\fetch_playerdata_by_pid($stn);
				$log.="<span class=\"yellow b\">现在那个邪恶的家伙没有人质可用了！</span>";
				\skillbase\skill_setvalue(1906,'var',0,$edata);
				\player\player_save($edata);
			}
			return;
		}
		else
		{
			$chprocess();
		}	
	}	
	
	function get_shopconfig(){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys'));
		if ($gametype==98){
			$file = __DIR__.'/config/shopitem.config.php';
			$s98 = openfile($file);
			return $s98;
		}else return $chprocess();
	}
	
	function get_npclist(){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys','instance98'));
		if (98 == $gametype){
			return $npcinfo_instance98;
		}else return $chprocess();
	}
	
	function get_enpcinfo()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','instance98'));
		if (98 == $gametype){
			return $enpcinfo_instance98;
		}else return $chprocess();
	}
	
	function get_itemfilecont(){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys'));
		if (98 == $gametype){
			$file = __DIR__.'/config/mapitem.config.php';
			$l = openfile($file);
			return $l;
		}else return $chprocess();
	}
	
	function get_startingitemfilecont(){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys'));
		if (98 == $gametype){
			$file = __DIR__.'/config/stitem.config.php';
			$l = openfile($file);
			return $l;
		}else return $chprocess();
	}
	
	function get_startingwepfilecont(){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys'));
		if (98 == $gametype){
			$file = __DIR__.'/config/stwep.config.php';
			$l = openfile($file);
			return $l;
		}else return $chprocess();
	}
	
	function get_trapfilecont(){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys'));
		if (98 == $gametype){
			$file = __DIR__.'/config/trapitem.config.php';
			$l = openfile($file);
			return $l;
		}else return $chprocess();
	}
	
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));
		
		if($news == 'valopen98') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"brickred b\">{$a}使用了游戏解除钥匙，通往英灵殿的道路被开启了！</span></li>";
		if($news == 'bearkey') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"brickred b\">{$a}使用了黑熊键刃，祝你好运。</span></li>";
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>