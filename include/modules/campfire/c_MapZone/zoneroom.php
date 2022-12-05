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

	//生成特殊种类区域格
	/*先盘下逻辑：
	初始化地图区域格rs_mapzone()后
	开始生成列表中第0（英灵殿）和第1（随机）地图的特殊区域格rs_mapzone_room($p)
	之后，每次有玩家满足了地区压制条件时，再生成下一张地图的特殊区域格
	要生成特殊区域格，首先要满足区域格数＞2

	然后，根据地图所处层数$mapzonefloor[$p]，判断能生成哪些种类的区域格
	再然后，根据玩家满足地区压制条件时的状态，进一步判断能生成哪些种类的区域格
	再再然后，依据权重，对可生成的区域格进行排序，依次生成直到达到当前地图的特殊区域格上限

	最后，将生成了的特殊区域格，保存在$mapzone_vars[$p]里*/

	/*
	再理一下特殊格列表：
	0 = 'shop', 商店，每层都应该有，解锁‘商店’和‘仓库’
	1 = 'armory', 武库，从第1层开始，和补给点择一出现，道具池内会生成弹药、通常权重的武器/装备、攻击性技能书
	2 = 'supply', 补给点，从第1层开始，和武库择一出现，具池内会生成弹药、常见补给品、解毒剂、防御性技能书
	3 = 'workbench', 工作间，从第1层开始，只有单数层会出现，解锁‘强化’，道具池内只会生成武器/装备强化素材
	4 = 'hospital', 医院，从第2层开始，只有双数层会出现，解锁‘静养’，道具池内只会生成高效补给品、属性强化道具、异常解除道具
	5 = 'miniboss', 警卫室，从2层开始，根据玩家杀人数出现，出现精英敌人，道具池内会生成罕见权重的武器/装备、技能书
	99 = 'secret', 隐藏房，从第1层开始，每层都有，生成在和已有区域格不相邻的位置
	'start' = 'start', 入口，位于地图第0区域格
	'end' = 'end', 出口，位于地图最末区域格
	'crimson' = 'crimson', 主控室，固定位于无月之影终点。
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
		$sp_room_list = array();
		if(in_array($f,$not_spawn_special_roomlist))
		{ //搞特殊化的地图不生成特殊房间
			return $sp_room_list;
		}
		//每层都有商店
		$sp_room_list[]=spawn_mapzone_sproom_shop();
		//两种补给点择一出现
		$sp_room_list[]= rand(0,9)>4 ? spawn_mapzone_sproom_armory() : spawn_mapzone_sproom_supply();
		//单数层时生成工作台 双数层生成诊所
		$sp_room_list[]= fmod($f,2) ? spawn_mapzone_sproom_workbench() :spawn_mapzone_sproom_hospital();
		//小BOSS房
		$sp_room_list[]=spawn_mapzone_sproom_miniboss();
		return $sp_room_list;
	}

	function get_special_roomlist_arr($f,$coorlist,$room_end,$xm,$ym)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//echo '开始生产特殊区域<br>';
		//$sid = $room_end + 1; //不要新增房间 就在原有房间基础上修改
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
}

?>