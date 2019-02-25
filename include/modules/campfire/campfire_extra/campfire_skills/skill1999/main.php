<?php

namespace skill1999
{	
	function init() 
	{
		define('MOD_SKILL1999_INFO','hidden;upgrade;');
	}
	
	function acquire1999(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(1999,'sitm','',$pa);
		\skillbase\skill_setvalue(1999,'spls',0,$pa);
		\skillbase\skill_setvalue(1999,'starttime','',$pa);
		\skillbase\skill_setvalue(1999,'lasttime','',$pa);
	}
	
	function lost1999(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1999(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function check_iteminmap1999($i,$p,$kind=0)
	{
		//0-返回地图上存在数量 / 1-返回查询结果
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','map'));
		$result = $db->query("SELECT * FROM {$tablepre}mapitem WHERE pls = '$p' AND itm = '$i'");
		$inum = $db->num_rows($result);
		if(!$inum) $inum=0;	
		if($kind==0) return $inum;
		else return $result;
	}
	
	function activate1999()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1999','player','logger','sys','itemmain','map'));
		\player\update_sdata();
		if (!\skillbase\skill_query(1999) || !check_unlocked1999($sdata))
		{
			$log.='你并没有派出寻物者！<br>';
			return;
		}
		$st = check_skill1999_state($sdata);
		if ($st==0){
			$log.="这是一个不应该发生的错误，你应该联系管理员！<br>";
			return;
		}
		if ($st==1){
			$log.='寻物者尚在探索中！<br>';
			return;
		}
		$sitm = \skillbase\skill_getvalue(1999,'sitm',$pa);
		$spls = \skillbase\skill_getvalue(1999,'spls',$pa);
		$result = check_iteminmap1999($sitm,$spls,1);
		$sitemnum = check_iteminmap1999($sitm,$spls,0);
		if($sitemnum <= 0)
		{
			$log.="你召回了寻物者，但它似乎什么也没带回来。<br>……是<span class='yellow b'>{$plsinfo[$spls]}</span>已经没有<span class='yellow b'>{$sitm}</span>了吗？<br>你愤怒的踢了它一脚，那原本就破破烂烂的人偶这下直接倒在地上，再也不动了。<br>";
		}
		else
		{
			$log.="你召回了寻物者，它似乎为你带回了什么东西。<br>但当你从它手中接过<span class='yellow b'>{$sitm}</span>后，那原本就破破烂烂的人偶便直接倒在了地上。<br>……是已经没用了吗？<br>";
			$sitemno = rand(0,$sitemnum-1);
			$db->data_seek($result,$sitemno);
			$smi=$db->fetch_array($result);
			$itms0 = \itemmain\focus_item($smi);
			if($itms0) \itemmain\itemget();
			addnews ( 0, 'bskill1999', $name , $sitm, $spls);
		}	
		\skillbase\skill_lost(1999,$pa);
	}
	
	//return 1:搜索中 2:搜索结束 其他:不能使用这个技能
	function check_skill1999_state(&$pa){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (!\skillbase\skill_query(1999, $pa) || !check_unlocked1999($pa)) return 0;
		eval(import_module('sys','player','skill1999'));
		$start=\skillbase\skill_getvalue(1999,'starttime',$pa);
		$last=\skillbase\skill_getvalue(1999,'lastttime',$pa);
		if (($now-$start)>$last) return 2;
		return 1;
	}
	
	function bufficons_list()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		\player\update_sdata();
		if ((\skillbase\skill_query(1999,$sdata))&&check_unlocked1999($sdata))
		{
			eval(import_module('skill1999'));
			$skill1999_stm = (int)\skillbase\skill_getvalue(1999,'starttime'); 
			$skill1999_lst = (int)\skillbase\skill_getvalue(1999,'lasttime'); 
			$z=Array(
				'disappear' => 0,
				'clickable' => 1,
				'hint' => '寻物者正在探索中……',
				'onclick' => "$('mode').value='special';$('command').value='skill1999_special';$('subcmd').value='activate';postCmd('gamecmd','command.php');this.disabled=true;",
			);
			if ($now<=$skill1999_stm+$skill1999_lst)
			{
				$z['style']=1;
				$z['totsec']=$skill1999_lst;
				$z['nowsec']=$now-$skill1999_stm;
				$z['clickable']=0;
			}
			else 
			{
				$z['style']=3;
				$z['clickable']=1;
				$z['activate_hint']='唤回你的寻物者';
			}
			\bufficons\bufficon_show('img/skill1999.gif',$z);
		}
		$chprocess();
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','map'));
		
		if($news == 'bskill1999') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime b\">寻物者为{$a}找来了原本位于{$plsinfo[$c]}的<span class=\"yellow b\">{$b}</span>！</span></li>";
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>
