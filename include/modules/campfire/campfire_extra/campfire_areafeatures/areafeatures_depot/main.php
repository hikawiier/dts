<?php

namespace areafeatures_depot
{
	function init()
	{
	}
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','areafeatures_depot','input','itemmain'));
		/*==========精灵中心特殊功能：areafeatures_depot菜单部分开始==========*/
		if($mode == 'lp_areafeatures_depot')
		{
			if($command == 'menu')
			{
				$log.="你思考了一下，还是决定暂时不使用仓库功能了。<br>……以后需要用到它的时候再来吧。<br>";
			}
			elseif($command == 'areafeatures_depot_save')
			{
				ob_clean();
				include template(MOD_areafeatures_depot_LP_areafeatures_depot_SAVE);
				$cmd = ob_get_contents();
				ob_clean();
				return;			
			}
			elseif($command == 'areafeatures_depot_load')
			{
				$saveitem_list = change_areafeatures_depot('decode',$areafeatures_depot);
				ob_clean();
				include template(MOD_areafeatures_depot_LP_areafeatures_depot_LOAD);
				$cmd = ob_get_contents();
				ob_clean();
				return;				
			}
			else
			{
				$log.="areafeatures_depot指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		if($mode == 'areafeatures_depot_save')
		{
			if($command == 'menu')
			{
				$log.="你仔细思考了一下，还是决定暂时不储存道具了。<br>等以后改变主意了再说吧。<br>";
			}
			elseif(strpos($command,'saveitem_')!==false)
			{
				$iid = substr($command,9);
				areafeatures_depot_save($iid);
			}
			else
			{
				$log.="areafeatures_depot_save指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		if($mode == 'areafeatures_depot_load')
		{
			if(!$saveitem_list)
			{
				$saveitem_list = Array();
			}
			if($command == 'menu')
			{
				$log.="你仔细思考了一下，还是决定暂时不取出道具了。<br>等以后改变主意了再说吧。<br>";
			}
			elseif($command== 'areafeatures_depot_load')
			{
				areafeatures_depot_load($load_itm_num);
			}
			else
			{
				$log.="areafeatures_depot_load指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		/*==========精灵中心特殊功能：areafeatures_depot菜单部分结束==========*/
		$chprocess();
	}
	/*==========精灵中心特殊功能：areafeatures_depot功能部分开始==========*/
	function change_areafeatures_depot($idpt_way,$idpt_info)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		//使用json_encode功能转换
		if($idpt_way=='encode')
		{
			$idpt_info = json_encode($idpt_info,JSON_UNESCAPED_UNICODE);
		}
		elseif($idpt_way=='decode')
		{
			if(!$idpt_info)
			{
				$idpt_info = Array();
				//装有道具的仓库格式应该是：
				//$idpt_info = Array(
				//	0 => Array(
				//		'itm' => $itm,
				//		'itmk' => $itmk,
				//		'itme' => $itme,
				//		'itms' => $itms,
				//		'itmsk' => $itmsk,
				//	),
				//);
				//以此类推
			}
			else
			{
				$idpt_info = json_decode($idpt_info,true);
			}
		}
		return $idpt_info;
	}
	function areafeatures_depot_save($i)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','areafeatures_depot'));		
		if($pls!=19)
		{
			$log.="<span class='red'>你所在的位置没有仓库功能，如果存在BUG，请告知管理员。</span><br>";
			return;
		}
		if(!is_numeric($i) || !$i || $i>6 || $i<1 || (${'itms'.$i}<=0 && ${'itms'.$i}!=='∞'))
		{
			$log.="<span class='red'>要储存的道具信息错误，请返回重新输入。</span><br>";
			return;
		}
		if($money < $saveitem_cost)
		{
			$log.="<span class='red'>你身上的钱不足以支付储存道具的手续费！</span><br>";
			return;
		}
		$idpt = change_areafeatures_depot('decode',$areafeatures_depot);
		$idpt_num = sizeof($idpt);
		if($idpt_num+1>$max_saveitem_num)
		{
			$log.="<span class='red'>仓库已满，无法再储存道具！</span><br>";
			return;
		}
		$money -= $saveitem_cost;
		$log.="你成功将道具<span class='yellow'>{${'itm'.$i}}</span>存进了仓库内！<br>同时你也不得不支付了手续费<span class='yellow'>{$saveitem_cost}</span>元。<br>";
		addnews($now,'areafeatures_depot_save',$name,${'itm'.$i});
		$idpt[$idpt_num]['itm'] = ${'itm'.$i}; ${'itm'.$i}='';
		$idpt[$idpt_num]['itmk'] = ${'itmk'.$i}; ${'itmk'.$i}='';
		$idpt[$idpt_num]['itme'] = ${'itme'.$i}; ${'itme'.$i}=0;
		$idpt[$idpt_num]['itms'] = ${'itms'.$i}; ${'itms'.$i}=0;
		$idpt[$idpt_num]['itmsk'] = ${'itmsk'.$i}; ${'itmsk'.$i}='';
		sort($idpt);
		$idpt = change_areafeatures_depot('encode',$idpt);
		$areafeatures_depot = $idpt;
		\player\player_save(\player\fetch_playerdata_by_pid($pid));
	}
	function areafeatures_depot_load($i)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','areafeatures_depot'));	
		if($pls!=19)
		{
			$log.="<span class='red'>你所在的位置没有仓库功能，如果存在BUG，请告知管理员。</span><br>";
			return;
		}
		if($money < $loaditem_cost)
		{
			$log.="<span class='red'>你身上的钱不足以支付取出道具的保管费……卧槽竟然二次收费，奸商啊！</span><br>";
			return;
		}
		$idpt = change_areafeatures_depot('decode',$areafeatures_depot);
		$idpt_num = sizeof($idpt);		
		if(!is_numeric($i) || $i>$max_saveitem_num || $i<0 || ($idpt[$i]['itms']<=0 && $idpt[$i]['itms']!=='∞'))
		{
			$log.="<span class='red'>要取出的道具信息错误，请返回重新输入。</span><br>";
			return;
		}
		$money -= $loaditem_cost;
		$itm0= $idpt[$i]['itm'];
		$itmk0= $idpt[$i]['itmk'];
		$itme0= $idpt[$i]['itme'];
		$itms0= $idpt[$i]['itms'];
		$itmsk0= $idpt[$i]['itmsk'];
		unset($idpt[$i]);
		$log.="你成功将道具<span class='yellow'>{$itm0}</span>从仓库中取了出来！<br>同时你也不得不支付了保管费<span class='yellow'>{$loaditem_cost}</span>元……你感觉自己的心在滴血。<br>";
		addnews($now,'areafeatures_depot_load',$name,$itm0);
		\itemmain\itemget();
		sort($idpt);
		$idpt = change_areafeatures_depot('encode',$idpt);
		$areafeatures_depot = $idpt;
		\player\player_save(\player\fetch_playerdata_by_pid($pid));
	}
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if($news == 'areafeatures_depot_save') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}向位于精灵中心的仓库中存入了道具<span class='yellow'>{$b}</span>。</span><br>\n";
		if($news == 'areafeatures_depot_load') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}从位于精灵中心的仓库中取出了道具<span class='yellow'>{$b}</span>。</span><br>\n";
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e);
	}
	/*==========精灵中心特殊功能：areafeatures_depot功能部分结束==========*/
}

?>
