<?php

namespace c_mapzone
{	
	function init() 
	{
		global $mapzone_coorlist,$mapzone_coorarr,$mapzone_pfloor,$mapzone_end,$mapzone_vars;
		$mapzonedata = NULL;
		eval(import_module('sys','map'));
		for($p=1;$p<=$areamax;$p++)
		{
			$result = $db->query("SELECT pls,zoneend,zonelist,zonevars FROM {$tablepre}mapzone WHERE pfloor='$p'");
			if ($db->num_rows($result))
			{
				$mapzonedata = $db->fetch_array($result);
				$mapzone_pls = $mapzonedata['pls'];
				$mapzone_pfloor[$mapzone_pls] = $p;
				$mapzone_end[$mapzone_pls] = $mapzonedata['zoneend'];
				$mapzone_vars[$mapzone_pls] = $mapzonedata['zonevars'];
				$coorlist = $mapzonedata['zonelist'];
				$coorlist = gdecode($coorlist,true);
				$mapzone_coorlist[$mapzone_pls] = $coorlist;
				//临时鼠族 $mapzone_coorarr 用一种看不懂的格式来记录房间↔坐标的对应关系 仅用于打印可视化地图
				//之后会把这一块换成存储在本地的缓存文件
				//哎呀！好像不行 因为十字方向移动判断四周是否存在邻接格的时候还要用到这个鼠族 要不就这样吧
				$mapzone_coorarr[$mapzone_pls] = change_coorlist_to_coorarr($coorlist);
			}
		}
		//这样搞真的能行吗？？？？		
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
		eval(import_module('sys','map','weather','c_mapzone'));
		//开始初始化区域表
		foreach($arealist as $p)
		{
			$mapzone_pls = $p; //地图编号
			$f = array_search($mapzone_pls,$arealist); //地图层数
			$mapzone_weather = $mapzonelist[$mapzone_pls]['weather'];	
			$room_size = array();
			$log_intensity = 'intensity';
			if($f == 0 || $f == $areaend)
			{ //首尾层地图搞些特殊化
				$room_size = get_mapzone_room_size($mapzone_pls);
				$log_intensity .= $mapzone_pls;
			}
			else
			{
				//决定地图所处的强度区间 刨除首尾2张地图 每经过占总数1/5的地图升级一次强度
				$i = 100*($f/($areamax-2));
				if($i >= 80 && $f > 8)
				{	//有8张以上地图 且位置位于4/5路程往上 生成强度4
					$room_size = get_mapzone_room_size(4);
					$log_intensity .= 4;
				}
				elseif($i >= 60 && $f > 6)
				{	//有6张以上地图 且位置位于3/5往上 生成强度3
					$room_size = get_mapzone_room_size(3);
					$log_intensity .= 3;
				}
				elseif($i >= 40 && $f > 4)
				{	//有4张以上地图 且位置位于2/5往上 生成强度2
					$room_size = get_mapzone_room_size(2);
					$log_intensity .= 2;
				}
				elseif($i >= 20 && $f > 2)
				{	//有2张以上地图 且位置位于1/5往上 生成强度1
					$room_size = get_mapzone_room_size(1);
					$log_intensity .= 1;
				}
				else
				{	//生成默认的最低强度地图
					$room_size = get_mapzone_room_size(99);
					$log_intensity .= 99;
				}
			}
			$log_intensity .= ',';
			$x_max = $room_size['x_axis'];
			$y_max = $room_size['y_axis'];
			$rooms_max = $room_size['rooms_max'];
			$room_end = $rooms_max-1; //这个room_end指向的是出口所在的房间 很关键
			$coordinates = get_mapzone_coordinates($room_size);
			//生成带坐标区域列表
			$mapzone_list = get_roomlist_arr($coordinates, $start_x, $start_y, 1, $rooms_max, $x_max, $y_max, $dr, $px, $py);
			//初始化特殊区域 插入到区域列表里
			$mapzone_list = get_special_roomlist_arr($f,$mapzone_list,$room_end,$x_max,$y_max);
			//再生产
			$mapzone_list = gencode($mapzone_list);
			$db->query("INSERT INTO {$tablepre}mapzone (pls, weather, zoneend, zonelist, zonevars) VALUES ('$mapzone_pls', '$mapzone_weather', '$room_end', '$mapzone_list', '$log_intensity')");
		}
	}
	
