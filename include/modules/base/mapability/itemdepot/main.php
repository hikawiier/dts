<?php

namespace itemdepot
{
	function init()
	{
	}
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemdepot','input','itemmain'));
		/*==========精灵中心特殊功能：itemdepot菜单部分开始==========*/
		if($mode == 'lp_itemdepot')
		{
			if($command == 'menu')
			{
				$log.="你思考了一下，还是决定暂时不使用仓库功能了。<br>……以后需要用到它的时候再来吧。<br>";
			}
			elseif($command == 'itemdepot_save')
			{
				ob_clean();
				include template(MOD_ITEMDEPOT_LP_ITEMDEPOT_SAVE);
				$cmd = ob_get_contents();
				ob_clean();
				return;			
			}
			elseif($command == 'itemdepot_load')
			{
				$saveitem_list = change_itemdepot('decode',$itemdepot);
				ob_clean();
				include template(MOD_ITEMDEPOT_LP_ITEMDEPOT_LOAD);
				$cmd = ob_get_contents();
				ob_clean();
				return;				
			}
			else
			{
				$log.="itemdepot指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		if($mode == 'itemdepot_save')
		{
			if($command == 'menu')
			{
				$log.="你仔细思考了一下，还是决定暂时不储存道具了。<br>等以后改变主意了再说吧。<br>";
			}
			elseif(strpos($command,'saveitem_')!==false)
			{
				$iid = substr($command,9);
				itemdepot_save($iid);
			}
			else
			{
				$log.="itemdepot_save指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		if($mode == 'itemdepot_load')
		{
			if(!$saveitem_list)
			{
				$saveitem_list = Array();
			}
			if($command == 'menu')
			{
				$log.="你仔细思考了一下，还是决定暂时不取出道具了。<br>等以后改变主意了再说吧。<br>";
			}
			elseif($command== 'itemdepot_load')
			{
				itemdepot_load($load_itm_num);
			}
			else
			{
				$log.="itemdepot_load指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		/*==========精灵中心特殊功能：itemdepot菜单部分结束==========*/
		$chprocess();
	}
	/*==========精灵中心特殊功能：itemdepot功能部分开始==========*/
	function change_itemdepot($idpt_way,$idpt_info)
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
	function itemdepot_save($i)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','itemdepot'));		
		if($pls!=19)
		{
			$log.="<span class='red'>你所在的位置没有仓库功能，如果存在BUG，请告知管理员。</span><br>";
			return;
		}
		if(!is_numeric($i) || !$i || $i>6 || $i<1 || ${'itms'.$i}<=0)
		{
			$log.="<span class='red'>要储存的道具信息错误，请返回重新输入。</span><br>";
			return;
		}
		if($money < $saveitem_cost)
		{
			$log.="<span class='red'>你身上的钱不足以支付储存道具的手续费！</span><br>";
			return;
		}
		$idpt = change_itemdepot('decode',$itemdepot);
		$idpt_num = sizeof($idpt);
		if($idpt_num+1>$max_saveitem_num)
		{
			$log.="<span class='red'>仓库已满，无法再储存道具！</span><br>";
			return;
		}
		$money -= $saveitem_cost;
		$log.="你成功将道具<span class='yellow'>{${'itm'.$i}}</span>存进了仓库内！<br>同时你也不得不支付了手续费<span class='yellow'>{$saveitem_cost}</span>元。<br>";
		$idpt[$idpt_num]['itm'] = ${'itm'.$i}; ${'itm'.$i}='';
		$idpt[$idpt_num]['itmk'] = ${'itmk'.$i}; ${'itmk'.$i}='';
		$idpt[$idpt_num]['itme'] = ${'itme'.$i}; ${'itme'.$i}=0;
		$idpt[$idpt_num]['itms'] = ${'itms'.$i}; ${'itms'.$i}=0;
		$idpt[$idpt_num]['itmsk'] = ${'itmsk'.$i}; ${'itmsk'.$i}='';
		sort($idpt);
		$idpt = change_itemdepot('encode',$idpt);
		$itemdepot = $idpt;
		\player\player_save(\player\fetch_playerdata_by_pid($pid));
	}
	function itemdepot_load($i)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','itemdepot'));	
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
		$idpt = change_itemdepot('decode',$itemdepot);
		$idpt_num = sizeof($idpt);		
		if(!is_numeric($i) || $i>$max_saveitem_num || $i<0 || $idpt[$i]['itms']<=0)
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
		\itemmain\itemget();
		sort($idpt);
		$idpt = change_itemdepot('encode',$idpt);
		$itemdepot = $idpt;
		\player\player_save(\player\fetch_playerdata_by_pid($pid));
	}
	/*==========精灵中心特殊功能：itemdepot功能部分结束==========*/
}

?>
