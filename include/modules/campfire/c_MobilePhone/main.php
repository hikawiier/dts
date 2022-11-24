<?php

namespace c_mobilephone
{
	//呃呃呃呃呃呃e为什么命名空间大写会报错
	
	function init() {}
	
	function print_mapzonedata()
	{
		//疑问 这个函数放这里还是放c_mapzone里 感觉放mapzone里更合适
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','c_mapzone'));
		$mapzonelist = $mapzonedata[$pls]['zonelist'];
		$mapzonelist = explode(',',$mapzonelist);
		for($i=0;$i<sizeof($mapzonelist);$i++){
			list($x, $y) = explode('-',$mapzonelist[$i]);
			$max_x = max($max_x,$x);
			$max_y = max($max_y,$y);
			$mpp[$x][$y]=$i;
		}
		$mpp['max_x'] = $max_x;
		$mpp['max_y'] = $max_y;

		//咕！杀了我！
		//$pzone_mapcontent = '<TABLE border="0" cellspacing="0" cellpadding="0" align=center" style="position:relative;background-repeat:no-repeat;background-position:right bottom;">';
		//$pzone_mapcontent .= 
		//'<TR>';
		/*for($v=0;$v<$x_axis;$v++)
		{
			$pzone_mapcontent .='<TD></TD>';
		}
		$pzone_mapcontent .='</TR>';	
		for($y=0;$y<$y_axis;$y++)
		{
			$pzone_mapcontent .= '<tr><TD></TD>';
			for($x=0;$x<$x_axis;$x++){
				if(isset($mpp[$x][$y])){
					$pzone_mapcontent .= '<td width="32" height="32" class="map2" align=middle background="map/zone.gif">';
					if($pzone == $mpp[$x][$y])
					{
						$pzone_mapcontent .='<span class="yellow b">你的位置</span>';
					}
					else
					{
						$pzone_num = $mpp[$x][$y];
						$pzone_info =  \c_mapzone\get_mapzoneinfo($pls,$pzone_num);
						$pzone_mapcontent .="<a id='mapzone_special' onclick='$('mode').value='special';$('command').value='mapzone_special';$('subcmd').value='mani_page';postCmd('gamecmd','command.php');this.disabled=true;'>";
						$pzone_mapcontent .= $pzone_info."</a>";
					}
					$pzone_mapcontent .='</span></td>';
				}else{
					$pzone_mapcontent .= '<td width="32" height="32"><IMG src="map/blank.gif" width="32" height="32" border=0></td>';
				}
			}
			$pzone_mapcontent .= '</tr>';
		}
		$pzone_mapcontent .= '</table>';*/
		return $mpp;
	}	

	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','input','logger','explore'));
		if ($mode == 'special' && $command == 'viewphone') 
		{
			$mode = MOD_C_MOBILEPHONE_PHONEPAGE;
			return;
		}
		if ($mode == 'special' && $command == 'mapzone_special') 
		{
			\explore\move($subcmd);
			return;
		}

		$chprocess();
	}
}

?>