	function get_roomlist_arr($c, $x, $y, $r, $rm, $xm, $ym, $px, $p)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//开始生成随机区域格
		$room_arr = generate_room($c, $x, $y, $r, $rm, $xm, $ym, $px, $p);
		//整理生成后的随机区域格鼠族
		return change_coorarr_to_coorlist($room_arr,$xm,$ym);
	}
	
	function change_coorarr_to_coorlist($c,$x_max,$y_max)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		//整理生成的随机区域格
		$room_list = Array();
		for($x=0;$x<=$x_max;$x++)
		{
			//echo '<br>';
			for($y=0;$y<=$y_max;$y++)
			{
				if($c[$x][$y]>0){
					//echo $a[$y][$x].' ';
					$room_id = $c[$x][$y]-1;
					$room_list[$room_id]['x']=$x;
					$room_list[$room_id]['y']=$y;
				}
			}
		}
		ksort($room_list);
		//list里只保留坐标
		//$room_list = array_keys(array_flip($room_list));
		return $room_list;
	}

	function change_coorlist_to_coorarr($coorlist)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		for($i=0;$i<sizeof($coorlist);$i++){
			$x = $coorlist[$i]['x'];
			$y = $coorlist[$i]['y'];
			$max_x = max($max_x,$x);
			$max_y = max($max_y,$y);
			$coorarr[$x][$y]=$i;
		}
		$coorarr['max_x'] = $max_x;
		$coorarr['max_y'] = $max_y;
		return $coorarr;
	}

	//下面是区域格生成的相关函数
	
	function generate_room($c, $x, $y, $r, $rm, $xm, $ym, $px, $py, $dr=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));
		// 增加房间号
		$c[$x][$y] = $r;
		//echo "此次生成了房间({$x},{$y})。这是我们的第{$r}间房，";		
		if($r>2 && (rand(0,100)<65))
		{
			//echo "一条岔路生成了，我们的出发点从({$x},{$y})变更为({$px},{$py})，";
			$x = $px;
			$y = $py;
			$odr = $dr;
			do {
				$dr = rand(0,3);
			  } while ($dr == $odr);
		}		
		if($r < $rm)
		{
			$r = $r + 1;
			if(!isset($dr)) $dr = rand(0,3);//随机挑选一个方向前进 0 x+ 1 x- 2 y+ 3 y-		
			//echo "接下来要向【{$dr}】方向移动。<br>";			
			$next_coord = generate_coord($c, $x, $y, $xm, $ym, $dr); 	
			$c = generate_room($c, $next_coord[0], $next_coord[1], $r, $rm, $xm, $ym, $next_coord[2], $next_coord[3], $next_coord[4]);     
		}
		return $c;
	}

	function generate_coord($c, $x, $y, $xm, $ym, $dr=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));
		//保留原始xy
		$px = $x;
		$py = $y;			
		//x变y不变
		if($dr === 0 || $dr === 1)
		{
			if($x === 0)
			{
				$dr = rand(2,3);
				$x = 1;
			} elseif($x === $xm)
			{
				$dr = rand(2,3);
				$x = $xm - 1;
			} else {
				if($dr === 1)
				{
					$x = $x - 1;
					//$dr++;
					//echo "向x-移动了一次，坐标是({$x},{$y})<br>";
				} else
				{
					$x = $x + 1;
					//$dr++;
					//echo "向x+移动了一次，坐标是({$x},{$y})<br>";
				}
			}
		} 
		//y变x不变
		else
		{
			if($y === 0)
			{
				$dr = rand(0,1);
				$y = 1;
			} elseif($y === $ym)
			{
				$dr = rand(0,1);
				$y = $ym - 1;
			} else {
				if($dr === 3)
				{
					$y = $y - 1;
					//$dr = 0;
					//echo "向y-移动了一次，坐标是({$x},{$y})<br>";
				} else
				{
					$y = $y + 1;
					//$dr++;
					//echo "向y+移动了一次，坐标是({$x},{$y})<br>";
				}
			}
		}
		$room = $c[$x][$y];
		// 我们有家了
		if($room > 0)
		{
			//用原始坐标重复运行 但是改变方向
			//↑↑不，还是用原来的方法 不改变方向 让它沿着一条路自己走到能创建新房间的地方
			return generate_coord($c, $x, $y, $xm, $ym, $dr);
		}
		return [$x, $y, $px, $py, $dr];
	}	

	function get_mapzone_room_size($intensity=99)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//默认的全局地图边界 10x10
		$x_axis = 10;
		$y_axis = 10;
		//默认的地图内有效格子数
		$rooms_max = 7;
		switch($intensity)
		{
			case 0: //无月之影可能比较大
				$room_size['x_axis'] = 40;
				$room_size['y_axis'] = 40;
				$room_size['rooms_max'] = 16;
				break;
			case 1:
				$room_size['x_axis'] = ceil($x_axis*1.5);
				$room_size['y_axis'] = ceil($x_axis*1.5);
				$room_size['rooms_max'] = ceil($rooms_max*1.2);
				break;
			case 2:
				$room_size['x_axis'] = ceil($x_axis*2);
				$room_size['y_axis'] = ceil($x_axis*2);
				$room_size['rooms_max'] = ceil($rooms_max*1.45);
				break;
			case 3:
				$room_size['x_axis'] = ceil($x_axis*2.5);
				$room_size['y_axis'] = ceil($x_axis*2.5);
				$room_size['rooms_max'] = ceil($rooms_max*1.65);
				break;
			case 4:
				$room_size['x_axis'] = ceil($x_axis*3);
				$room_size['y_axis'] = ceil($x_axis*3);
				$room_size['rooms_max'] = ceil($rooms_max*1.9);
				break;
			case 34: //英灵殿只有2个房间
				$room_size['x_axis'] = 5;
				$room_size['y_axis'] = 5;
				$room_size['rooms_max'] = 2;
				break;
			default:
				$room_size['x_axis'] = $x_axis;
				$room_size['y_axis'] = $y_axis;
				$room_size['rooms_max'] = $rooms_max;
				break;
		}
		return $room_size;
	}

	function get_mapzone_coordinates($room_size)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$x_array = array_fill(0, $room_size['x_axis'], 0);
		$y_array = array_fill(0, $room_size['y_axis'], 0);
		$coordinates = [];
		foreach($x_array as $x => $v)
		{
			$coordinates[$x] = $y_array;
		}
		return $coordinates;
	}
	
	function get_next_mapinfo($p)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		$next = array_search($p,$arealist)+1;
		if(isset($arealist[$next]))
		{
			return $arealist[$next];
		}
		return 'end';
	}

	function get_prev_mapinfo($p)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		$prev = array_search($p,$arealist)-1;
		if(isset($arealist[$prev]))
		{
			return $arealist[$prev];
		}
		return 'end';
	}

	function get_mapzoneinfo($p,$z){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','c_mapzone'));

		$mpt=$mapzone_coorlist[$p][$z]['t'];
		if($mpt)
		{
			$mpt = explode('-',$mpt);
			$info = '<span class="yellow b">'.$mapzoneinfo[$mpt[0]].'</span>';
		}
		else
		{
			if($z == 0)
			{
				$info = isset($mapzoneinfo['start'][$pls]) ? "<span class='yellow b'>{$mapzoneinfo['start'][$pls]}</span>" : '<span class="yellow b">入口</span>';
			}
			elseif($z == $mapzone_end[$p])
			{
				$info = isset($mapzoneinfo['end'][$pls]) ? "<span class='yellow b'>{$mapzoneinfo['end'][$pls]}</span>" : '<span class="yellow b">出口</span>';
			}
			else
			{
				$info = '区域'.$z;
			}
			
		}
		return $info;
	}
}

?>