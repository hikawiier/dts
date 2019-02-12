<?php

namespace ex_cursed
{
	
	function init() 
	{
		eval(import_module('itemmain'));
		$itemspkinfo['O'] = '诅咒';
		$itemspkdesc['O']='装备以后无法更换、卸下或丢弃';
		$itemspkremark['O']='……';
	}
	
	function itemdrop_valid_check($itm, $itmk, $itme, $itms, $itmsk)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if(in_array('O',\itemmain\get_itmsk_array($itmsk))){
			eval(import_module('logger'));
			if(check_enkan()) {
				$log .= '<span class="lime b">圆环之理的光辉暂时消解了装备的诅咒。</span><br>';
			}else{
				$log .= '<span class="red b">摆脱这个装备的诅咒是不可能的。</span><br>';
				return false;
			}
		}
		return $chprocess($itm, $itmk, $itme, $itms, $itmsk);
	}
	
	function itemoff_valid_check($itm, $itmk, $itme, $itms, $itmsk)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if(in_array('O',\itemmain\get_itmsk_array($itmsk))){
			eval(import_module('logger'));
			if(check_enkan()) {
				$log .= '<span class="lime b">圆环之理的光辉暂时消解了装备的诅咒。</span><br>';
			}else{
				$log .= '<span class="red b">摆脱这个装备的诅咒是不可能的。</span><br>';
				return false;
			}
		}
		return $chprocess($itm, $itmk, $itme, $itms, $itmsk);
	}
	
	function itemuse(&$theitem)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if(strpos ( $itmk, 'W' ) === 0 || strpos ( $itmk, 'D' ) === 0 || strpos ( $itmk, 'A' ) === 0) {
			eval(import_module('player','logger'));
			if(strpos ( $itmk, 'W' ) === 0) $obj = 'wep';
			elseif(strpos ( $itmk, 'DB' ) === 0) $obj = 'arb';
			elseif(strpos ( $itmk, 'DH' ) === 0) $obj = 'arh';
			elseif(strpos ( $itmk, 'DA' ) === 0) $obj = 'ara';
			elseif(strpos ( $itmk, 'DF' ) === 0) $obj = 'arf';
			elseif(strpos ( $itmk, 'A' ) === 0) $obj = 'art';
			if(in_array('O',\itemmain\get_itmsk_array(${$obj.'sk'}))){
				if(check_enkan()) {
					$log .= '<span class="lime b">圆环之理的光辉暂时消解了装备的诅咒。</span><br>';
				}else{
					$log .= '<span class="red b">摆脱这个装备的诅咒是不可能的。</span><br>';
					return;
				}
			}
		}
		$chprocess($theitem);
	}
	
	//恶趣味，装备或者包裹里有破则的时候，诅咒暂时失效
	function check_enkan(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','itemmain','armor'));
		$flag = 0;
		foreach(array_merge( Array('wep'), array_merge($item_equip_list, $armor_equip_list)) as $v){
			if(strpos(${$v}, '概念武装『破则』')!==false){
				$flag = 1;
				break;
			}
		}
		return $flag;
	}
}


?>