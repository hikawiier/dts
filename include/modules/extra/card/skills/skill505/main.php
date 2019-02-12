<?php

namespace skill505
{
	$skill505_keyitm = '「巨大灯泡」';
	
	$skill505_plslist = array(1,4,10,13,22,23,24,33);//四科幻，四奇幻地点
	
	function init() 
	{
		define('MOD_SKILL505_INFO','card;unique;');
		eval(import_module('clubbase'));
		$clubskillname[505] = '一决';
	}
	
	function acquire505(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','skill505'));
		\skillbase\skill_setvalue(505,'u','0',$pa);
		//开局在某几个特定随机地点空降一个巨大灯泡
		shuffle($skill505_plslist);
		$rpls = $skill505_plslist[0];
		\skillbase\skill_setvalue(505,'rpls',$rpls,$pa);
		$itm = $skill505_keyitm;
		$itmk = 'DA';
		$itme = 100;
		$itms = 2;
		$itmsk = '';
		$db->query("INSERT INTO {$tablepre}mapitem (itm, itmk, itme, itms, itmsk ,pls) VALUES ('$itm', '$itmk', '$itme', '$itms', '$itmsk', '$rpls')");
	}
	
	function lost505(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_delvalue(505,'u',$pa);
	}
	
	function check_unlocked505(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return \skillbase\skill_getvalue(505,'u',$pa);
	}
	
