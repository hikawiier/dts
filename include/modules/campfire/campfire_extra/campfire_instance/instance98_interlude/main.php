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
		$chprocess();
	}
	function get_trap_escape_rate()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		if($pls == 92 || $pls == 93 || $pls == 94)	return $chprocess()-120;	
		$chprocess();
	}
	
	function calculate_trapdef_proc_rate()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		if ($pls == 92 || $pls == 93 || $pls == 94)	return $chprocess()-30;			
		$chprocess();
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
			$result = $db->query("SELECT cid FROM {$tablepre}chat WHERE send='一一五'");
			if(!$result) \sys\addchat(6, '来英灵殿吧，本小姐今天就要……哎哎哎……怎么突然黑屏了？！', '一一五');
			return;
		}	
		$chprocess($ntype, $nsub, $num);
	}
	function story_branch_event($story,$branch)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger'));
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
				$log.="文本暂时没有补全，请等待后续游戏内容更新！<br>";
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
			if(strpos($theitem['itm'],'游戏解除钥匙')!==false)
			{
				$log.="你看了看手中的钥匙，说出了那句你已烂熟于心的台词：<br><span class='yellow b'>“我是 {$name}。精神锁定解除。”</span><br>
				狂风卷起，你神情庄重，静候接下来的变化。<br>……<br>然而什么也没有发生。<br>你在这有些尴尬的沉默中等待了一会儿。<br><br>
				<span class='linen b'>“可恨啊！你作为一个触手，就这样就满足了吗！”</span><br>你听到有声音打破了沉默的氛围，而且那声音听起来耳熟极了。<br>
				<span class='linen b'>“有点挑战精神好不好？？我在<span class='yellow b'>英灵殿</span>等你。”</span><br>
				你还想再问两句，但手中的游戏解除钥匙忽然<span class='red'>爆炸</span>了！<br>
				……！？<br>";
				\itemmain\itms_reduce($theitem);
				$log.="<span class='yellow b'>你的怒气增加了100点！</span><br>";
				$log.="看来只能去英灵殿看看了……？<br>";
				if(!$gamevars['valhalla'])
				{
					$gamevars['valhalla'] = 1;
					\sys\save_gameinfo();
					addnews($now, 'valopen98',$name);
				}
				$rage=100;			
				return;
			}
			elseif(strpos($theitem['itm'],'《黑熊语录》')!==false)
			{
				if(\skillbase\skill_getvalue(1003,'used_bearbook'))
				{
					$log.="你感觉自己暂时没法从这里学到什么新的<del>嘴臭</del>知识了。<br>也许可以将它分享给你的伙伴们看看。<br>";
					return;
				}	
				$nowitme = $theitem['itme'];
				$add_skillpoint = $theitem['itme']>1 ? rand(1,$theitem['itme']) : 1;	
				$theitem['itme'] -= $add_skillpoint;
				$skillpoint+=$add_skillpoint;
				$log.="你粗略的翻看了一遍黑熊语录，饶是满腹经纶的你也感觉受益匪浅！<br>你获得了<span class='yellow b'>{$add_skillpoint}</span>点技能点！<br>";
				if($theitem['itme'])
				{
					$log.="看起来里面还有不少值得学习的内容，你决定把它保存下来。<br>也许可以将它分享给你的伙伴们看看。<br>";
				}
				else
				{
					$log.="看起来你已经花10分钟完全搞懂里面的内容了。<br>你随手就把它丢掉了。<br>";
					\itemmain\itms_reduce($theitem);				
				}
				if($add_skillpoint==$nowitme)
				{
					$itme0=1;$itms0=1;
					$log.="<span class='yellow b'>你发现有张卡牌黏在了你的衣服上！大概是从书里掉出来的……？</span><br>";
					if($add_skillpoint>50)
					{
						$itm0='画有熊本熊的迷之卡牌';$itmk0='VO';$itmsk='13';
					}
					else
					{
						$log.="<span class='yellow b'>但这张卡似乎已经被人用过很多次了的样子……</span><br>";
						$itm0='本来画有熊本熊的迷之卡牌';$itmk0='VO3';$itmsk='';
					}
					\itemmain\itemget();
				}
				\skillbase\skill_setvalue(1003,'used_bearbook',1);
				return;
			}
			elseif(strpos($theitem['itm'],'黑熊键刃')!==false)
			{
				$bear_keysword=true;
				ob_clean();
				include template(MOD_INSTANCE98_STORY_BRANCH_CONFIRM);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			elseif(strpos($theitem['itm'],'Way of Life')!==false)
			{
				$way_of_life=true;
				ob_clean();
				include template(MOD_INSTANCE98_STORY_BRANCH_CONFIRM);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			$chprocess($theitem);
		}
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
				$log.="你踏上银色的阶梯，逐级向上，直到临近天穹，你回头望去，来时的阶梯已消失不见。<br>……<br>当你拨开迷雾，来到天空的另一端时，你看到了一副奇妙的景象。<br>";
				$pls = 94;//伪造移动
				\explore\move_to_area(94);
			}		
			else
			{
				$log.="teleport_confirm相关：所输入的指令无效。{$command}<br>";
			}				
		}
		if($mode == 'bear_branches' || $mode == 'way_of_life')
		{
			if($command == 'menu')	$log.="谢谢理解！<br>";
			else	\instance98\story_branch_event($mode,$command);
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
			$ban ="殿堂的深处传来一个极力想模仿出严肃的感觉，但反而显得有点搞笑的的声音：<br>“<span class=\"linen b\">要进入<span class=\"yellow b\">{$plsinfo[$moveto]}</span>，你必须先通过<span class=\"yellow b\">{$plsinfo[$moveto+1]}</span>的试炼！你懂不懂RPG的啊！</span>”<br>";
			if($moveto==34)
			{
				if($gamevars['valhalla'])
				{
					$log.="根据记忆中的位置，你推开了属于英灵殿的那扇厚重的木门。<br>在那一刹那，一股白色的光芒将你包裹了进去。<br>那强光刺得你闭上了眼。<br>当你反应过来的时候，呈现在你眼前的是一条";
					$pls = 98;//伪造移动
					\explore\move_to_area(98);
					return;
				}
				else					
				{
					$randpls = rand($areanum+1,sizeof($arealist));
					while($randpls==34) $randpls = rand($areanum+1,sizeof($arealist));
					$pls = $arealist[$randpls];
					$log.="殿堂的深处传来一个极力想模仿出严肃的感觉，但反而显得有点搞笑的的声音：<span class=\"linen b\">“你还没有进入这里的资格，快滚！”</span><br>一股未知的力量包围了你，当你反应过来的时候，发现自己正身处<span class=\"yellow b\">{$plsinfo[$pls]}</span>。<br>";
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
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>