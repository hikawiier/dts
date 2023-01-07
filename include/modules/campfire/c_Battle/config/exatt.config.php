<?php

namespace c_battle
{
	//属性异常等级：
	//写给自己：用异常状态为玩法增加变化 而不是制造痛苦
	$ex_inf_lvl_arr = Array
	(
		//烧伤
		'u' => Array
		(
			'lvl' => 0,//初始等级
			'max' => 4,//强制等级上限
			'source_lvl' => Array //特定来源施加的默认异常等级与等级上限 顺序：异常加深 → 达到上限后延长异常持续时间1轮
			(
				'u' => Array(0,2),//火焰属性攻击 默认0级 上限2级
				'f' => Array(3,4),//灼焰属性攻击 默认3级 上限4级
				'weather16' => Array(2,4),//暴露在臭氧洞下 默认2级 上限4级
				'event21' => Array(0,1),//被圣G的地图事件环形激光击中 默认0级 上限1级
				'event33' => Array(1,4),//被雏菊地图事件击中 默认1级 上限4级
			),
			//'source_max_events' => Array(), //单独来源施加异常等级达到上限后执行的事件
			'last_turns' => 4,//该异常在初次获得时默认的持续时间（单位：战斗轮）（战斗轮：战斗轮步进或探索/移动时-1）
			'source_lastturns' => Array //特定来源施加的异常在初次获得时默认的持续时间
			(
				'u' => 4,//火焰属性攻击
				'f' => 3,//灼焰属性攻击
				'weather16' => 3,//暴露在臭氧洞下
				'event21' => 4,//被圣G的地图事件环形激光击中
				'event33' =>5,//被雏菊地图事件击中
			),
			'infectious' => Array('u','u','u','f','f'),//每等级下该异常的【传染性】 存在的话攻击时会视为带有对应的属性
			'ex_dmg_punish' => Array(1,1.1,1.15,1.3,1.5),//每等级下该异常对【属性伤害】的影响
			//DOT伤害相关 如果不会造成DOT伤害不需要这些
			'dot_dmg' => Array(13,27,44,71,103),//没有来源的情况下 每等级下该异常基础的固定DOT伤害
			'dot_r_dmg' => Array(0,0,0.0125,0.0175,0.0225),//没有来源的情况下 每等级下该异常基础的百分比DOT伤害
			'dot_up_fluc' => Array(0,5,10,10,15),//每等级下该异常的DOT伤害向上浮动范围 单位百分比
			'dot_down_fluc' => Array(30,25,15,10,10),//每等级下该异常的DOT伤害向下浮动范围 单位百分比
			//'hitrate_punish' => Array(),//每等级下该异常对【攻击命中率】的影响 如果没有不需要这一条
			//'att_punish' => Array(),//每等级下该异常对【攻击力】的影响 如果没有不需要这一条
			//'def_punish' => Array(),//每等级下该异常对【防御力】的影响 如果没有不需要这一条
			//'att_r_punish' => Array(),//每等级下该异常对【最终攻击修正】的影响 如果没有不需要这一条
			//'phy_def_punish' => Array(),//每等级下该异常对【物理护甲】的影响 如果没有不需要这一条
			'ex_def_punish' => Array(0,-5,-5,-15,-15),//每等级下该异常对【属性抗性】的影响 如果没有不需要这一条
			//'active_r_punish' => Array(),//每等级下该异常对【先攻率】的影响 如果没有不需要这一条
			'counter_r_punish' => Array(1,1.1,1.1,1.15,1.2),//每等级下该异常对【反击率】的影响 如果没有不需要这一条
			//'escape_r_punish' => Array(),//每等级下该异常对【逃跑成功率】的影响 如果没有不需要这一条
			//'hp_punish' => Array(),//每等级下该异常对【生命变化】的影响 如果没有不需要这一条
			//'sp_punish' => Array(),//每等级下该异常对【体力变化】的影响 如果没有不需要这一条
			'rage_punish' => Array(1,2,4,7,10),//每等级下该异常对【怒气变化】的影响 如果没有不需要这一条
		),
		//中毒
		'p' => Array
		(
			'lvl' => 0,
			'max' => 4,
			'source_lvl' => Array
			(
				'p' => Array(0,2),//毒性属性攻击
				'itemPB' => Array(0,0),//吃到有毒食物
				'itemPB2' => Array(1,2),//吃到猛毒食物
				'weather10' => Array(0,2),//暴露在瘴气下
				'weather15' => Array(0,3),//暴露在辐射尘下
			),
			'last_turns' => 3,
			'source_lastturns' => Array
			(
				'p' => 4,//毒性属性攻击
				'itemPB' => 3,//吃到有毒食物
				'itemPB2' => 5,//吃到猛毒食物
				'weather10' => 5,//暴露在瘴气下
				'weather15' => 7,//暴露在辐射尘下
			),
			'infectious' => Array('p','p','p','p','p'),
			'ex_dmg_punish' => Array(1,1.1,1.15,1.3,1.5),
			'dot_dmg' => Array(0,0,23,44,77),
			'dot_r_dmg' => Array(0.0075,0.0105,0.0115,0.0165,0.025),
			'dot_up_fluc' => Array(0,5,10,10,15),
			'dot_down_fluc' => Array(30,25,15,10,10),
			'ex_def_punish' => Array(0,-5,-5,-15,-15),
			'counter_r_punish' => Array(1,1.1,1.1,1.15,1.2),
			'rage_punish' => Array(1,2,4,7,10),
		),
		//冻结
		'i' => Array
		(
			'lvl' => 0,
			'max' => 4,
			'source_lvl' => Array
			(
				'i' => Array(0,2),//冻气属性攻击
				'k' => Array(3,4),//冰华属性攻击
				'weather7' => Array(0,0),//暴露在雪天
				'weather12' => Array(0,2),//暴露在暴风雪下
				'weather13' => Array(1,3),//暴露在冰雹下
			),
			'last_turns' => 5,
			'source_lastturns' => Array
			(
				'i' => 5,//冻气属性攻击
				'k' => 7,//冰华属性攻击
				'weather7' => 3,//暴露在雪天
				'weather12' => 4,//暴露在暴风雪下
				'weather13' => 4,//暴露在冰雹下
			),
			'infectious' => Array('i','i','i','k','k'),
		),
		//麻痹
		'e' => Array
		(
			'lvl' => 0,
			'max' => 4,
			'source_lvl' => Array
			(
				'e' => Array(0,2),//电气属性攻击
				'weather6' => Array(0,0),//在雷雨天被雷劈了……
				'weather14' => Array(1,3),//暴露在离子暴中
			),
			'last_turns' => 3,
			'source_lastturns' => Array
			(
				'e' => 3,//电气属性攻击
				'weather6' => 2,//在雷雨天被雷劈了……
				'weather14' => 4,//暴露在离子暴中
			),
			'infectious' => Array('e','e','e','e','e'),
		),
		//混乱
		'w' => Array
		(
			'lvl' => 0,
			'max' => 4,
			'source_lvl' => Array
			(
				'w' => Array(0,2),//音波属性攻击
				't' => Array(3,4),//音爆属性攻击
				'weather8' => Array(0,0),
				'weather9' => Array(0,0),//在雾天行动可能会混乱
				'weather14' => Array(1,3),//暴露在离子暴中
			),
			'last_turns' => 2,
			'source_lastturns' => Array
			(
				'w' => 2,//音波属性攻击
				't' => 2,//音爆属性攻击
				'weather8' => 3,
				'weather9' => 3,//在雾天行动可能会混乱
				'weather14' => 4,//暴露在离子暴中
			),
			'infectious' => Array('w','w','w','t','t'),
		),
	);
}

?>
