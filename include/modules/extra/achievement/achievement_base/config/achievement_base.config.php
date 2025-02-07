<?php
namespace achievement_base{
	
	//成就总列表，按先后顺序
	//过期的成就会放到最后
	$achtype=array(
		31=>'2017十一活动',
		32=>'2017万圣节活动',
		33=>'2018春节活动',
		34=>'2018愚人节活动',
		20=>'日常任务',
		10=>'结局成就',
		3=>'战斗成就',
		1=>'道具成就',
		4=>'特殊挑战',
		2=>'竞速挑战',
		5=>'终生成就',
		//0=>'其他成就',
	);
	
	//生效中的所有成就
	$achlist=array(//为了方便调整各成就的显示顺序放在这里了
		1=>array(300,302,303,304,358,353,354,357,355),
		2=>array(308,309,322,323,359),
		3=>array(310,311,312,347,348,356),
		4=>array(325,313,326,367,351,352,368,369),
		5=>array(363,364),
		10=>array(305,301,306,307,350),
		20=>array(314,315,316,317,318,319,320,321,324,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,349),
		31=>array(327,328,329),
		32=>array(330,331),
		33=>array(360,361,362),
		34=>array(365,366),
	);
	
	//成就起止时间，如果设置，则只认非零的数据
	$ach_available_period=array(
		31=>array(1506816000, 1508111999),
		32=>array(1509465600, 1510012799),
		33=>array(1518739200, 1521935999),
		34=>array(1522540800, 1524441599)
	);
	
	//成就编号=>允许完成的模式，未定义则用0键的数据
	$ach_allow_mode=array(
		0=>array(0, 4, 18, 19),//默认标准模式、卡片模式、荣耀模式、极速模式可以完成
		307 => array(0, 4, 18, 16),//解离成就
		308 => array(0, 4),//5分钟KEY弹，只允许标准、卡片完成
		309 => array(0, 4),//15分钟贤者，只允许标准、卡片完成
		318 => array(0, 4, 18, 16),//日常解离成就
		323 => array(0, 4, 16),//最速解离成就
		313 => array(15),//伐木模式成就
		322 => array(0, 4),//30分钟死斗成就，只允许标准、卡片完成
		327 => array(18),//2017年十一活动，荣耀房NPC成就
		328 => array(18),//2017年十一活动，荣耀房杀玩家成就
		329 => array(18),//2017年十一活动，荣耀房破灭之诗成就
		330 => array(0, 4, 19),//2017万圣节活动成就1，允许极速房完成
		331 => array(0, 4, 19),//2017万圣节活动成就2，允许极速房完成
		348 => array(4, 18, 19),//杀高级卡成就
		349 => array(0, 4, 18),//危险地区杀玩家成就
		350 => array(19),//极速模式解禁胜利
		351 => array(1),//除错模式
		352 => array(1),//除错模式
		358 => array(0, 4),//合成方块系道具，由于掉落表不同，只允许标准、卡片完成
		359 => array(19),//极速模式10分钟胜利
		361 => array(0, 4),//2018年春节活动2
		365 => array(0, 4),//2018年愚人节活动
		366 => array(0, 4),//2018年愚人节活动
		//367 => array(0, 4),//破解
		368 => array(0, 4, 18),//清空NPC成就
	);
	
	//日常成就间隔时间。一天四次真的是“日常”成就？
	$daily_intv = 21600;
	
	//日常成就类型列表，这里影响能够获得的日常成就。只有这里和$achlist都定义的日常成就才是有效的
	$daily_type = array(
		//类别1，自我挑战，升级之类的
		1=>array(324,332,333,334,335,336,337,338),
		//类别2，PVE战斗成就，杀特定NPC之类的
		2=>array(314,316,321,339,340,341,342,343),
		//类别3，挑战类成就，包括杀玩家、解禁解离
		3=>array(315,318,344,345,346,349)
	);
	
	//成就页面每行几个成就窗格
	$ach_show_num_per_row = 3;
	
	/*//campfire MOD修改标识
	$achtype[98] = '篝火模组包成就';
	$achlist[98] = Array(1800,1801);
	$ach_allow_mode[1801] = array(98);*/
}
?>