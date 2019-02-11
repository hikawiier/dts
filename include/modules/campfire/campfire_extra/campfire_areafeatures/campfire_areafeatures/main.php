<?php

namespace campfire_areafeatures
{
	function init()
	{
	}
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','campfire_areafeatures','campfire_areafeatures_etconsole','campfire_areafeatures_transforgun','campfire_areafeatures_depot','campfire_areafeatures_transfortrap','input'));
		if ($mode == 'command' && $command == 'campfire_areafeatures')	
		{
			//判断该地区是否有该功能
			$lp_name = substr($lp_cmd,3);
			if( ($campfire_areafeatures_map[$pls]!==$lp_name && !in_array($lp_name,$campfire_areafeatures_map[$pls])) || !in_array($pls,array_keys($campfire_areafeatures_map)))
			{
				$log.="该地图没有{$lp_name}功能，或该功能不在此地区。如果遇到了BUG，请您将这句话转述给管理员。<br>";
			}
			//地区功能开始
			if($lp_cmd=="lp_campfire_areafeatures_etconsole")
			{
				//无月之影：gmesysctl
				$check_campfire_areafeatures_etconsole_flag = 0;
				$check_campfire_areafeatures_etconsole_flag = \campfire_areafeatures_etconsole\check_campfire_areafeatures_etconsole();
				if($check_campfire_areafeatures_etconsole_flag){
					ob_clean();
					include template(MOD_campfire_areafeatures_etconsole_lp_campfire_areafeatures_etconsole);
					$cmd = ob_get_contents();
					ob_clean();
					return;
				}
			}
			elseif($lp_cmd=='lp_campfire_areafeatures_depot')
			{
				//精灵中心：campfire_areafeatures_depot
				ob_clean();
				include template(MOD_campfire_areafeatures_depot_lp_campfire_areafeatures_depot);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			elseif($lp_cmd=='lp_campfire_areafeatures_transforgun')
			{
				if($wepk=='WG' || $wepk=='WJ' || $wepk=='WDG' || $wepk=='WGK')
				{
					$wep_skind = $wepsk ? str_split($wepsk) : Array();
				}
				//F前：campfire_areafeatures_transforgun
				ob_clean();
				include template(MOD_campfire_areafeatures_transforgun_lp_campfire_areafeatures_transforgun);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			elseif($lp_cmd=='lp_campfire_areafeatures_transfortrap')
			{
				//和田：campfire_areafeatures_transfortrap
				$change_fail_obbs = max(0,round(100-($wd*0.35)));
				ob_clean();
				include template(MOD_campfire_areafeatures_transfortrap_lp_campfire_areafeatures_transfortrap);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			else
			{
				$log.="该地图没有{$lp_name}功能，如果遇到了BUG，请您将这句话转述给管理员。<br>";
			}
			return;
		}
		$chprocess();
	}
}

?>
