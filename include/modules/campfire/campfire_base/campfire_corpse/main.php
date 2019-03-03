<?php

namespace campfire_corpse
{
	$check_can_changedepot_list=Array(0,1);
	
	function init() {}
	
	function findcorpse(&$edata){
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','player','corpse','campfire_corpse'));	
		if(in_array($edata['type'],$check_can_changedepot_list)) $w_can_changedepot = check_can_changedepot($edata['name'],$edata['type']);
		$chprocess($edata);
	}
	function check_can_changedepot($n,$t)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','corpse','campfire_corpse','areafeatures_depot'));
		if(in_array($t,$check_can_changedepot_list))
		{
			$flag = \areafeatures_depot\areafeatures_depot_getlist($n,$t);
			if($flag) return 1;
		}			
		return 0;
	}
	
	function getcorpse_action(&$edata, $item)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','player','logger','corpse'));		
		if($item == 'changedepot') {
			if(!check_can_changedepot($edata['name'],$edata['type']))
			{
				$log.="这具尸体生前没有在仓库里存过东西！";
				$mode = 'command';
				return;
			}	
			\areafeatures_depot\areafeatures_depot_changeowner($edata['name'],$edata['type'],$sdata['name'],$sdata['type']);
			$log .= "你将{$edata['name']}生前存在仓库里的东西转移到了自己的名下！";
			addnews ( 0, 'cdown', $sdata['name'], $edata['name'] );
			$mode = 'command';
			return;
		}
		$chprocess($edata,$item);
	}

	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player'));
		
		if($news == 'cdown') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime b\">{$a}将{$b}生前存在仓库里的东西转移到了自己的名下！</span></li>";
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>
