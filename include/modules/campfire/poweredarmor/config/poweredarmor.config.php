<?php

namespace poweredarmor
{
	//蠢
	$nosta = '∞';
	//最多能抵消多少百分比的伤害
	$max_pa_reduce_dmg_per = 99;
	//每件动力装甲带来的减伤比例
	$once_pa_reduce_dmg_per = Array(
		'T' => 25,
		'S' => 22,
		'A' => 17,
		'B' => 12,
		'C' => 5,
		'O' => 3,
	);
	//每点耐久可以抵消多少点伤害
	$once_pas_reduce_dmg  = Array(
		'T' => 10,
		'S' => 8,
		'A' => 6,
		'B' => 4,
		'C' => 3,
		'O' => 1,
	);
	//身体装甲可以降低多少倍的耐久消耗
	$bpa_reduce_pas_cost_per = Array(
		'T' => 3,
		'S' => 2.5,
		'A' => 2,
		'B' => 1.75,
		'C' => 1.5,
		'O' => 1.25,
	);
	//头部装甲，作战/强袭/偷袭时增加先攻率
	$hpa_add_acitve_obbs = Array(
		'T' => 1.3,
		'S' => 1.2,
		'A' => 1.15,
		'B' => 1.1,
		'C' => 1.05,
		'O' => 1,
	);
	//头部装甲，作战/强袭/偷袭时增加遇敌率
	$hpa_add_metman_obbs = Array(
		'T' => 1.4,
		'S' => 1.3,
		'A' => 1.25,
		'B' => 1.15,
		'C' => 1.1,
		'O' => 1,
	);
	//头部装甲，探索时增加道具发现率
	$hpa_add_metman_obbs = Array(
		'T' => 1.4,
		'S' => 1.3,
		'A' => 1.25,
		'B' => 1.15,
		'C' => 1.1,
		'O' => 1,
	);
	//手部装甲，增加命中率
	$apa_add_hitrate_obbs = Array(
		'T' => 2,
		'S' => 1.8,
		'A' => 1.7,
		'B' => 1.5,
		'C' => 1.3,
		'O' => 1.1,
	);
	//腿部装甲，降低移动时的体力消耗
	$fpa_reduce_move_cost = Array(
		'T' => 11,
		'S' => 10,
		'A' => 8,
		'B' => 6,
		'C' => 5,
		'O' => 3,
	);
	//腿部装甲，降低探索时的体力消耗
	$fpa_reduce_explore_cost = Array(
		'T' => 11,
		'S' => 10,
		'A' => 8,
		'B' => 6,
		'C' => 5,
		'O' => 3,
	);
}

?>
