<?php

namespace skill1912
{	
	function init() 
	{
		define('MOD_SKILL1912_INFO','card;unique;feature;');
		eval(import_module('clubbase'));
		$clubskillname[1912] = '朋友';
	}
	
	function acquire1912(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$maxsans = rand(1,20);
		$maxsans *= 5;
		\skillbase\skill_setvalue(1912,'sans',$maxsans,$pa);
	}
	
	function lost1912(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(1912,$pa)) \skillbase\skill_lost(1912,$pa);
	}
	
	function check_unlocked1912(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function get_sans1912(&$pa = NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return \skillbase\skill_getvalue(1912,'sans',$pa);
	}
	
	//朋友判定
	function get_friendship1912(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','logger','player'));
		$_name = $pa['name'];
		$_team = $pa['teamID'];
		$_pls = $pa['pls']; 
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE name!='$_name' AND teamID='$_team' AND pls=$_pls AND type = 0 AND hp > 0");
		if(!$db->num_rows($result)) {
			return 0;
		} else {
			$fdata = $db->fetch_array($result);
			$fdata = \player\fetch_playerdata($fdata['name']);
			//调查员之间不会互相伤害
			if(\skillbase\skill_query(1912,$fdata) && check_unlocked1912($fdata))
				return 0;	
			else
				return $fdata;	
		}
	}
	
	//朋友走了
	function kill_friendship1912(&$pa,&$pd)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		$pd['state'] = 204;
		$pa['attackwith']='skill1912';
		\player\kill($pa,$pd);
		\player\player_save($pd);
	}
	
	//复活判定注册
	function set_revive_sequence(&$pa, &$pd)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd);
		if(\skillbase\skill_query(1912,$pd) && check_unlocked1912($pd)){
			$pd['revive_sequence'][25] = 'skill1912';
		}
		return;
	}

	//复活判定
	function revive_check(&$pa, &$pd, $rkey)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger','skill1912'));
		$ret = $chprocess($pa, $pd, $rkey);
		$gc = get_friendship1912($pd);
		if('skill1912' == $rkey && in_array($pd['state'],Array(20,21,22,23,24,25,27,29,39,40,41,43)) && $gc){
			$ret = true;
			kill_friendship1912($pd,$gc);			
		}
		return $ret;
	}
	
	//发复活状况
	function post_revive_events(&$pa, &$pd, $rkey)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $rkey);
		if('skill1912' == $rkey){
			$pd['hp']=$pd['mhp'];
			$pd['skill1912_flag']=1;
			$pd['rivival_news'] = array('revival1912', $pd['name']);
					//san check 0/1d6
			$san_check = rand(0,6);
			$sans = get_sans1912($pd);
			if(5<$sans/$san_check)
			{
				$log.="你的精神受到了极大的打击，你不能再依靠你的朋友了。<br>";
				lost1912($pd);
			}
			else
			{
				$log.="目睹了朋友在你眼前死亡，你的理智值降低了！<br>";
				$sans = $sans-$san_check;
				\skillbase\skill_setvalue(1912,'sans',$sans,$pd);
			}
		}
		return;
	}
	
	function kill(&$pa, &$pd)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$ret = $chprocess($pa,$pd);
		
		eval(import_module('sys','logger'));
		
		if(!empty($pd['skill1912_flag'])){
			if ($pd['o_state']==27)	//陷阱
			{
				$log.= "<span class=\"lime b\">但是，你的朋友忽然瞬移到了你的身边将你一把推开！！</span><br>";
				if(!$pd['sourceless']){
					$w_log = "<span class=\"lime b\">但是，{$pd['name']}的朋友忽然瞬移到了你的身边将你一把推开！</span><br>";
					\logger\logsave ( $pa['pid'], $now, $w_log ,'b');
				}
			}
		}
		return $ret;
	}
	
	function player_kill_enemy(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$chprocess($pa,$pd,$active);
		
		eval(import_module('sys','logger'));
		if (isset($pd['skill1912_flag']) && $pd['skill1912_flag'])
		{
			if ($active)
			{
				$log.='<span class="lime b">但是，对方的朋友忽然瞬移到了它的身边替它挡下了致命一击！</span><br>';
				$pd['battlelog'].='<span class="lime b">但是，你的朋友忽然瞬移到了你的身边替你挡下了致命一击！</span>';
			}
			else
			{
				$log.='<span class="lime b">但是，你的朋友忽然瞬移到了你的身边替你挡下了致命一击！</span><br>';
				$pd['battlelog'].='<span class="lime b">但是，对方的朋友忽然瞬移到了它的身边替它挡下了致命一击！</span>';
			}
		}
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		
		if($news == 'death204') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"seagreen b\">{$a}为保护朋友壮烈成仁了！</span>{$e0}</li>";
		if($news == 'revival1912') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">{$a}在朋友的保护下死里逃生！</span></li>";
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
	
	
}

?>
