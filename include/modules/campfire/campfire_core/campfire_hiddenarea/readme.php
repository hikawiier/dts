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
	
	//模块 core/player
	//	01.
	function addarea_pc_process($atime)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		if($areanum >= sizeof($plsinfo) - 1) return;//如果禁区数已达上限，跳过所有处理（gameover()函数会判定游戏结束）
		//plsinfo修改标记
		if($hidden_area && ($areanum >= sizeof($plsinfo)-sizeof($hidden_area)-1)) return;
		$now_areaarr = array_slice($arealist,0,$areanum+1);
		$where = "('".implode("','",$now_areaarr)."')";//构建查询列表——当前所有禁区
		$result = $db->query("SELECT * FROM {$tablepre}players WHERE pls IN $where AND hp>0");
		while($sub = $db->fetch_array($result)) 
		{
			addarea_pc_process_single($sub, $atime);
		}
		$alivenum = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}players WHERE hp>0 AND type=0"), 0);
		$chprocess($atime);
		return;
	}
	
	
	//模块 main/map
	//  01.
	function get_next_areadata_html($atime=0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		$areadata='';
		if(!$atime){
			$atime = $areatime;
		}
		//plsinfo修改标记
		$plsinfo = array_flip(array_diff(array_flip($plsinfo),$hidden_arealist));
		$timediff = $atime - $now;
		if($timediff > 43200){//如果禁区时间在12个小时以后则显示其他信息
			$areadata .= '距离下一次禁区还有12个小时以上';
		}else{
			if($areanum < count($plsinfo)) {
				$at= getdate($atime);
				$nexthour = $at['hours'];$nextmin = $at['minutes'];
				while($nextmin >= 60){
					$nexthour +=1;$nextmin -= 60;
				}
				if($nexthour >= 24){$nexthour-=24;}
				$areadata .= "<b>{$nexthour}时{$nextmin}分：</b> " . implode('&nbsp;&nbsp;', get_area_plsname(0, $areanum+1, $areanum+$areaadd));
			}
			if($areanum+$areaadd < count($plsinfo)) {
				$at2= getdate($atime + get_area_interval()*60);
				$nexthour2 = $at2['hours'];$nextmin2 = $at2['minutes'];
				while($nextmin2 >= 60){
					$nexthour2 +=1;$nextmin2 -= 60;
				}
				if($nexthour2 >= 24){$nexthour2-=24;}
				$areadata .= "；<b>{$nexthour2}时{$nextmin2}分：</b> " . implode('&nbsp;&nbsp;', get_area_plsname(0, $areanum+$areaadd+1, $areanum+$areaadd*2));
			}
			if($areanum+$areaadd*2 < count($plsinfo)) {
				$at3= getdate($atime + get_area_interval()*120);
				$nexthour3 = $at3['hours'];$nextmin3 = $at3['minutes'];
				while($nextmin3 >= 60){
					$nexthour3 +=1;$nextmin3 -= 60;
				}
				if($nexthour3 >= 24){$nexthour3-=24;}
				$areadata .= "；<b>{$nexthour3}时{$nextmin3}分：</b> " . implode('&nbsp;&nbsp;', get_area_plsname(0, $areanum+$areaadd*2+1, $areanum+$areaadd*3));
			}
		}
		echo $areadata;
	}	
	//	02.
	function add_once_area($atime) {
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','map'));
		if ( $gamestate > 10 && $now > $atime ) {
			//plsinfo修改标记
			$plsinfo = array_flip(array_diff(array_flip($plsinfo),$hidden_arealist));
			$plsnum = sizeof($plsinfo) - 1;
			$areanum += $areaadd;
			if($areanum >= $plsnum) 
			{
				$areaaddlist = array_slice($arealist,$areanum - $areaadd +1);
				$areanum = $plsnum;
			}
			else
			{
				if($hack > 0){$hack--;}
				$areaaddlist = array_slice($arealist,$areanum - $areaadd +1,$areaadd);
			}
			
			post_addarea_process($atime, $areaaddlist);
			
			check_addarea_gameover($atime);
		} else {
			return;
		}
	}
	//	03.
	function check_addarea_gameover($atime){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map'));
		$plsnum = sizeof($plsinfo) - 1;
		//plsinfo修改标记
		if($hidden_area) $plsnum = sizeof($plsinfo)-sizeof($hidden_area)-1;
		if($areanum >= $plsnum) 
		{
			\sys\gameover($atime,'end1');
			return;
		}
			
		if( $alivenum == 1 && $gamestate >= 30 ) { 
			\sys\gameover($atime);
			return;
		} elseif( $alivenum <= 0 && $gamestate >= 30 ) {
			\sys\gameover($atime,'end1');
		} else {
			\sys\rs_game(16+32);
		}
	}
	//	04.
	function check_can_enter($pno){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map'));
		//$plsinfo修改标记
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
	
	
	//模块 base/weather
	//	01.
	function apply_tornado_weather_effect()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','player','logger'));
		//plsinfo修改标记
		$plsinfo = array_flip(array_diff(array_flip($plsinfo),$hidden_arealist));
		if($hack)
		{
			$pls = rand(0,sizeof($plsinfo)-1);
		}
		else 
		{
			$pls = rand($areanum+1,sizeof($plsinfo)-1);$pls=$arealist[$pls];
		}
		$log .= "但是强烈的龙卷风把你吹到了<span class=\"yellow b\">$plsinfo[$pls]</span>！<br>";
	}
	
	//模块 base/itemmain
	//	01.
	function rs_game($xmode)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$chprocess($xmode);
		
		eval(import_module('sys','map','itemmain'));
		if ($xmode & 16) {	//地图道具
			$plsnum = sizeof($plsinfo);
			//plsinfo修改标记
			if($hidden_area)
			{
				$plsnum = sizeof($plsinfo)-sizeof($hidden_area);
			}			
			$iqry = '';
			$itemlist = get_itemfilecont();
			$in = sizeof($itemlist);
			$an = $areanum ? ceil($areanum/$areaadd) : 0;
			for($i = 1; $i < $in; $i++) {
				if(!empty($itemlist[$i]) && strpos($itemlist[$i],',')!==false){
					list($iarea,$imap,$inum,$iname,$ikind,$ieff,$ista,$iskind) = mapitem_data_process(explode(',',$itemlist[$i]));
					if( $iarea == $an || $iarea == 99 || ($iarea == 98 && $an > 0)) {
						for($j = $inum; $j>0; $j--) {
							if ($imap == 99)
							{
								do {
									$rmap = rand(0,$plsnum-1);
								} while (in_array($rmap,$map_noitemdrop_arealist));
							}
							else  $rmap = $imap;
							list($iname, $ikind, $ieff, $ista, $iskind, $rmap) = mapitem_single_data_process($iname, $ikind, $ieff, $ista, $iskind, $rmap);
							$iqry .= "('$iname', '$ikind','$ieff','$ista','$iskind','$rmap'),";
						}
					}
				}
			}
			if(!empty($iqry)){
				$iqry = "INSERT INTO {$tablepre}mapitem (itm,itmk,itme,itms,itmsk,pls) VALUES ".substr($iqry, 0, -1);
				$db->query($iqry);
			}
		}
	}
	
	//模块 base/item/trap
	//	01.
	function rs_game($xmode)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$chprocess($xmode);
		
		eval(import_module('sys','map','itemmain','trap'));
		if ($xmode & 16) {	//地图陷阱初始化
			//plsinfo标记
			$plsnum = sizeof($plsinfo);
			if($hidden_area)
			{
				$plsnum = sizeof($plsinfo)-sizeof($hidden_area);
			}	
			$iqry = '';
			$itemlist = get_trapfilecont();
			$in = sizeof($itemlist);
			$an = $areanum ? ceil($areanum/$areaadd) : 0;
			for($i = 1; $i < $in; $i++) {
				if(!empty($itemlist[$i]) && strpos($itemlist[$i],',')!==false){
					list($iarea,$imap,$inum,$iname,$ikind,$ieff,$ista,$iskind) = explode(',',$itemlist[$i]);
					if(strpos($iskind,'=')===0){
						$tmp_pa_name = substr($iskind,1);
						$iskind = '';
						$result = $db->query("SELECT pid FROM {$tablepre}players WHERE name='$tmp_pa_name' AND type>0");
						if($db->num_rows($result)){
							$ipid = $db->fetch_array($result);
							$iskind = $ipid['pid'];
						}
					}
					if(($iarea == $an)||($iarea == 99)) {
						for($j = $inum; $j>0; $j--) {							
							if ($imap == 99)
							{
								do {
									$rmap = rand(0,$plsnum-1);
								} while (in_array($rmap,$map_noitemdrop_arealist));
							}
							else  $rmap = $imap;
							$iqry .= "('$iname', '$ikind','$ieff','$ista','$iskind','$rmap'),";
						}
					}
				}
			}
			if(!empty($iqry)){
				$iqry = "INSERT INTO {$tablepre}maptrap (itm,itmk,itme,itms,itmsk,pls) VALUES ".substr($iqry, 0, -1);
				$db->query($iqry);
			}
		}
	}
	
	//模块 base/items/radar
	//	01.
	function use_radar($radarsk = 0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','map','radar','logger'));
		if(!$mode) {
			$log .= '仪器使用失败！<br>';
			return;
		}
		//plsinfo修改标记
		$plsinfo = array_flip(array_diff(array_flip($plsinfo),$hidden_arealist));
		//.....省略部分
	}
	
	//模块 base/explore
	//	01.
	function move($moveto = 99) {
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger'));
		
		$plsnum = sizeof($plsinfo);
		if((($moveto == 'main')||($moveto < 0 )||(($moveto >= $plsnum)))&&!array_search($moveto,$hidden_arealist)){
			$log .= '请选择正确的移动地点。<br>';
			return;
		} elseif($pls == $moveto){
			$log .= '相同地点，不需要移动。<br>';
			return;
		} elseif(array_search($moveto,$arealist) <= $areanum && !$hack && !in_array($moveto,$hidden_arealist)){
			//plsinfo修改标记
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
		//.....省略
	}
	
	//	02.
	function search(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','logger'));
		
		if(array_search($pls,$arealist) <= $areanum && !$hack && !in_array($pls,$hidden_arealist)){
			//plsinfo修改标记
			$log .= $plsinfo[$pls].'是禁区，还是赶快逃跑吧！<br>';
			return;
		}
		//.....省略
	}
	
	
	//模块 base/npc
	function get_safe_plslist($no_dangerous_zone = true, $type = 0){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		$ret = $chprocess($no_dangerous_zone, $type);
		//plsinfo修改标记
		eval(import_module('map'));		
		$ret = array_diff($ret,$hidden_area);
		if($no_dangerous_zone && 1 == $type)
			$ret = array_diff($ret, array(21,26,33));
		return $ret;
	}
		
	
	//模块 skill452
	function battle_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(452,$pd) && check_unlocked452($pd) && $pa['dmg_dealt']>=150 && $pd['hp']>0 && $pd['tactic']==4)
		{
			eval(import_module('logger','map','sys'));
			//plsinfo修改标记
			if($hidden_area) $plsinfo = array_flip(array_diff(array_flip($plsinfo),$hidden_arealist));
			$plsnum = sizeof($plsinfo) - 1;
			$pls452 = $arealist[rand($areanum+1,$plsnum)];	
			if ($areanum+1 < $plsnum){
				while ($pls452==34) {$pls452 = $arealist[rand($areanum+1,$plsnum)];}
			}
			if ($active)
				$log.="<span class=\"cyan b\">{$pd['name']}通过相位裂隙紧急转移到了{$plsinfo[$pls452]}！</span><br>";
			else  $log.="<span class=\"cyan b\">你通过相位裂隙紧急转移到了{$plsinfo[$pls452]}！</span><br>";
			$pd['pls']=$pls452;
		}
		$chprocess($pa,$pd,$active);
	}
	
	//模块 base/event
	if ($pls==34)
	{//英灵殿
		if (($art!='Untainted Glory')&&($gamestate != 50)&&($gametype!=2)){
			$rpls=-1;
			//增加了隐藏区域相关判定，标记一下
			while ($rpls<0 || $arealist[$rpls]==34 || !array_search($rpls,$arealist)){
				if($hack){$rpls = rand(0,sizeof($plsinfo)-1);}
				else {$rpls = rand($areanum+1,sizeof($plsinfo)-1);}
			} 
			$pls=$arealist[$rpls];
			$log.="殿堂的深处传来一个声音：<span class=\"evergreen b\">“你还没有进入这里的资格”。</span><br>一股未知的力量包围了你，当你反应过来的时候，发现自己正身处<span class=\"yellow b\">{$plsinfo[$pls]}</span>。<br>";
			if (CURSCRIPT !== 'botservice') $log.="<span id=\"HsUipfcGhU\"></span>";
		}
		$ret = 1;
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
//explore/explore.htm
/*---------------------
<input type="button" class="cmdbutton" id="zz" value="[Z]探索" onclick="$('command').value='search';postCmd('gamecmd','command.php');this.disabled=true;" <!--{if array_search($pls,$arealist) <= $areanum && !$hack && !in_array($pls,$hidden_arealist)}-->disabled<!--{/if}-->>
<span id="moveto_box"><select id="moveto" name="moveto" onchange="$('command').value='move';replay_record_DOM_path(this.options[this.selectedIndex]);postCmd('gamecmd','command.php');this.disabled=true;">
{template move}
</select></span>
<br /> 
------------------------*/

}	


?>