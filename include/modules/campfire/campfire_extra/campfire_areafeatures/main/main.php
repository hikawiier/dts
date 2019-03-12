<?php

namespace campfire_areafeatures
{
	function init()
	{
	}
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','map','instance98','campfire_areafeatures','areafeatures_etconsole','areafeatures_transforgun','areafeatures_depot','areafeatures_transfortrap','input'));
		if ($mode == 'command' && $command == 'campfire_areafeatures')	
		{
			//判断该地区是否有该功能
			$lp_name = substr($lp_cmd,3);
			if( ($campfire_areafeatures_map[$pls]!==$lp_name && !in_array($lp_name,$campfire_areafeatures_map[$pls])) || !in_array($pls,array_keys($campfire_areafeatures_map)))
			{
				$log.="该地图没有{$lp_name}功能，或该功能不在此地区。如果遇到了BUG，请您将这句话转述给管理员。<br>";
			}
			//地区功能开始
			if($lp_cmd=="lp_areafeatures_etconsole")
			{
				//无月之影：gmesysctl
				$check_areafeatures_etconsole_flag = 0;
				$check_areafeatures_etconsole_flag = \areafeatures_etconsole\check_areafeatures_etconsole();
				if($check_areafeatures_etconsole_flag){
					ob_clean();
					include template(MOD_AREAFEATURES_ETCONSOLE_LP_AREAFEATURES_ETCONSOLE);
					$cmd = ob_get_contents();
					ob_clean();
				}
			}
			elseif($lp_cmd=='lp_areafeatures_depot')
			{
				//精灵中心：areafeatures_depot
				ob_clean();
				include template(MOD_AREAFEATURES_DEPOT_LP_AREAFEATURES_DEPOT);
				$cmd = ob_get_contents();
				ob_clean();
			}
			elseif($lp_cmd=='lp_areafeatures_transforgun')
			{
				if(!in_array($wepk,$gun_weapon_arr))
				{
					$log.="你的武器不属于枪械，为什么要使用枪械改造台？<br>";
					return;
				}
				$wep_skind = $wepsk ? \itemmain\get_itmsk_array($wepsk) : Array();
				$name_list='';
				foreach($repair_item_effect as $item_name => $item_succ_obbs)
				{
					if($item_succ_obbs=='e') $item_succ_obbs='效果x10';
					$name_list.="<span class='yellow'>【{$item_name}:<span class='lime'>{$item_succ_obbs}%</span>】</span>";
				}
				//F前：areafeatures_transforgun
				ob_clean();
				include template(MOD_AREAFEATURES_TRANSFORGUN_LP_AREAFEATURES_TRANSFORGUN);
				$cmd = ob_get_contents();
				ob_clean();
			}
			elseif($lp_cmd=='lp_areafeatures_transfortrap')
			{
				//和田：areafeatures_transfortrap
				$change_fail_obbs = min(90,round($wd*0.35));
				ob_clean();
				include template(MOD_AREAFEATURES_TRANSFORTRAP_LP_AREAFEATURES_TRANSFORTRAP);
				$cmd = ob_get_contents();
				ob_clean();
			}
			elseif(strpos($lp_cmd,'teleport')!==false)
			{
				if(strpos($lp_cmd,'98')!==false)
				{
					$randpls = rand($areanum+1,sizeof($arealist));
					while($randpls==34) $randpls = rand($areanum+1,sizeof($arealist));
					$pls = $arealist[$randpls];
					$log.="你回到了甬道的尽头，推开了那扇厚重的木门。<br>在那一刹那，一股白色的光芒将你包裹了进去。<br>那强光刺得你闭上了眼。<br>当你反应过来的时候，你发现自己已身处<span class=\"yellow b\">{$plsinfo[$pls]}</span>。<br>";
				}
				elseif(strpos($lp_cmd,'95')!==false)
				{
					if(\instance98\living_npc(1006))
					{
						$log.="在击败这里的敌人前，你无法翻越过云之阶。<br>";
						return;
					}	
					ob_clean();
					include template(MOD_INSTANCE98_TELEPORT_CONFIRM);
					$cmd = ob_get_contents();
					ob_clean();
				}
			}	
			else
			{
				$log.="该地图没有{$lp_name}功能，如果遇到了BUG，请您将这句话转述给管理员。<br>";
				return;
			}
		}
		$chprocess();
	}
}

?>
