<?php

namespace c_mapzone
{
	// 第一个房间的固定位置
	$start_x = 2;
    $start_y = 2;
	// 初始化第一次拓展房间的方向 0=x+;1=x-;2=y+;3=y-;
	$dr = 0;
	// 初始化岔路坐标
	$px = 0;
	$py = 0;
	//不会生成特殊房间的地图
	$not_spawn_special_roomlist = Array(34);
	
	$mapzonelist = Array(
		0 => Array
		(
			'space' => 12, //地图大小（自定义格数，最大25格）
			'weather' => 0, //默认天气
			'exposed' => 0, //暴露度上限修正 0=默认
			'elements' => Array
			( 
				//包含哪些特殊格 
				//loot（补给站-回复道具多）armor（武库 装备多） mini-boss(特殊敌人刷新点) shop（商店）function（杂项）
				//后面数字代表会生成在第几格（最低不能为0，最高不能为地图格子数量，这两个格子是出入口）
				'loot' => 4,
				'mini-boss' => 5,
				'shop' => 6,
			),
		),
		1 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		2 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		3 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		4 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		5 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		6 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		7 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		8 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		9 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		10 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		11 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		12 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		13 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		14 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		15 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		16 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		17 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		18 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		19 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		20 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		21 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		22 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		23 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		24 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		25 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		26 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		27 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		28 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		29 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		30 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		31 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		32 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		33 => Array
		(
			'space' => 9, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
		34 => Array
		(
			'space' => 2, 
			'weather' => 0,
			'exposed' => 0, 
			'elements' => Array
			( 
			),
		),
	);

	$mapzoneinfo = array
	(//标记一些特殊房间的名称，没有的话编号0默认叫【入口】，编号末默认叫【出口】，其他默认叫【区域】
		'shop' => '商店',
		'armory' => '武库',
		'supply' => '补给点',
		'workbench' => '工作间',
		'hospital' => '诊所',
		'miniboss' => '警卫室',
		'start' => Array(34=>'遗址',),
		'end' => Array(0=>'总控室',34=>'出口',),
	);

}

?>
