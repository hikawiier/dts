<?php

namespace attrbase
{
	function init() {}
	
	//下面这两个获取属性的函数规则如下：
	//添加：请在get_ex_XXX_array_core()里使用array_push()
	//删除/改变：请在get_ex_XXX_array()里删除或者直接赋值
	//也就是说，删除的效果一定覆盖添加的效果，至于删除怎么判定再说
	
	//获取防御属性列表（全部战斗装备）
	function get_ex_def_array(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return get_ex_def_array_core($pa, $pd, $active);
	}
	
	function get_ex_def_array_core(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player'));
		$ret = Array();
		foreach ($battle_equip_list as $itm)
			foreach (\itemmain\get_itmsk_array($pd[$itm.'sk']) as $key)
				array_push($ret,$key);
				
		return $ret;
	}
	
	//获取攻击属性列表（武器防具和饰品）
	function get_ex_attack_array(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return get_ex_attack_array_core($pa, $pd, $active);
	}
	
	function get_ex_attack_array_core(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (attr_dmg_check_not_WPG($pa, $pd, $active))
			$ret = \itemmain\get_itmsk_array($pa['wepsk']);
		else $ret = array();
		
		if (defined('MOD_ARMOR'))
		{		
			eval(import_module('armor'));
			foreach ($armor_equip_list as $itm)
				foreach (\itemmain\get_itmsk_array($pa[$itm.'sk']) as $key)
					array_push($ret,$key);	
		}
		
		if (defined('MOD_ARMOR_ART'))
		{
			$ret = array_merge($ret,\itemmain\get_itmsk_array($pa['artsk']));
			//奇葩的饰品特判…… 木有办法……
			if ($pa['artk']=='Al') array_push($ret,'l');
			if ($pa['artk']=='Ag') array_push($ret,'g');
		}
		return $ret;
	}
			
	function attr_dmg_check_not_WPG(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//必须作为本系武器使用才有属性伤害（枪械当钝器没有）
		return (strpos($pa['wepk'],$pa['wep_kind'])!==false);
	}
	
	//判断两个属性代号$ssk是不是与$mark一致。其中$ssk是从具体道具属性字段里取出的，$mark是需要判定是否存在的（复合属性时，$mark应只有^字母）
	function check_itmsk_single_mark($ssk, $mark)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$flag = false;
		if($ssk === $mark) $flag = true;
		elseif(strpos($mark, '^')===0 && strpos($ssk, $mark)===0) $flag = true;
		return $flag;
	}
	
	//判定一个属性数组里是不是有给定的属性代号
	//如果是复合属性或者要统计总数，会返回数值。所以判定属性不存在请用false===
	//如果$count==1，则会统计属性总数；复合属性则是计算这一属性数值的总和
	//代码里存在大量的in_array()，如果不涉及复合属性的判断，实际上是等价的，可以不改
	function check_in_itmsk($mark, $skarr, $count = 0)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$flag = false;
		if(in_array($mark, $skarr)) {
			if(!$count) $flag = true;
			else {
				$flag = 0;
				foreach($skarr as $v){
					if($v === $mark) $flag ++;
				}
			}
		} elseif(strpos($mark, '^')===0) {
			//判定是不是合法的复合属性
			$compret = \itemmain\get_comp_itmsk_info($mark);
			if(NULL !== $compret) {
				list($skk, $null) = $compret;
				foreach($skarr as $v) {
					if (check_itmsk_single_mark($v, $skk)) {
						//$flag = true;
						list($null, $skn) = \itemmain\get_comp_itmsk_info($v);
						if(!$flag) $flag = $skn;
						else $flag += $skn;
						if(!$count) break;
					}
				}
			}
		}
		return $flag;
	}
	
	//检查$pa是否具有$nm属性，如$pa为NULL则检查当前玩家
	//警告：本函数不供战斗使用！！！！！本函数只应当被用来检查非战斗相关属性！！！
	function check_itmsk($nm, &$pa = NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','logger'));
		if ($pa == NULL)
		{
			foreach ($battle_equip_list as $itm)
				foreach (\itemmain\get_itmsk_array(${$itm.'sk'}) as $key)
					if (check_itmsk_single_mark($key, $nm))
						return 1;
			return 0;
		}
		else
		{
			foreach ($battle_equip_list as $itm)
				foreach (\itemmain\get_itmsk_array($pa[$itm.'sk']) as $key)
					if (check_itmsk_single_mark($key, $nm))
						return 1;
			return 0;
		}
	}
}

?>