	//只要是在skill505标记的地图，每次探索都有额外5%的概率发现巨大灯泡
	//当然如果灯泡捡起来了就没这回事了
	function discover_item()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','skill505'));
		if (\skillbase\skill_query(505,$sdata) && !check_unlocked505($sdata)){//注意这里是没解锁才有效
			eval(import_module('sys','player','logger','skill505'));
			$ipls = \skillbase\skill_getvalue(505,'rpls',$sdata);
			if($pls == $ipls) {
				$result = $db->query("SELECT * FROM {$tablepre}mapitem WHERE pls='$ipls' AND itm='$skill505_keyitm'");
				if($db->num_rows($result)){
					$idata = $db->fetch_array($result);
					if(rand(0,99) < 5) {
						$itms0 = \itemmain\focus_item($idata);
						if($itms0){
							\itemmain\itemfind();
							return;
						}
					}
				}
			}			
		}
		$chprocess();		
	}
	
	//使用任意道具（包括装备）后如果手臂装备是【巨大灯泡】则解锁
	function itemuse(&$theitem) 
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger','skill505','clubbase'));
		$o_ara = $ara;
		
		$chprocess($theitem);
		
		if(\skillbase\skill_query(505,$pd) && !check_unlocked505($pd) && $skill505_keyitm == $ara && $skill505_keyitm != $o_ara){
			\skillbase\skill_setvalue(505,'u','1');
			$log .= '你抱起了那个被称为「太阳」的灯泡。<span class="yellow b">你感到它暖洋洋的。</span><br>';
			$log .= '<span class="red b">提示：技能「'.$clubskillname[505].'」已激活！请时刻注意保护好'.$skill505_keyitm.'！</span><br>';
		}
	}
	
	//灯在人在，灯灭人亡
	function apply_total_damage_modifier_invincible(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(505,$pd) && check_unlocked505($pd) && skill505_check_keyitm_equiped($pd)){
			eval(import_module('logger','clubbase'));
			$pa['dmg_dealt']=0;
			$log .= \battle\battlelog_parser($pa,$pd,$active,"<span class=\"yellow b\"><:pd_name:>的技能「{$clubskillname[505]}」使<:pa_name:>的攻击没有造成任何伤害！</span><br>");
		}
		$chprocess($pa,$pd,$active);
	}
	
	//灯在人在，灯灭人亡
	function get_trap_final_damage_modifier_down(&$pa, &$pd, $tritm, $damage)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(505,$pd) && check_unlocked505($pd) && skill505_check_keyitm_equiped($pd)){
			eval(import_module('sys','logger','clubbase'));
			$log .= "<span class=\"yellow b\">你的技能「{$clubskillname[505]}」使你免疫了陷阱伤害！</span><br>";
			return 0;
		}
		
		return $chprocess($pa,$pd,$tritm,$damage);
	}
	
	function skill505_clear_corpse(&$pa){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if($pa['hp'] > 0) return;
		foreach(array('weps','arbs','arhs','aras','arfs','arts','itms0','itms1','itms2','itms3','itms4','itms5','itms6','money') as $val){
			$pa[$val] = 0;
		}
	}
	
	//如果战斗中被打爆巨大灯泡，做死亡标记
	function armor_break(&$pa, &$pd, $active, $whicharmor)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if(\skillbase\skill_query(505,$pd) && check_unlocked505($pd)){
			eval(import_module('skill505'));
			if($pd[$whicharmor] == $skill505_keyitm) {
				$pd['skill505_fatal'] = 1;
			}
		}
		$chprocess($pa, $pd, $active, $whicharmor);
		if(!empty($pd['skill505_fatal']) && skill505_check_keyitm_exists($pd)){
			$pd['skill505_fatal'] = 0;
		}
	}
	
	function attack_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('logger'));
		//灯泡被打爆以后在攻击结束时死亡
		if($pa['hp']>0 && !empty($pa['skill505_fatal'])){
			$pa['hp'] = 0;
			$pa['state']=$pa['deathmark']=44;
			skill505_clear_corpse($pa);
			$log .= \battle\battlelog_parser($pa, $pd, $active, '<span class="red b"><:pa_name:>的「太阳」被击碎了，其灵魂无法再在这个世界维持存在了！</span><br>');
		}elseif($pd['hp']>0 && !empty($pd['skill505_fatal'])){
			$pd['hp'] = 0;
			$pd['state']=$pd['deathmark']=44;
			skill505_clear_corpse($pd);
			$log .= \battle\battlelog_parser($pa, $pd, $active, '<span class="red b"><:pd_name:>的「太阳」被击碎了，其灵魂无法再在这个世界维持存在了！</span><br>');
		}
		$chprocess($pa,$pd,$active);
	}
	
	//平时操作完成后，如果灯泡在身上、地上都不存在，则死亡
	function act(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess();
		eval(import_module('sys','player'));
		if(\skillbase\skill_query(505,$sdata) && check_unlocked505($sdata) && $hp > 0){
			if(!skill505_check_keyitm_exists($sdata)) {
				$hp = 0;
				$state = 45;
				skill505_clear_corpse($sdata);
				\player\update_sdata();
				$sdata['sourceless'] = 1;
				\player\kill($sdata,$sdata);
//				\player\player_save($sdata);
//				\player\load_playerdata($sdata);
			}
		}
	}
	
	function skill505_check_keyitm_equiped(&$pa){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$equiped = 0;
		eval(import_module('skill505'));
		foreach(array('wep','arb','arh','ara','arf','art') as $pv){
			if($pa[$pv] == $skill505_keyitm){
				$equiped = 1;
				break;
			}
		}
		return $equiped;
	}
	
	function skill505_check_keyitm_exists(&$pa){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$exists = 0;
		eval(import_module('skill505'));
		foreach(array('wep','arb','arh','ara','arf','art','itm0','itm1','itm2','itm3','itm4','itm5','itm6') as $pv){
			if($pa[$pv] == $skill505_keyitm){
				$exists = 1;
				break;
			}
		}
		if(!$exists) {
			foreach($pa['searchmemory'] as $sv){
				if(isset($sv['itm']) && $sv['itm'] == $skill505_keyitm){
					$exists = 1;
					break;
				}
			}
		}
		return $exists;
	}
	
	function gemming($t1, $t2)	//宝石骑士宝石buff技能
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','skill505','logger'));
		$itm=&${$t1}; 
		if($itm == $skill505_keyitm){
			$log.='在灯泡上镶嵌宝石是会坏的！<br>';
			$mode = 'command';
			return;
		}
		$chprocess($t1, $t2);
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if(isset($exarr['dword'])) $e0 = $exarr['dword'];

		if($news == 'death44' || $news == 'death45') {
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">$a</span><span class=\"red b\">维系存在的关键道具被毁，意识消散了</span>$e0</li>";
		} else return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>