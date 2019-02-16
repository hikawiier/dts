<?php

namespace areafeatures_etconsole
{
	//areafeatures_etconsole - 特定地图提供的特殊功能位置
	//一次释放几种NPC（从0开始数）
	$extract_times = 2;
	//可释放的NPC种类
	$extract_npc = Array(
		0 => Array(
			'type' => 1001, //NPC种类
			'sub' => Array //NPC的小类与每类生成的数量
			(
				0 => 3,
				1 => 3,
				2 => 3,
			),
		),
		1 => Array(
			'type' => 1002, 
			'sub' => Array //NPC的小类与每类生成的数量
			(
				0 => 1,
				1 => 1,
				2 => 1,
				3 => 1,
			),
		),
	);
}

?>
