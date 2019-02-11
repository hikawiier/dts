<?php

namespace campfire_itemmix
{	
	function init() 
	{
		eval(import_module('itemmix'));
		//为了便于维护，将MOD增加的合成列表移至$mixinfo外。期待有更独立的解决方法。
		//解决办法来了//
		//这种解决方案在游戏内可以完成合成，但帮助列表的自动生成对此表示无能为力，我只能说：四面出来挨打
		//篝火-回复道具
		$mixinfo[]=array('class' => 'h', 'stuff' => array('压缩饼干','水'),'result' => array('应急储备粮','HB',95,4,),);
		$mixinfo[]=array('class' => 'h', 'stuff' => array('压缩饼干','蒸馏水'),'result' => array('健康储备粮','HB',150,4,),);
		$mixinfo[]=array('class' => 'h', 'stuff' => array('食堂的盒饭','打火机'),'result' => array('迷之黑暗物质','PR',100,5,),);
		//篝火-钝器：
		$mixinfo[]=array('class' => 'wp', 'stuff' => array('每只虾360元的炸虾饭','黑色方块','白色方块','冰棍棒'),'result' => array('★悔悟之棒★','WP',1514,'∞','Znrk'),);
		$mixinfo[]=array('class' => 'hidden', 'stuff' => array('每只虾360元的炸虾饭','黑色方块','白色方块','冰钉棍棒'),'result' => array('★悔悟之棒★','WP',1514,'∞','Znrk'),);
		//篝火-爆系武器：
		$mixinfo[]=array('class' => 'wd', 'stuff' => array('★BIUBIUBIU★','悲叹之种','蒸馏水'),'result' => array('★冰枪术★','WD',666,'∞','ZnkNdr'),);
		//篝火-道具：
		$mixinfo[]=array('class' => 'item', 'stuff' => array('打火机','地雷'),'result' => array('土制二踢脚（？）','EW',1,1,0),);
		//篝火-方块系：
		$mixinfo[]=array('class' => 'cube', 'stuff' => array('黑色方块','白色方块','怨灵'),'result' => array('★无极★','WP',114,514,'Nrd'),);
		$mixinfo[]=array('class' => 'cube', 'stuff' => array('黑色方块','白色方块','幽灵'),'result' => array('★无极★','WP',114,514,'Nrd'),);
	}
}

?>