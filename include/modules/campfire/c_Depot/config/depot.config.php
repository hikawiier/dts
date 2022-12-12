<?php

namespace c_depot
{
	//areafeatures_depot - 个人仓库最多可以储存的道具数量
	$max_saveitem_num = 20;
	//储存每件道具的手续费
	$saveitem_cost = 20;
	$loaditem_cost = 220;
	//游戏开场时NPC存在仓库里的道具
	$npc_depot = Array
	(
		0 => Array(
			'name' => '希塔丽尔·雅思奈落',
			'type' => 98,
			'itm' => Array
			(
				0 => Array
				(
					'itm' => '遗梦',
					'itmk' => 'kgrt',
					'itme' => 1,
					'itms' => 1,
					'itmsk' => '',
				),
			),
		),
	);
}

?>
