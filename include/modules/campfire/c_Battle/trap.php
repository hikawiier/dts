<?php

namespace c_battle
{
	//战斗中踩雷相关判定
	//原版踩雷是纯evp/pvp设计 基本没有eve兼容性 所以就恶心一下 在这里重过一遍几个关键函数

	function calculate_in_battle_trap_obbs(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','map','itemmain','trap'));
		//奇迹雷兼容
		$tpls=$pa['pls'];$tpzone=$pa['pzone'];
		$trapresult = $db->query("SELECT * FROM {$tablepre}maptrap WHERE pls = '$tpls' AND pzone ='$tpzone' ORDER BY itmk DESC");
		$xtrp = $db->fetch_array($trapresult);
		if($xtrp['itmk'] == 'TOc') { in_battle_trapget($pa,$pd,$active,$xtrp); return 1; }
		//获取基础踩雷率
		$real_trap_obbs = \trap\calculate_real_trap_obbs($pa);
		$real_trap_obbs = \trap\calculate_real_trap_obbs_change($real_trap_obbs,$pa);
		$trap_dice=rand(0,$trap_max_obbs-1);
		//测试用
		//$trap_dice = 0;
		if($trap_dice < $real_trap_obbs)
		{
			$trapresult = \trap\get_traplist();
			$trpnum = $db->num_rows($trapresult);
			if ($trpnum == 0) return 0;
			$itemno = rand(0,$trpnum-1);
			$db->data_seek($trapresult,$itemno);
			$mi=$db->fetch_array($trapresult);
			in_battle_trapget($pa,$pd,$active,$mi);
		}
	}

	function in_battle_trapget(&$pa,&$pd,$active,$mi)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','trap'));
		$pa['itm0']=$mi['itm'];
		$pa['itmk0']=$mi['itmk'];
		$pa['itme0']=$mi['itme'];
		$pa['itms0']=$mi['itms'];
		$pa['itmsk0']=$mi['itmsk']; 
		$tid=$mi['tid'];
		$db->query("DELETE FROM {$tablepre}maptrap WHERE tid='$tid'");
		
		$pa['source_trap'] = is_numeric($pa['itmsk0']) ? true : false;
		$pa['self_trap_flag'] = ($pa['source_trap'] && ($pa['itmsk0'] == $pa['pid'])) ? true : false;
		
