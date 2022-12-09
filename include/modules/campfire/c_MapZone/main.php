<?php

namespace c_mapzone
{	
	function init() 
	{
		global $mapzone_coorlist,$mapzone_coorarr,$mapzone_speclist,$mapzone_pfloor,$mapzone_end,$mapzone_weather;$mapzone_vars;
		/*$mapzone_update_flag = check_need_update_mapzonedata();
		if($mapzone_update_flag)
		{
			echo "更新了一次地图";
			$mapzonedata = load_mapzonedata();	
		}*/
		eval(import_module('sys','map'));
		for($f=1;$f<=$areamax;$f++)
		{
			$result = $db->query("SELECT * FROM {$tablepre}mapzone WHERE pfloor='$f'");
			if ($db->num_rows($result))
			{
				$mapzonedata = $db->fetch_array($result);
				$mapzone_pls = $mapzonedata['pls'];
				$mapzone_pfloor[$mapzone_pls] = $mapzonedata['pfloor'];
				$mapzone_end[$mapzone_pls] = $mapzonedata['zoneend'];
				$mapzone_vars[$mapzone_pls] = $mapzonedata['zonevars'];
				$mapzone_weather[$mapzone_pls] = $mapzonedata['weather'];
				$speclist = $mapzonedata['speclist'];
				$speclist = json_decode($speclist,true);
				$coorlist = $mapzonedata['zonelist'];
				$coorlist = gdecode($coorlist,true);
				$mapzone_coorlist[$mapzone_pls] = $coorlist;
				$mapzone_speclist[$mapzone_pls] = $speclist;
				$mapzone_coorarr[$mapzone_pls] = change_coorlist_to_coorarr($coorlist);
				//为什么一个coorlist不够，还需要一个cooarr? <--现在又加上一个 speclist
				//因为存在两种需求 以坐标为索引寻找区域编号 和 以区域编号为索引寻找坐标 <--现在又多了一个需求 以区域类型为索引找区域编号
				//是很蛋疼
			}
		}
	}

	function rs_game($xmode = 0) 	//开局区域表初始化
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($xmode);		
		eval(import_module('sys','c_mapzone'));
		if ($xmode & 4) 
		{
			//把区域初始化做一下
			rs_mapzone();
		}
	}
	
	function rs_mapzone(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','c_mapzone'));
		//开始初始化区域表
		$db->query("DELETE FROM {$tablepre}mapzone WHERE pfloor>0 ");
		foreach($arealist as $p)
		{
			//地图编号
			$mapzone_pls = $p; 
			//地图层数
			$f = array_search($mapzone_pls,$arealist); 
			//初始化地图危险度
			$map_intensity = get_mapzone_intensity($f,$mapzone_pls);
			//初始化地图大小
			$room_size = array();
			$room_size = get_mapzone_room_size($map_intensity);
			$x_max = $room_size['x_axis'];
			$y_max = $room_size['y_axis'];
			$rooms_max = $room_size['rooms_max'];
			//初始化空白坐标组
			$coordinates = get_mapzone_coordinates($room_size);
			//初始化最末房间编号
			$room_end = $rooms_max-1; 
			//生成带坐标区域列表
			$mapzone_list = get_roomlist_arr($coordinates, $start_x, $start_y, 1, $rooms_max, $x_max, $y_max, $dr, $px, $py);
			//初始化特殊区域 插入到区域列表里
			$mapzone_list = get_special_roomlist_arr($f,$mapzone_list,$room_end,$x_max,$y_max);
			//提取特殊种类房间——这一步完全可以合并到上面一步里，但是为了尽快出DEMO先一切从懒
			$mapzone_spec = change_coorlist_to_specarr($mapzone_list,$room_end);
			//再生产
			$mapzone_spec = json_encode($mapzone_spec);
			$mapzone_list = gencode($mapzone_list);
			$db->query("INSERT INTO {$tablepre}mapzone (pls, intensity, weather, zoneend, zonelist, speclist, zonevars) VALUES ('$mapzone_pls', '$map_intensity', '$mapzone_weather', '$room_end', '$mapzone_list', '$mapzone_spec','')");
		}
		//这时候gamevar还没被处理 那就这么搞吧！
		/*$file = GAME_ROOT.'./gamedata/cache/'.$groomid.'.mapzonedata.lock';
		if(file_exists($file))
		{
			unlink($file);
		}
		$data = 'needupdate';
		writeover($file, $data);
		chmod($file,0777);*/
	}

	function load_mapzonedata()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		$tmp_mapzonedata = Array();
		for($f=1;$f<=$areamax;$f++)
		{
			$result = $db->query("SELECT * FROM {$tablepre}mapzone WHERE pfloor='$f'");
			if ($db->num_rows($result))
			{
				$tmp_data = $db->fetch_array($result);
				$tmp_pls = $tmp_data['pls'];
				$tmp_mapzonedata[$tmp_pls] = $tmp_data;
				//处理zonelist
				$tmp_zonelist = $tmp_data['zonelist'];
				$tmp_zonelist = gdecode($tmp_zonelist,true);
				$tmp_mapzonedata[$tmp_pls]['zonelist'] = $tmp_zonelist;
				//处理speclist
				$tmp_speclist = $tmp_data['speclist'];
				$tmp_speclist = json_decode($tmp_speclist,true);
				$tmp_mapzonedata[$tmp_pls]['speclist'] = $tmp_speclist;
			}
		}
		return $tmp_mapzonedata;
	}

	function update_mapzonedata($p,$znew,$kind=0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','c_mapzone'));
		switch($kind){
			case 'weather':
				$db->query("UPDATE {$tablepre}mapzone SET weather='$znew' WHERE pls='$p'");
				break;
			case 'zonevars':
				$db->query("UPDATE {$tablepre}mapzone SET zonevars='$znew' WHERE pls='$p'");
				break;
			default:
				return;
		}
		do {
			$file = GAME_ROOT.'./gamedata/cache/'.$groomid.'.mapzonedata.lock';
		} while(file_exists($file));
		/*$data = 'needupdate';
		writeover($file, $data);
		chmod($file,0777);*/
	}

	function check_need_update_mapzonedata()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		$file = GAME_ROOT.'./gamedata/cache/'.$groomid.'.mapzonedata.lock';
		if(file_exists($file))
		{
			$cont = file_get_contents($file);
			unlink($file);
		}
		if(strpos($cont,'needupdate')===0)
		{
			$cont = '';
			return true;
		}
		return false;
	}

	function post_gameover_events()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess();
		eval(import_module('sys'));
		//游戏结束时有没删干净的mapzonedata文件给它清理掉
		$file = GAME_ROOT.'./gamedata/cache/'.$groomid.'.mapzonedata.lock';
		if(file_exists($file)) unlink($file);
	}
}

?>