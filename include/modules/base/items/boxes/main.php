<?php

namespace boxes
{
	function init()
	{
		eval(import_module('itemmain'));
		$iteminfo['p'] = '礼物';
		$iteminfo['ygo'] = '卡包';
		$iteminfo['fy'] = '全图唯一的野生浮云礼盒';
		$iteminfo['GA'] = '弹药箱';
		$iteminfo['kj3'] = '礼包';
		$iteminfo['GC'] = '军火箱';
	}

	function itemuse(&$theitem) 
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','itemmain','logger'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if(strpos ( $itmk, 'p' ) === 0)
		{
			//现在所有的礼物盒类道具都应该使用类别'p'，依靠属性来判断究竟是什么礼物盒
			//每个属性对应的文件名
			$p_arr = Array(
				'^999^' => 'present',
				'^998^' => 'ygobox',
				'^997^' => 'fybox',
				'^996^' => 'ugabox',
				'^995^' => 'ugcbox',
			);
			//这样写的问题就是，礼盒的属性里不能再有其他内容了，否则就会爆炸，保险起见加一个判定
			$pi_flag = false;
			foreach(array_keys($p_arr) as $p_sk)
			{
				$isk = \itemmain\get_itmsk_array($itmsk);
				if(in_array($p_sk,$isk) && sizeof($isk)==1)
				{
					$pi_flag = true;
				}
			}		
			if(!$pi_flag)
			{
				$log.="这个盒子看起来已经坏掉了，还是扔了吧。<br>";
				return;
			}
			$p_path = '/config/'.$p_arr[$isk[0]].'.config.php';
			$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
			$file = __DIR__.$p_path;
			$plist = openfile($file);
			while (1)
			{
				$rand = rand(0,count($plist)-1);
				list($in,$ik,$ie,$is,$isk) = explode(',',$plist[$rand]);
				$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
				//房间模式内开不出卡
				if (!in_array($gametype,$room_mode) || substr($ik,0,2)!='VO') break;
			}
			addnews($now,'present',$name,$itm,$in);
			\itemmain\itms_reduce($theitem);
			\itemmain\itemget();		
			return;
		} /*elseif(strpos ( $itmk, 'ygo' ) === 0){
			$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
			$file = __DIR__.'/config/ygobox.config.php';
			$plist1 = openfile($file);
			$rand1 = rand(0,count($plist1)-1);
			list($in,$ik,$ie,$is,$isk) = explode(',',$plist1[$rand1]);
			$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
			addnews($now,'present',$name,$itm,$in);
			\itemmain\itms_reduce($theitem);
			\itemmain\itemget();	
			return;
		} elseif(strpos ( $itmk, 'fy' ) === 0){
			$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
			$file = __DIR__.'/config/fybox.config.php';
			$plist1 = openfile($file);
			$rand1 = rand(0,count($plist1)-1);
			list($in,$ik,$ie,$is,$isk) = explode(',',$plist1[$rand1]);
			$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
			addnews($now,'present',$name,$itm,$in);
			\itemmain\itms_reduce($theitem);
			\itemmain\itemget();	
			return;
		} elseif(strpos ( $itmk, 'GA' ) === 0){
			$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
			$file = __DIR__.'/config/ugabox.config.php';
			$plist1 = openfile($file);
			$rand1 = rand(0,count($plist1)-1);
			list($in,$ik,$ie,$is,$isk) = explode(',',$plist1[$rand1]);
			$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
			addnews($now,'present',$name,$itm,$in);
			\itemmain\itms_reduce($theitem);
			\itemmain\itemget();	
			return;
		} elseif(strpos ( $itmk, 'kj3' ) === 0){
			$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
			$file = __DIR__.'/config/kj3box.config.php';
		} elseif(strpos ( $itmk, 'GC' ) === 0){
			$log.="你打开了<span class=\"yellow\">$itm</span>。<br>";
			$file = __DIR__.'/config/ugcbox.config.php';
			$plist1 = openfile($file);
			$rand1 = rand(0,count($plist1)-1);
			list($in,$ik,$ie,$is,$isk) = explode(',',$plist1[$rand1]);
			$itm0 = $in;$itmk0=$ik;$itme0=$ie;$itms0=$is;$itmsk0=$isk;
			addnews($now,'present',$name,$itm,$in);
			\itemmain\itms_reduce($theitem);
			\itemmain\itemget();	
			return;
		}*/
		$chprocess($theitem);
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		
		if($news == 'present') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow\">{$a}打开了{$b}，获得了{$c}！</span></li>";
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
		
}

?>
