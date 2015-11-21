<?php

namespace localproject
{
	function init()
	{
	}
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','localproject','gamesysctl','remakegun','itemdepot','input'));
		if ($mode == 'command' && $command == 'localproject')	
		{
			//判断该地区是否有该功能
			$lp_name = substr($lp_cmd,3);
			if( ($localproject_map[$pls]!==$lp_name && !in_array($lp_name,$localproject_map[$pls])) || !in_array($pls,array_keys($localproject_map)))
			{
				$log.="该地图没有{$lp_name}功能，或该功能不在此地区。如果遇到了BUG，请您将这句话转述给管理员。<br>";
			}
			//地区功能开始
			if($lp_cmd=="lp_gamesysctl")
			{
				//无月之影：gmesysctl
				$check_gamesysctl_flag = 0;
				$check_gamesysctl_flag = \gamesysctl\check_gamesysctl();
				if($check_gamesysctl_flag){
					ob_clean();
					include template(MOD_GAMESYSCTL_LP_GAMESYSCTL);
					$cmd = ob_get_contents();
					ob_clean();
					return;
				}
			}
			elseif($lp_cmd=='lp_itemdepot')
			{
				//精灵中心：itemdepot
				ob_clean();
				include template(MOD_ITEMDEPOT_LP_ITEMDEPOT);
				$cmd = ob_get_contents();
				ob_clean();
				return;
			}
			elseif($lp_cmd=='lp_remakegun')
			{
				if($wepk=='WG' || $wepk=='WJ' || $wepk=='WDG' || $wepk=='WGK')
				{
					$wep_skind = $wepsk ? str_split($wepsk) : Array();
				}
				//F前：remakegun
				ob_clean();
				include template(MOD_REMAKEGUN_LP_REMAKEGUN);
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
