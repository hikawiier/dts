<?php

namespace c_mapzone
{	
	function init() 
	{
		global $mapzone_coorlist,$mapzone_coorarr,$mapzone_pfloor,$mapzone_end,$mapzone_weather,$mapzone_exposed,$mapzone_vars;
		$mapzonedata = NULL;
		eval(import_module('sys','map'));
		//鱼唇的可视化……
		if($gamevars['genzone'])
		{
			for($p=1;$p<=$areamax;$p++)
			{
				$result = $db->query("SELECT * FROM {$tablepre}mapzone WHERE pfloor='$p'");
				if ($db->num_rows($result))
				{
					$mapzonedata = $db->fetch_array($result);
					$mapzone_pls = $mapzonedata['pls'];
					$mapzone_pfloor[$mapzone_pls] = $p;
					$mapzone_end[$mapzone_pls] = $mapzonedata['zoneend'];
					$mapzone_weather[$mapzone_pls] = $mapzonedata['weather'];
					$mapzone_exposed[$mapzone_pls] = $mapzonedata['exposed'];
					$mapzone_vars[$mapzone_pls] = $mapzonedata['zonevars'];
					//杀了我把
					//留两个数组：一个是房间号=>坐标 一个是坐标=>房间号 为什么会这样？我不到啊！
					$coorlist = $mapzonedata['zonelist'];
					$coorlist = explode(',',$coorlist);
					for($i=0;$i<sizeof($coorlist);$i++){
						list($x, $y) = explode('-',$coorlist[$i]);
						//生成地图的时候怎么不这么写……？神经病啊
						$mapzone_coorlist[$mapzone_pls][$i]['x'] = $x;
						$mapzone_coorlist[$mapzone_pls][$i]['y'] = $y;
						$max_x = max($max_x,$x);
						$max_y = max($max_y,$y);
						$mapzone_coorarr[$mapzone_pls][$x][$y]=$i;
					}
					$mapzone_coorarr[$mapzone_pls]['max_x'] = $max_x;
					$mapzone_coorarr[$mapzone_pls]['max_y'] = $max_y;
				}
			}
			$gamevars['genzone'] = NULL;
		}
		//这样搞真的能行吗？？？？		
	}

	function update_mapzonedata($p,$znew,$kind=0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','c_mapzone'));
		switch($kind){
			case 'exposed':
				$db->query("UPDATE {$tablepre}mapzone SET exposed='$znew' WHERE pls='$p'");
				break;
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
			//传一个标记告诉game表我好了
			$gamevars['genzone'] = 1;
			save_gameinfo();
		}
	}
	
	function rs_mapzone(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','weather','c_mapzone'));
		//开始初始化区域表
		foreach($arealist as $p)
		{
			$mapzone_id = $p; //地图编号
			$mapzone_weather = $mapzonelist[$mapzone_id]['weather'];	
			$rooms_max = $mapzonelist[$mapzone_id]['space'];
			$room_end = $rooms_max-1; 
			$mapzone_list = get_roomlist_arr($coordinates, $start_x, $start_y, 1, $rooms_max, $x_max, $y_max, $dr, $px, $py);
			$db->query("INSERT INTO {$tablepre}mapzone (pls, weather, zoneend, zonelist) VALUES ('$mapzone_id', '$mapzone_weather', '$room_end', '$mapzone_list')");
		}
	}
	
	function get_roomlist_arr($c, $x, $y, $r, $rm, $xm, $ym, $px, $p)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//开始生成随机区域格
		$room_arr = generate_room($c, $x, $y, $r, $rm, $xm, $ym, $px, $p);
		//整理生成后的随机区域格鼠族
		return encode_roomlist($room_arr,$xm, $ym);
	}
	
	function encode_roomlist($c,$x_max,$y_max)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		//整理生成的随机区域格
		$room_list = Array();
		for($y=0;$y<=$y_max;$y++)
		{
			//echo '<br>';
			for($x=0;$x<=$x_max;$x++)
			{
				if($c[$y][$x]>0){
					//echo $a[$y][$x].' ';
					$room_id = $c[$y][$x];
					$room_coor = $x.'-'.$y;
					$room_list[$room_id]=$room_coor;
				}
			}
		}
		ksort($room_list);
		//list里只保留坐标
		$room_list = array_keys(array_flip($room_list));
		return implode(',',$room_list);
	}

	function get_roomlist_data($p)
	{
		//拉取区域格表相关的数据
		//有什么办法能把拉取出来的区域格放在全局变量里？因为它一局下来多半也不会有变化了
		//哈哈有拌饭了
		if (eval(__MAGIC__)) return $___RET_VALUE;
		/*eval(import_module('sys'));
		$result = $db->query("SELECT * FROM {$tablepre}mapzone WHERE pls = '$pls'");
		$rarr = $db->fetch_array($result);
		$z = explode(',',$rarr['zonelist']);
		return $z;*/
	}

	//下面是区域格生成的相关函数
	
	function generate_room($c, $x, $y, $r, $rm, $xm, $ym, $px, $py)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));
		// 增加房间号
		$c[$y][$x] = $r;
		//echo "此次生成了房间({$x},{$y})。这是我们的第{$r}间房，";		
		if($r>2 && (rand(0,100)<65))
		{
			//echo "一条岔路生成了，我们的出发点从({$x},{$y})变更为({$px},{$py})，";
			$x = $px;
			$y = $py;
		}		
		if($r < $rm)
		{
			$r = $r + 1;
			$dr = rand(0,3);//随机挑选一个方向前进 0 x+ 1 x- 2 y+ 3 y-		
			//echo "接下来要向【{$dr}】方向移动。<br>";			
			$next_coord = generate_coord($c, $x, $y, $xm, $ym, $dr); 			
			$c = generate_room($c, $next_coord[0], $next_coord[1], $r, $rm, $xm, $ym, $next_coord[2], $next_coord[3]);     
		}
		return $c;
	}

	function generate_coord($c, $x, $y, $xm, $ym, $dr)
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
		$room = $c[$y][$x];
		// 我们有家了
		if($room > 0)
		{
			//用原始坐标重复运行 但是改变方向
			//↑↑不，还是用原来的方法 不改变方向 让它沿着一条路自己走到能创建新房间的地方
			return generate_coord($c, $x, $y, $xm, $ym, $dr);
		}
		return [$x, $y, $px, $py];
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
		if(isset($mapzoneinfo[$p][$z]))
		{
			$info = '<span class="yellow b">'.$mapzoneinfo[$p][$z].'</span>';
		}
		else
		{
			if($z == 0)
			{
				$info = '<span class="yellow b">入口</span>';
			}
			elseif($z == $mapzone_end[$p])
			{
				$info = '<span class="yellow b">出口</span>';
			}
			else
			{
				$info = '区域'.$z;
			}
			
		}
		return $info;
	}	

	/*function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger','c_mapzone'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		
		$chprocess($theitem);
	}*/
}

?>