		if($pa['source_trap'] && !$pa['self_trap_flag'])
		{
			$wdata = \player\fetch_playerdata_by_pid($pa['itmsk0']);
			$pa['source_trap']=$wdata['name'];
			$pa['source_trap_type']=$wdata['type'];
			$pa['trprefix']='<span class="yellow b">'.$pa['source_trap'].'</span>设置的';
		}
		elseif($pa['self_trap_flag'])
		{
			$pa['source_trap'] = $pa['name'];
			$pa['source_trap_type'] = $pa['type'];
			$pa['trprefix'] = '自己设置的';
		}
		else
		{
			$pa['source_trap'] = $pa['source_trap_type'] = $pa['trprefix'] = '';
		}
		in_battle_trap($pa,$pd,$active);
	}

	function in_battle_trap(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//奇迹雷兼容
		if ($pa['itmk0'] == 'TOc') { in_battle_trap_hit($pa,$pd,$active); return; }
		//获取回避率率
		$escrate = \trap\get_trap_escape_rate($pa);
		$escrate = $escrate >= 90 ? 90 : $escrate;//最大回避率
		$dice=rand(0,99);
		//测试用 $dice=100;
		if($dice >= $escrate)
		{
			in_battle_trap_hit($pa,$pd,$active);
		} 
		else 
		{
			in_battle_trap_miss($pa,$pd,$active);
		}
	}

	function in_battle_trap_miss(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','trap','logger'));
		if ($pa['source_trap'])
		{
			addnews($now,'trapmiss',$pa['name'],$pa['source_trap'],$pa['itm0']);
		}
		$p_name = $pa['type'] ? $pa['name'] : '你';
		$log.= "什么！{$p_name}不小心踩到了{$pa['trprefix']}陷阱<span class=\"yellow b\">{$pa['itm0']}</span>！不过成功地回避了它！<br>";
		//重置标记
		in_battle_trapflag_reset($pa,$pd,$active);
	}

	function in_battle_trap_hit(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		in_battle_trap_deal_damage($pa,$pd,$active);
		if($pa['hp'] <= 0)
		{		
			$log .= "<span class=\"red b\">你被{$pa['trprefix']}陷阱杀死了！</span>";
			$pa['death_flag'] = 1;
			$pa['deathmark'] = 122;		
		}
		//重置标记
		in_battle_trapflag_reset($pa,$pd,$active);
		return;	
	}

	function in_battle_trap_deal_damage(&$pa,&$pd,$active)
	{
		//关于陷阱迎击属性
		//暂时没有在这里加上关于陷阱迎击的判定 因为之后要像“物理护甲、属性抗性”一样新建一个总计的“陷阱闪避率”技能 迎击、探雷等属性交由该技能统一处理
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','trap','logger'));
		$bid = $pa['itmsk0'];
		$tmp_edata= $bid ? \player\fetch_playerdata_by_pid($bid) : \player\create_dummy_playerdata();
		$p_name = $pa['type'] ? $pa['name'] : '你';
		$log .= "什么！{$p_name}不小心踩到了{$trprefix}陷阱<span class=\"yellow b\">{$pa['itm0']}</span>！";
		//陷阱能造成的基础伤害
		$trap_damage = round(rand(0,$pa['itme0']/2)+($pa['itme0']/2));
		//奇迹雷兼容
		if($pa['itmk0'] == 'TOc') $trap_damage = 999983;
		$tritm=Array();
		$tritm['itm']=$pa['itm0']; $tritm['itmk']=$pa['itmk0']; 
		$tritm['itme']=$pa['itme0']; $tritm['itms']=$pa['itms0']; $tritm['itmsk']=$pa['itmsk0'];
		//这里前两个输入分别是“陷阱主人$tmp_edata”和“被炸者$pa”
		$multiplier = \trap\get_trap_damage_multiplier($tmp_edata, $pa, $tritm, $trap_damage);
		if (count($multiplier)>0)
		{
			$fin_dmg=$trap_damage; $mult_words='';
			foreach ($multiplier as $key)
			{
				$fin_dmg=$fin_dmg*$key;
				$mult_words.="×{$key}";
			}
			$fin_dmg=round($fin_dmg);
			if ($fin_dmg < 1) $fin_dmg = 1;
			$log .= "{$p_name}受到了{$trap_damage}{$mult_words}＝<span class=\"dmg\">{$fin_dmg}</span>点伤害。<br>";
			$trap_damage = $fin_dmg;
		}
		else
		{
			$log.="受到了<span class=\"dmg\">$trap_damage</span>点伤害！<br>";
		}
		$trap_damage = \trap\get_trap_final_damage_modifier_up($tmp_edata, $pa, $tritm, $trap_damage);
		$trap_damage = \trap\get_trap_final_damage_modifier_down($tmp_edata, $pa, $tritm, $trap_damage);
		$trap_damage = \trap\get_trap_final_damage_change($tmp_edata, $pa, $tritm, $trap_damage);

		$pa['hp'] -= $trap_damage;

		if ($trap_damage>0) \trap\post_traphit_events($tmp_edata, $pa, $tritm, $damage);
		
		if($pa['source_trap']){
			addnews($now,'trap',$pa['name'],$pa['source_trap'],$pa['itm0'],$trap_damage);
		}
		//send_trap_enemylog(1);
		//战斗雷不发log了 说到底这个模式就只有玩家一个人 发了也没人能看到
	}

	function in_battle_trapflag_reset(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$pa['itm0']= $pa['itmk0'] = $pa['itmsk0'] = '';
		$pa['itme0'] = $pa['itms0'] = 0;
		unset($pa['self_trap_flag']);
		unset($pa['source_trap']);
		unset($pa['source_trap_type']);
		unset($pa['trprefix']);
		return;
	}

	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if($news == 'death122') {
			$dname = $typeinfo[$b].' '.$a;
			if(!$e)
				$e0="<span class=\"yellow b\">【{$dname} 什么都没说就死去了】</span><br>\n";
			else  $e0="<span class=\"yellow b\">【{$dname}：“{$e}”】</span><br>\n";
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">$a</span>在与<span class=\"yellow b\">$c</span>的战斗中误触陷阱<span class=\"red b\">$d</span>而死{$e0}</li>";
		}
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>