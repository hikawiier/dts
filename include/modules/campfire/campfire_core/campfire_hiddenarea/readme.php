<?php
namespace campfire_hiddenarea
{
	//请不要在moduel.list文件中载入本模块！！
	//由于技术原因，隐藏区域相关的内容，无法通过模块化实现
	//它们分散在以下模块中
	/*-----赋值部分-----*/
	function init()
	{
		//模块 core/map/map.config
		//campfireMOD新增地区，及隐藏地图设定
		//地区
		$plsinfo[96]='浮空之城 - 埃森莱尔';
		$plsinfo[97]='机械飞艇 - 梅洛特';
		$plsinfo[98]='古战场';
		//地图
		$xyinfo[96]='DX-VAzS';
		$xyinfo[97]='DA-VAzS';
		$xyinfo[98]='DE-VAzS';
		//地图描述
		$areainfo[98]="一望无垠的平原上，遍布着大小惊人的坑洼与断壁残垣。虽有野花在不远处盛开，却掩盖不住从这片死土中渗出荒凉寂寥之感……<BR>呆在这里只会让人感觉到不舒服，还是赶紧离开吧。<BR>";
		//全部隐藏地图列表（注意：在$plsinfo中，隐藏地图的序号一定要排在非隐藏地图后面，否则会出BUG，推荐使用从98开始的倒序，无效的地图不要写在列表里，否则也会出BUG） 
		//所谓的隐藏地图，实际上是不存在于arealist里的地图，所以不会被禁区和其他相关的东西
		//有关隐藏地图需要进行的修改：所有使用sizeof($plsinfo)的地方需要变成sizeof($plsinfo)-sizeof($hidden_arealist)；直接调用$arealist的地方，则需要在前面添加判定$arealist=array_diff($arealist,$hidden_arealist)
		$hidden_arealist = Array(96,97,98);
		//隐藏地图分组（这个功能的作用是，如果有些隐藏地图之间不能互相移动，那可以通过这个分组功能来规定可以互相移动的隐藏地图都有哪些）
		//即使只有一张地图，也请把它写进一个array里
		$hidden_areagroup = Array(
			'AncientLayers' => Array(96,97,98),
		);
		
		//模块 item/itemmain/itemmain.config【此处已添加进模组中，不需要重复添加】
		//模块 campfire/campfire_base/campfireitem/campfire_itemmain【此处已添加进模组中，不需要重复添加】
		//不会有物品掉落的地区列表
		$map_noitemdrop_arealist[] = 99;
		$map_noitemdrop_arealist[] = 98;
		$map_noitemdrop_arealist[] = 97;		
	}
	
	/*-----函数部分-----*/
	//模块 base/npc
	function get_safe_plslist($no_dangerous_zone = true, $type = 0){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		$ret = $chprocess($no_dangerous_zone, $type);
		//隐藏区域判定
		eval(import_module('map'));		
		$ret = array_diff($ret,$hidden_area);
		if($no_dangerous_zone && 1 == $type)
			$ret = array_diff($ret, array(21,26,33));
		return $ret;
	}
	
	//模块 base/event 
	function event()
	{
		if ($pls==34){//英灵殿
			if (($art!='Untainted Glory')&&($gamestate != 50)&&($gametype!=2)){
			$rpls=-1;
			//涉及到地区的随机内容要排除隐藏地区
			$plsinfo = array_flip(array_diff(array_flip($plsinfo),$hidden_arealist));
			while ($rpls<0 || $arealist[$rpls]==34){
				if($hack){$rpls = rand(0,sizeof($plsinfo)-1);}
				else {$rpls = rand($areanum+1,sizeof($plsinfo)-1);}
				} 
				$pls=$arealist[$rpls];
				$log.="殿堂的深处传来一个声音：<span class=\"evergreen b\">“你还没有进入这里的资格”。</span><br>一股未知的力量包围了你，当你反应过来的时候，发现自己正身处<span class=\"yellow b\">{$plsinfo[$pls]}</span>。<br>";
				if (CURSCRIPT !== 'botservice') $log.="<span id=\"HsUipfcGhU\"></span>";
			}
			$ret = 1;
		}
	}
	
