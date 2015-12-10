<?php

namespace armor
{
	function init()
	{
		eval(import_module('player','itemmain'));
		global $armor_equip_list, $armor_iteminfo;
		$equip_list=array_merge($equip_list,$armor_equip_list);
		$battle_equip_list=array_merge($battle_equip_list,$armor_equip_list);
		$iteminfo+=$armor_iteminfo;
	}
	
	function get_external_def_multiplier(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1.0;
	}
	
	function get_external_def(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('armor'));
		$sum = 0;
		foreach($armor_equip_list as $key) 
			if(strpos($pd[$key.'k'],'P')===false || $pd[$key.'s']!==$nosta)
				$sum+=$pd[$key.'e'];
		return $sum;
	}
	
	function get_def(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$pd['external_def'] = get_external_def($pa,$pd,$active)*get_external_def_multiplier($pa,$pd,$active);
		return $chprocess($pa, $pd, $active)+$pd['external_def'];
	}
	
	function armor_break(&$pa, &$pd, $active, $whicharmor)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('logger'));
		if ($active)
		{
			$log .= "{$pd['name']}的<span class=\"red\">".$pd[$whicharmor]."</span>受损过重，无法再装备了！<br>";
			$pd['armorbreaklog'] .= "你的<span class=\"red\">".$pd[$whicharmor]."</span>受损过重，无法再装备了！<br>";
		}
		else  $log .= "你的<span class=\"red\">".$pd[$whicharmor]."</span>受损过重，无法再装备了！<br>";
		
		$pd[$whicharmor] = ''; $pd[$whicharmor.'e'] = 0; $pd[$whicharmor.'s'] = 0; $pd[$whicharmor.'sk'] = '';
						
		if ($whicharmor=='arb')
		{
			eval(import_module('armor'));
			$pd['arb'] = $noarb; $pd['arbs'] = $nosta;$pd['arbk']="DN";
		}
	}
	
	//防具受损
	//返回受损了多少耐久
	function armor_hurt(&$pa, &$pd, $active, $which, $hurtvalue)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('armor','wound','logger'));
		if ((in_array($which,$armor_equip_list) && isset($pd[$which.'e']) && $pd[$which.'e']>0) && (strpos($pd[$which.'k'],'P')===false || $pd[$which.'s']!==$nosta))	//有防具 装甲耐久不为∞
		{
			if ($pd[$which.'s'] == $nosta && strpos($pd[$which.'k'],'P')===false)	//无限耐久防具可以抵挡一次任意损耗的攻击
			{
				$pd[$which.'s'] = $hurtvalue;
			}
			$x = min($pd[$which.'s'], $hurtvalue);
			$pd[$which.'s'] -= $x;
			if(strpos($pd[$which.'k'],'P')!==false)
				$hurt_info='能量储备';
			else  $hurt_info = '耐久度';
			if ($active)
			{
				$log .= "{$pd['name']}的".$pd[$which]."{$hurt_info}下降了{$x}！<br>";
			}
			else
			{
				$log .= "你的".$pd[$which]."的{$hurt_info}下降了{$x}！<br>";
			}
					
			if ($pd[$which.'s']<=0) 
			{
				if(strpos($pd[$which.'k'],'P')!==false)
				{
					$pd[$which.'s'] = $nosta;
					if ($active)
						$log .= "{$pd['name']}的{$pd[$which]}能量耗尽了！<br>";
					else  $log .= "你的{$pd[$which]}能量耗尽了！<br>";
				}
				else
				{
					armor_break($pa, $pd, $active, $which);
				}
			}			
			return $x;
		}
		else  return 0;
	}
	
	function apply_weapon_inf(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('armor','wound','logger'));
		for ($i=0; $i<strlen($inf_place); $i++)
			if (isset($pa['attack_wounded_'.$inf_place[$i]]) && $pa['attack_wounded_'.$inf_place[$i]]>0)
			{
				$pa['attack_wounded_'.$inf_place[$i]] -= armor_hurt($pa, $pd, $active, 'ar'.$inf_place[$i], $pa['attack_wounded_'.$inf_place[$i]]);
			}
		
		$chprocess($pa, $pd, $active);
	}
	
	function itemuse(&$theitem) 
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','itemmain','logger'));
		
		$itm=&$theitem['itm']; $itmk=&$theitem['itmk'];
		$itme=&$theitem['itme']; $itms=&$theitem['itms']; $itmsk=&$theitem['itmsk'];
		
		if (strpos ( $itmk, 'D' ) === 0)
		{
			if(strpos ( $itmk, 'DB' ) === 0) {
				$eqp = 'arb';
				$noeqp = 'DN';
			}elseif(strpos ( $itmk, 'DH' ) === 0) {
				$eqp = 'arh';
				$noeqp = '';
			}elseif(strpos ( $itmk, 'DA' ) === 0) {
				$eqp = 'ara';
				$noeqp = '';
			}elseif(strpos ( $itmk, 'DF' ) === 0) {
				$eqp = 'arf';
				$noeqp = '';
			}
			if (($noeqp && strpos ( ${$eqp.'k'}, $noeqp ) === 0) || ! ${$eqp.'s'}) {
				${$eqp} = $itm;
				${$eqp.'k'} = $itmk;
				${$eqp.'e'} = $itme;
				${$eqp.'s'} = $itms;
				${$eqp.'sk'} = $itmsk;
				$log .= "装备了<span class=\"yellow\">$itm</span>。<br>";
				$itm = $itmk = $itmsk = '';
				$itme = $itms = 0;
			} else {
				swap(${$eqp},$itm);
				swap(${$eqp.'k'},$itmk);
				swap(${$eqp.'e'},$itme);
				swap(${$eqp.'s'},$itms);
				swap(${$eqp.'sk'},$itmsk);
				$log .= "卸下了<span class=\"red\">$itm</span>，装备了<span class=\"yellow\">${$eqp}</span>。<br>";
			}
			return;
		}
		$chprocess($theitem);
	}
	
	function assault_prepare(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($active) $pd['armorbreaklog']=''; else $pa['armorbreaklog']='';
		$chprocess($pa, $pd, $active);
	}
	
	function assault_finish(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($active) 
			$pd['battlelog'].=$pd['armorbreaklog'];
		else  $pa['battlelog'].=$pa['armorbreaklog'];
		$chprocess($pa, $pd, $active);
	}
}

?>
