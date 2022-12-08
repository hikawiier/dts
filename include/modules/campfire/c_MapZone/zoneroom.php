<?php

namespace c_mapzone
{	
	//地区压制
	/*先盘下逻辑：
	初始化地图区域格rs_mapzone()后
	第一次进入新地图时，初始化该地图压制条件rs_mapzone_clear_request($p)

	压制条件列表：
	0 = 'killcrowd', 满足击杀数
	1 = 'killvip', 满足特定击杀
	2 = 'usingitm', 使用特定道具
	3 = 'explorezone', 移动到过指定区域格
	*/

	//下面是区域格生成部分
	//决定地图危险度
	function get_mapzone_intensity($f,$mapzone_pls)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('map','c_mapzone'));
		if($f == 0 || $f == $areaend)
		{ //首尾层地图搞些特殊化
			return $mapzone_pls;
		}
		else
		{
			//决定地图所处的危险度区间 刨除首尾2张地图 每经过占总数1/5的地图升级一次强度
			$i = 100*($f/($areamax-2));
			if($i >= 80 && $f > 8)
			{	//有8张以上地图 且位置位于4/5路程往上 生成强度5
				return 5;
			}
			elseif($i >= 60 && $f > 6)
			{	//有6张以上地图 且位置位于3/5往上 生成强度4
				return 4;
			}
			elseif($i >= 40 && $f > 4)
			{	//有4张以上地图 且位置位于2/5往上 生成强度3
				return 3;
			}
			elseif($i >= 20 && $f > 2)
			{	//有2张以上地图 且位置位于1/5往上 生成强度2
				return 2;
			}
			else
			{	//生成默认的最低强度地图 为什么默认是99？你杀了我吧
				//头套都给你薅掉 现在默认强度是1
				return 1;
			}
		}
		return 1;
	}


	//生成地图大小
	function get_mapzone_room_size($intensity=1)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//默认的全局地图边界 10x10
		$x_axis = 10;
		$y_axis = 10;
		//默认的地图内有效格子数
		$rooms_max = 7;
		switch($intensity)
		{
			case 0: //0不是最低强度 是TMD无月之影 太会玩了！
				$room_size['x_axis'] = 40;
				$room_size['y_axis'] = 40;
				$room_size['rooms_max'] = 16;
				break;
			case 1:
				$room_size['x_axis'] = ceil($x_axis*1.2);
				$room_size['y_axis'] = ceil($y_axis*1.2);
				$room_size['rooms_max'] = ceil($rooms_max*1.1);
				break;
			case 2:
				$room_size['x_axis'] = ceil($x_axis*1.5);
				$room_size['y_axis'] = ceil($y_axis*1.5);
				$room_size['rooms_max'] = ceil($rooms_max*1.3);
				break;
			case 3:
				$room_size['x_axis'] = ceil($x_axis*2);
				$room_size['y_axis'] = ceil($y_axis*2);
				$room_size['rooms_max'] = ceil($rooms_max*1.5);
				break;
			case 4:
				$room_size['x_axis'] = ceil($x_axis*2.5);
				$room_size['y_axis'] = ceil($y_axis*2.5);
				$room_size['rooms_max'] = ceil($rooms_max*1.7);
				break;
			case 5:
				$room_size['x_axis'] = ceil($x_axis*3);
				$room_size['y_axis'] = ceil($y_axis*3);
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

	//初始化地图空坐标集
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

	function get_roomlist_arr($c, $x, $y, $r, $rm, $xm, $ym, $px, $p)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//开始生成随机区域格
		$room_arr = generate_room($c, $x, $y, $r, $rm, $xm, $ym, $px, $p);
		//整理生成后的随机区域格鼠族
		return change_coorarr_to_coorlist($room_arr,$xm,$ym);
	}
	
	//生成格
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

	//生成坐标
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

	//下面是生成特殊种类区域格部分
	/*
    特殊格列表：
	'shop' => '商店',每层都有，提供功能‘商店’、‘仓库’
	'armory' => '武库', 每层都有，但和补给点择一出现，道具池内会生成弹药、通常权重的武器/装备、攻击性技能书
	'supply' => '补给点', 每层都有，但和武库择一出现，具池内会生成弹药、常见补给品、解毒剂、防御性技能书
	'workbench' => '工作间', 只有单数层会出现，解锁‘强化’，道具池内只会生成武器/装备强化素材
	'hospital' => '医务室', 只有双数层会出现，解锁‘静养’，道具池内只会生成高效补给品、属性强化道具、异常解除道具
	'miniboss' => '警卫室', 每3层固定出现，如果当前地图没有也可以选择主动生成一个，出现精英敌人，道具池内会生成罕见权重的武器/装备、技能书
	'secret' => '隐藏房', 每层都有，但需要玩家自己生成
	'start' = '入口', 位于地图第0区域格
	'end' => '出口', 位于地图最末区域格
	'crimson' = 'crimson',主控室，固定位于无月之影终点。
	*/
	function spawn_mapzone_sproom_shop()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 'shop-0';
	}

	function spawn_mapzone_sproom_armory()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 'armory-0';
	}

	function spawn_mapzone_sproom_supply()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 'supply-0';
	}

	function spawn_mapzone_sproom_workbench()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 'workbench-0';
	}

	function spawn_mapzone_sproom_hospital()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 'hospital-0';
	}

	function spawn_mapzone_sproom_miniboss()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 'miniboss-0';
	}

	function spawn_mapzone_sproom_secret()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 'secret-0';
	}

	function spawn_special_roomlist($f)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('map'));
		//根据层数还有其他乱七八糟的东西获取能生成的特殊房间列表
		//搞特殊化的地图不生成特殊房间
		if(in_array($f,$map_nospeczone_arealist))
		{ 
			return $sp_room_list;
		}
		//每层都有商店
		$sp_room_list[]=spawn_mapzone_sproom_shop();
		//两种补给点择一出现
		$sp_room_list[]= rand(0,9)>4 ? spawn_mapzone_sproom_armory() : spawn_mapzone_sproom_supply();
		//单数层时生成工作台 双数层生成诊所
		$sp_room_list[]= fmod($f,2) ? spawn_mapzone_sproom_workbench() :spawn_mapzone_sproom_hospital();
		//小BOSS房 每更替一次强度生成一次
		if(fmod($f,2)==0)
		{
			$sp_room_list[]=spawn_mapzone_sproom_miniboss();
		}
		return $sp_room_list;
	}

	function get_special_roomlist_arr($f,$coorlist,$room_end,$xm,$ym)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//echo '开始生产特殊区域<br>';
		//$sid = $room_end + 1; //不要新增房间 就在原有房间基础上修改
		//不知道出于怎样的心态让你干出这种事……
		$coorlist[0]['t'] = 'start';
		$coorlist[$room_end]['t'] = 'end';
		$sp_room_list = spawn_special_roomlist($f);
		if(count($sp_room_list))
		{
			//第一遍循环 在只有一个邻接房间的房间附近生成
			for($i=1;$i<$room_end;$i++)
			{ //我草 怎么这里还$i<sizeof($coorlist) 这不无限循环了
				if(!count($sp_room_list))
				{
					return $coorlist;
				}
				$coorarr = change_coorlist_to_coorarr($coorlist);
				$x = $coorlist[$i]['x'];
				$y = $coorlist[$i]['y'];
				$dir = check_moveto_zone_dir($coorarr,$x,$y);
				$cdir = count($dir);
				if($cdir<2 && !$coorlist[$i]['t']) 
				{
					//echo '发现你了！美味的小孩！<br>';
					/*$odr = key($dir);
					do {
						$dr = rand(0,3);
						} while ($dr == $odr);
					$ncoord = generate_coord($coorarr, $x, $y, $xm, $ym, $dr); */
					$coorlist[$i]['t'] = array_shift($sp_room_list);
				}
			}
			//第一轮结束后如果还有未生成的特殊房间，再从尾巴开始第二轮循环
			for($i=$room_end-1;$i>1;$i--)
			{
				//echo '触发第二轮循环了<br>';
				if(!count($sp_room_list))
				{
					return $coorlist;
				}
				$coorarr = change_coorlist_to_coorarr($coorlist);
				$x = $coorlist[$i]['x'];
				$y = $coorlist[$i]['y'];
				$dir = check_moveto_zone_dir($coorarr,$x,$y);
				$cdir = count($dir);
				//这次从与两个房间相连的开始找
				if($cdir<3 && !$coorlist[$i]['t']) 
				{
					$coorlist[$i]['t'] = array_shift($sp_room_list);
				}
			}
			//还没变完？那随便找地方变吧
			for($i=$room_end-1;$i>1;$i--)
			{
				//echo '触发最后循环了<br>';
				if(!count($sp_room_list))
				{
					return $coorlist;
				}
				if(!$coorlist[$i]['t']) 
				{
					$coorlist[$i]['t'] = array_shift($sp_room_list);
				}
			}
		}
		return $coorlist;
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

	function change_coorlist_to_specarr($coorlist,$room_end)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$c=count($coorlist);
		//$specarr['start']=0;
		//$specarr['end']=$room_end;
		for($i=0;$i<=$room_end;$i++){
			if(isset($coorlist[$i]['t']))
			{
				$t = $coorlist[$i]['t'];
				$t = explode('-',$t);
				$specarr[$t[0]] = $i;
			}
		}
		return $specarr;
	}
}

?>