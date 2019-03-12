<?php

namespace campfire_bgm
{
	function init() 
	{
	}
		
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','input','explore','campfire_areafeatures','instance98'));
		if(($mode == 'command' && $command=='move' && $moveto==34 && $gametype==98) 
		||($mode=='command' && $command=='campfire_areafeatures' && strpos($lp_cmd,'teleport98')!==false)
		||($mode == 'teleport_confirm' && $command == 'confirm'))
		{
			$refreash_flag = true;
		}	
		$chprocess();
		if($refreash_flag) refreash_bgm_page();
	}
	
	function refreash_bgm_page()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','input'));	
		$gamedata['innerHTML']['cmd_interface'] = dump_template('cmd_interface');
	}	
	function get_area_bgm()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','player','campfire_bgm'));
		$bgmlink ='';
		foreach($bgm_areagroup as $areagroup=>$bgmlink)
		{
			if(in_array($pls,$hidden_areagroup[$areagroup]) || in_array($pls,$bgm_arealist)) return $bgmlink;
		}
		return 0;
	}
}

?>