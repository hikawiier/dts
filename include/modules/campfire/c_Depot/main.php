<?php

namespace c_depot
{
	function init()
	{
	}

	function rs_game($xmode)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		$chprocess($xmode);		
		eval(import_module('sys','c_depot'));
		if ($xmode & 8) {	//仓库内储存的道具初始化		
			foreach($npc_depot as $nd_num => $nd_arr)
			{
				foreach($nd_arr['itm'] as $nd_itm_arr)
				{
					$ditm = $nd_itm_arr['itm'];$ditmk = $nd_itm_arr['itmk'];$ditmsk = $nd_itm_arr['itmsk'];
					$ditme = $nd_itm_arr['itme'];$ditms = $nd_itm_arr['itms'];
					$dname = $nd_arr['name'];$dtype = $nd_arr['type'];
					$db->query("INSERT INTO {$tablepre}itemdepot (itm, itmk, itme, itms, itmsk ,itmowner, itmpw) VALUES ('$ditm', '$ditmk', '$ditme', '$ditms', '$ditmsk', '$dname', '$dtype')");
				}
			}
		}
	}

	function depot_getlist($n,$t)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys'));		
		$iarr = Array();		
		$depot = $db->query("SELECT * FROM {$tablepre}itemdepot WHERE itmowner='$n' AND itmpw='$t'");
		while($i = $db->fetch_array($depot)) 
		{
			$iarr[] = $i;
		}
		return $iarr;
	}	

	function depot_changeowner($n,$t,$tn,$tt)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys'));		
		$db->query("UPDATE {$tablepre}itemdepot SET itmowner='$tn',itmpw='$tt' WHERE itmowner='$n' AND itmpw=$t");
	}

	function depot_save($i)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','c_depot'));		
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
		if(strpos(${'itmsk'.$i},'O')!==false)
		{
			$log.="<span class='red'>你尝试着将诅咒道具扔进仓库，但仓库又立刻将它吐了出来！</span><br>";
			return;
		}
			
		$idpt = depot_getlist($name,$type);
		$idpt_num = sizeof($idpt);
		if($idpt_num+1>$max_saveitem_num)
		{
			$log.="<span class='red'>仓库已满，无法再储存道具！</span><br>";
			return;
		}
		$money -= $saveitem_cost;
		$log.="你成功将道具<span class='yellow'>{${'itm'.$i}}</span>存进了仓库内！<br>同时你也不得不支付了手续费<span class='yellow'>{$saveitem_cost}</span>元。<br>";
		$itm=&${'itm'.$i};$itmk=&${'itmk'.$i};$itmsk=&${'itmsk'.$i};
		$itme=&${'itme'.$i};$itms=&${'itms'.$i};
		addnews($now,'af_ds',$name,${'itm'.$i});
		$db->query("INSERT INTO {$tablepre}itemdepot (itm, itmk, itme, itms, itmsk ,itmowner, itmpw) VALUES ('$itm', '$itmk', '$itme', '$itms', '$itmsk', '$name', '$type')");
		$itm='';$itmk='';$itmsk='';
		$itme=0;$itms=0;
	}

	function depot_load($i)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','itemmain','c_depot'));	
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
		$idpt = depot_getlist($name,$type);
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
		$iid = $idpt[$i]['iid'];
		$db->query("DELETE FROM {$tablepre}itemdepot WHERE iid='$iid'");
		$log.="你成功将道具<span class='yellow'>{$itm0}</span>从仓库中取了出来！<br>同时你也不得不支付了保管费<span class='yellow'>{$loaditem_cost}</span>元……你感觉自己的心在滴血。<br>";
		addnews($now,'af_dl',$name,$itm0);
		\itemmain\itemget();
	}

	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if($news == 'af_ds') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}向仓库中存入了道具<span class='yellow'>{$b}</span>。</span><br>\n";
		if($news == 'af_dl') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"lime\">{$a}从仓库中取出了道具<span class='yellow'>{$b}</span>。</span><br>\n";
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e);
	}
	
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;	
		eval(import_module('sys','player','logger','c_depot','input','itemmain'));
		if($mode == 'depot')
		{
			if($command == 'menu')
			{
				$log.="你思考了一下，还是决定暂时不使用仓库功能了。<br>……以后需要用到它的时候再来吧。<br>";
			}
			elseif($command == 'depot_save')
			{
				ob_clean();
				include template(MOD_C_DEPOT_DEPOT_SAVE);
				$cmd = ob_get_contents();
				ob_clean();
				return;			
			}
			elseif($command == 'depot_load')
			{
				$saveitem_list = depot_getlist($name,$type);
				ob_clean();
				include template(MOD_C_DEPOT_DEPOT_LOAD);
				$cmd = ob_get_contents();
				ob_clean();
				return;				
			}
			else
			{
				$log.="depot指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		if($mode == 'depot_save')
		{
			if($command == 'menu')
			{
				$log.="你仔细思考了一下，还是决定暂时不储存道具了。<br>等以后改变主意了再说吧。<br>";
			}
			elseif(strpos($command,'saveitem_')!==false)
			{
				$iid = substr($command,9);
				depot_save($iid);
			}
			else
			{
				$log.="depot_save指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		if($mode == 'depot_load')
		{
			if(!$saveitem_list)
			{
				$saveitem_list = Array();
			}
			if($command == 'menu')
			{
				$log.="你仔细思考了一下，还是决定暂时不取出道具了。<br>等以后改变主意了再说吧。<br>";
			}
			elseif($command== 'c_depot_load')
			{
				depot_load($load_itm_num);
			}
			else
			{
				$log.="depot_load指令选择错误，如果遇到BUG，请联系管理员。<br>";
				return;
			}
			return;
		}
		$chprocess();
	}
}

?>