	//模块 base/items/radar
	function use_radar($radarsk = 0)
	{
		//生命探测器无法显示隐藏区域
		$plsinfo = array_flip(array_diff(array_flip($plsinfo),$hidden_arealist));
	}
	
	//模块 core/map
	function check_can_enter($pno){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));
		$hag_name = array_search($pls,$hidden_areagroup);
		if(array_search($pno,$hidden_areagroup)==$hag_name)
		{
			$enter_hidden_area_flag = true;
		}
		else
		{
			$enter_hidden_area_flag = false;
		}
		return (!check_in_forbidden_area($pno) || $hack) && $enter_hidden_area_flag;
	}
	//单次禁区增加
	function add_once_area($atime) {
		$arealist = array_diff($arealist,$hidden_arealist);
	}
	function rs_game($xmode = 0) {
		$arealist = array_diff($arealist,$hidden_arealist);
	}
	function get_next_areadata_html($atime=0)
	{
		$plsinfo = array_flip(array_diff(array_flip($plsinfo),$hidden_arealist));
	}	
	
	//模块bass/npc
		//非禁区域列表。如果$no_dangerous_zone开启，则再排除掉SCP、英灵殿等危险地区
	function get_safe_plslist($no_dangerous_zone = true, $type = 0){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
			eval(import_module('sys','map'));
			//排除隐藏地区
			$arealist = array_diff($arealist,$hidden_arealist);
		if($areanum+1 > sizeof($arealist)) return array();
			else {
				$r = array_slice($arealist,$areanum+1);
				if($no_dangerous_zone) $r = array_diff($r, array(32,34));
				return $r;
			}
	}

	//模块base/explore	
	function move($moveto = 99) {
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if(($moveto == 'main')||($moveto < 0 )||(($moveto >= $plsnum) && !in_array($moveto,$hidden_arealist))){
			$log .= '请选择正确的移动地点。<br>';
			return;
		} elseif($pls == $moveto){
			$log .= '相同地点，不需要移动。<br>';
			return;
		} elseif(array_search($moveto,$arealist) <= $areanum && !$hack && !in_array($moveto,$hidden_arealist)){
			$log .= $plsinfo[$moveto].'是禁区，还是离远点吧！<br>';
			return;
		} elseif(in_array($moveto,$hidden_arealist)){
			$hag_name = array_search($pls,$hidden_areagroup);
			if(array_search($moveto,$hidden_areagroup)==$hag_name)
			{
				$enter_hidden_area_flag = true;
			}
			else
			{
				$enter_hidden_area_flag = false;
			}
			if(!$enter_hidden_area_flag){
				$log .= "地图上没有{$plsinfo[$moveto]}啊？是不是你看错了？{$hag_name}<br>";
				return;
			}
		}
	}
	
	function search(){
		if(array_search($pls,$arealist) <= $areanum && !$hack && !in_array($pls,$hidden_arealist)){
			$log .= $plsinfo[$pls].'是禁区，还是赶快逃跑吧！<br>';
			return;
	}
	/*-----htm模板部分-----*/
	
//templates/default/move.htm	
/*---------------------
<option value="main">■ 移动 ■</option>
<optgroup label="移动地点列表：">
<!--{if (in_array($pls,$hidden_arealist))}-->
	<!--{loop array_keys($hidden_areagroup) $hag_name}-->
		<!--{if in_array($pls,$hidden_areagroup[$hag_name])}-->
			<!--{loop $hidden_areagroup[$hag_name] $hag_num}-->
			<!--{if $pls == $hag_num}--><optgroup label="■ 现在位置 ■">
			<!--{else}--><option value="$hag_num">$plsinfo[$hag_num]($xyinfo[$hag_num])</option>
			<!--{/if}-->
			<!--{/loop}-->
		<!--{/if}-->
	<!--{/loop}-->
<!--{else}-->
	<!--{loop $plsinfo $key $value}-->
	<!--{if ((array_search($key,$arealist) > $areanum || $hack) && (!in_array($key,$hidden_arealist)))}-->
		<!--{if $pls == $key}--><optgroup label="■ 现在位置 ■">
		<!--{else}--><option value="$key">$value($xyinfo[$key])</option>
		<!--{/if}-->
	<!--{/if}-->
	<!--{/loop}-->
<!--{/if}-->
------------------------*/
}	


?>