<?php

namespace ex_dmg_att
{
	//属性异常登陆 杀了我吧
	$ex_inf_list = Array('u','i','e','w','p');
	$ex_skillid_list = Array(5,6,7,8,9);

	//属性异常的传染性描述
	$ex_inf_infectious_info = Array
	(
		'u' => '<span class="red b">火焰</span>',
		'f' => '<span class="red b">炽热之焰</span>',
		'i' => '<span class="cyan b">冻气</span>',
		'k' => '<span class="cyan b">凝结之息</span>',
		'e' => '<span class="yellow b">电流</span>',
		'p' => '<span class="purple b">毒雾</span>',
		'w' => '<span class="grey b">不协调感</span>',
		't' => '<span class="seagreen b">轰鸣音浪</span>',
	);

	//属性攻击登录
	$ex_attack_list = Array('p', 'u', 'i', 'd', 'e','w','f','k','t');
	
	//属性攻击名称
	$exdmgname = Array('p' => '毒性攻击', 'u' => '火焰燃烧', 'i'=>'冻气缠绕', 'd'=>'爆炸','e'=>'电击','w'=>'音波攻击','f' => '<span class="yellow b">炽热之焰</span>','k' => '<span class="cyan b">凝结之息</span>', 't' => '<span class="seagreen">音爆冲击</span>');
	
	//各种属性攻击的得意武器（ 伤害2倍）
	$ex_good_wep = Array('p' => 'K','u' => 'G','i'=> 'C','d' => 'D', 'e' => 'P', 'w' => 'D');
	
	//各种属性攻击的得意社团（致伤命中率+20）
	//$ex_good_club = Array('p' => 8,'e' => 7);
	
	//各种属性攻击的基础伤害
	$ex_base_dmg = Array('p' => 15, 'u' => 25, 'i'=> 10, 'd'=> 1,'e' => 15,'w' => 20,'f' => 5,'k' => 5,'t' => 5,);
	
	//各种属性攻击的最大伤害值，游戏中，属性攻击伤害不会超过这个参数，0则不限制伤害
	$ex_max_dmg = Array('p' => 90, 'u' => 120, 'i'=> 80, 'd'=> 0,'e' => 100,'w' => 110,'f' => 0,'k' => 0,'t' => 0);
	
	//各种属性攻击的伤害武器参数，越小伤害上升越快
	$ex_wep_dmg = Array('p' => 10, 'u' => 5, 'i'=> 12, 'd'=> 2,'e' => 10,'w' => 12,'f' => 4,'k' => 5,'t' => 5);
	
	//各种属性攻击的伤害熟练参数，越小伤害上升越快
	$ex_skill_dmg = Array('p' => 15, 'u' => 20, 'i'=> 20 ,'d'=> 500,'e' => 20,'w' => 15,'f' => 40,'k' => 30,'t' => 45);
	
	//各种属性攻击的伤害浮动范围
	$ex_dmg_fluc = Array('p' => 15, 'u' => 30, 'i'=> 10,'d'=> 20,'e' => 5,'w' => 15,'f' => 30,'k' => 10,'t' => 45);
	
	//属性攻击导致的异常状态对应技能编号
	$ex_inf = Array('p' => 5, 'u' => 6, 'i'=> 7, 'e' => 8, 'w' => 9, 'f' => 6, 'k' => 7, 't' => 9);
	
	//已经进入异常状态对属性攻击伤害的影响
	$ex_inf_punish = Array('p' => 2, 'u' => 1, 'i'=> 0.75, 'd' => 1, 'e' => 1, 'w'=>1.5, 'f' => 1.5, 'k' => 1.5, 't'=> 2);
	
	//各种属性攻击的初始致异常状态率
	$ex_inf_r = Array('p' => 5, 'u' => 10, 'i'=> 5,'e' => 5,'w'=> 5,'f' => 25,'k' => 40,'t'=> 20);
	
	//各种属性攻击的最高致异常状态率
	$ex_max_inf_r = Array('p' => 60, 'u' => 40, 'i'=> 50,'e' => 50,'w'=> 40,'f' => 70,'k' => 80,'t'=> 60);
	
	//熟练度对致异常状态率的影响
	$ex_skill_inf_r = Array('p' => 0.1, 'u' => 0.05, 'i'=> 0.08,'e' => 0.08,'w' => 0.1, 'f' => 0.1, 'k' => 0.1, 't'=> 0.08);
	
}

?>
